<?php
// TODO: Do not translate source language. Ex "Vis steder" => "Fiskesteder"

//@include_once __DIR__ . '/../fx/fx-email.php';

class translate
{
    private $apiKey;
    private $endpoint;
    private $useCache;
    private $pdo;
    private $pageId;        // nullable
    private $targetLang;    // e.g., "DA", "EN"
    private $hashIndex = []; // in-memory cache of hash => translated text
    private $curlHandle;
    private $debug = false;

    /**
     * @param string|null $targetLang   Target language (e.g., 'da', 'en'). If null, passthrough.
     * @param int|null    $pageId       Optional page context. Can be NULL.
     * @param bool        $useCache     Whether to use DB + in-memory cache.
     */
    public function __construct($targetLang = null, $pageId = null, $useCache = true)
    {
        global $config, $pdo;

        $this->apiKey     = $config["deepL"]["apiKey"];
        $this->endpoint   = rtrim($config["deepL"]["endpoint"], '/') . '/';
        $this->useCache   = $useCache;
        $this->pdo        = $pdo;
        $this->pageId     = $pageId;
        $this->targetLang = $targetLang ? strtoupper($targetLang) : null;

        if ($this->debug) {
            zdebug("DeepL API Initialized; lang={$this->targetLang}; pageId=" . var_export($this->pageId, true));
        }
    }

    /**
     * Translate a string using cache -> cross-page -> DeepL.
     *
     * @param string $text
     * @param bool   $isStatic  If true, marks cache row as 'static' (informational only)
     * @return string
     */
    public function gettext($text, $isStatic = true)
    {
        if ($this->targetLang === null) {
            return $text;
        }

        $normalizedText = trim($text);
        if ($normalizedText === '') {
            return $text; // nothing to translate
        }

        $hash = hash('sha256', $normalizedText);

        // 1) In-memory cache for this page+lang
        if ($this->useCache) {
            $this->loadInMemoryCache(); // NULL-safe
            if (isset($this->hashIndex[$hash])) {
                if ($this->debug) zdebug("sou_gettext_cache HIT (in-memory): {$hash}");
                return $this->hashIndex[$hash];
            }
        }

        // 2) Cross-page lookup (same lang, different pageId handling is NULL-safe)
        $translatedText = $this->fetchFromOtherPages($hash);
        if ($translatedText !== null) {
            if ($this->debug) zdebug("sou_gettext_cache HIT (cross-page): {$hash}");
            if ($this->useCache) {
                $this->storeInCache($normalizedText, $translatedText, $isStatic);
            }
            return $translatedText;
        }

        // 3) DeepL call
        $translatedText = $this->fetchFromDeepL($normalizedText);

        if ($this->useCache) {
            $this->storeInCache($normalizedText, $translatedText, $isStatic);
        }

        // 4) Log DeepL API call
        $sql = "INSERT INTO `".LOG_DB."`.`deepl_log`
                    (`hash`, `pageId`, `language`, `normalizedText`, `translatedText`, `timestamp`)
                VALUES (:hash, :pageId, :language, :normalizedText, :translatedText, NOW())";
        $params = [
            ':hash'            => $hash,
            ':pageId'          => $this->pageId,
            ':language'        => $this->targetLang,
            ':normalizedText'  => $normalizedText,
            ':translatedText'  => $translatedText
        ];
        pdo_execute($this->pdo, $sql, $params);

        return $translatedText;
    }

    /**
     * Try to reuse a translation from other pages with same language.
     * NULL-safe logic for pageId.
     */
    private function fetchFromOtherPages($hash)
    {
        $sql = "SELECT `text`
                FROM `sou_gettext_cache`
                WHERE `hash` = :hash
                  AND `language` = :language
                  AND (
                        (:pageId IS NULL AND `pageId` IS NOT NULL)
                     OR (:pageId IS NOT NULL AND `pageId` <> :pageId)
                  )
                LIMIT 1";
        $params = [
            ':hash'     => $hash,
            ':language' => $this->targetLang,
            ':pageId'   => $this->pageId
        ];
        return pdo_get_col($this->pdo, $sql, $params);
    }

    /**
     * Whether to send an error email (throttled).
     */
    private function shouldSendErrorMail(string $type, $errMsg = null): bool
    {
        global $pdo;

        $sql = "SELECT `last_sent`
                FROM `".LOG_DB."`.`system_error_throttle`
                WHERE `error_type` = :type";
        $row = pdo_get_row($pdo, $sql, [':type' => $type]);

        $now = new DateTime();
        $threshold = new DateTime('-30 minutes');

        if ($row && new DateTime($row['last_sent']) > $threshold) {
            return false;
        }

        $updateSql = $row
            ? "UPDATE `".LOG_DB."`.`system_error_throttle`
               SET `text` = :errmsg, `last_sent` = :now
               WHERE `error_type` = :type"
            : "INSERT INTO `".LOG_DB."`.`system_error_throttle`
               (`error_type`, `text`, `last_sent`)
               VALUES (:type, :errmsg, :now)";

        pdo_execute($pdo, $updateSql, [
            ':type'   => $type,
            ':errmsg' => $errMsg ?? 'No error message provided',
            ':now'    => $now->format('Y-m-d H:i:s')
        ]);

        return true;
    }

    /**
     * Call DeepL Translate v2
     */
    private function fetchFromDeepL($text)
    {
        try {
            $data = [
                'text'               => $text,                 // single text (we encode properly)
                'target_lang'        => $this->targetLang,
                'tag_handling'       => 'html',
                'outline_detection'  => '1',
                'model_type'         => 'quality_optimized'    // highest translation quality
            ];

            $result = $this->makeCurl($this->endpoint . 'v2/translate', $data);

            $translated = $result['translations'][0]['text'] ?? null;
            if (!is_string($translated) || $translated === '') {
                return $text; // fallback
            }
            return $translated;

        } catch (Exception $e) {
            $errorType  = (strpos($e->getMessage(), 'Quota Exceeded') !== false) ? 'quota' : 'generic';
            $shouldSend = $this->shouldSendErrorMail($errorType, $e->getMessage());

            if ($shouldSend) {
                if ($errorType === 'quota') {
                    $html = "<p><strong>DeepL API quota exceeded</strong></p><pre>" .
                        htmlspecialchars($e->getMessage()) . "</pre>";
                    send_mail("DeepL Quota Exceeded", $html, "thomas@zandora.net");
                } else {
                    $html = "<p><strong>Unexpected DeepL API error</strong></p>" .
                        "<p><strong>Message:</strong></p><pre>" .
                        htmlspecialchars($e->getMessage()) . "</pre>" .
                        "<p><strong>Trace:</strong></p><pre>" .
                        htmlspecialchars($e->getTraceAsString()) . "</pre>";
                    send_mail("DeepL API Error", $html, "thomas@zandora.net");
                }
            }

            return $text; // graceful fallback
        }
    }

    /**
     * Reusable cURL handle (application/x-www-form-urlencoded)
     */
    private function makeCurl($url, $data)
    {
        if (!$this->curlHandle) {
            $this->curlHandle = curl_init();
            curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curlHandle, CURLOPT_POST, true);
            curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, [
                'Authorization: DeepL-Auth-Key ' . $this->apiKey,
                'Content-Type: application/x-www-form-urlencoded'
            ]);
        }

        // DeepL expects key=...&text=...; ensure RFC3986 encoding
        $postData = http_build_query($data, '', '&', PHP_QUERY_RFC3986);
        curl_setopt($this->curlHandle, CURLOPT_URL, $url);
        curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($this->curlHandle);
        $httpCode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($this->curlHandle);
            curl_close($this->curlHandle);
            $this->curlHandle = null;
            throw new Exception('cURL Error: ' . $error);
        }

        $result = json_decode($response, true);

        if ($httpCode !== 200 || !isset($result['translations'])) {
            $errorMessage = isset($result['message']) ? $result['message'] : 'Unknown error';
            throw new Exception('DeepL API Error: ' . $errorMessage);
        }

        return $result;
    }

    /**
     * UPSERT into sou_gettext_cache (idempotent; handles NULL pageId).
     * Requires UNIQUE KEY on (language, pageId, hash).
     */
    private function storeInCache($sourceText, $translatedText, $isStatic)
    {
        $hash = hash('sha256', $sourceText);
        $type = $isStatic ? 'static' : 'dynamic';

        $sql = "INSERT INTO `sou_gettext_cache`
                    (`hash`, `language`, `pageId`, `source`, `text`, `type`)
                VALUES
                    (:hash, :language, :pageId, :source, :text, :type)
                ON DUPLICATE KEY UPDATE
                    `text` = VALUES(`text`),
                    `type` = VALUES(`type`),
                    `source` = VALUES(`source`)";
        $params = [
            ':hash'     => $hash,
            ':language' => $this->targetLang,
            ':pageId'   => $this->pageId,
            ':source'   => $sourceText,
            ':text'     => $translatedText,
            ':type'     => $type
        ];
        pdo_execute($this->pdo, $sql, $params);

        // Keep in-memory cache warm
        $this->hashIndex[$hash] = $translatedText;
    }

    /**
     * Load page+lang scoped translations into the in-memory hashIndex.
     * NULL-safe for pageId.
     */
    private function loadInMemoryCache()
    {
        if (!empty($this->hashIndex)) return; // Already loaded

        $sql = "SELECT `hash`, `text`
                FROM `sou_gettext_cache`
                WHERE `language` = :targetLang
                  AND (
                        (:pageId IS NULL AND `pageId` IS NULL)
                     OR (:pageId IS NOT NULL AND `pageId` = :pageId)
                  )";
        $params = [
            ':targetLang' => $this->targetLang,
            ':pageId'     => $this->pageId
        ];

        $rows = pdo_get_array($this->pdo, $sql, $params);
        foreach ($rows as $row) {
            $this->hashIndex[$row['hash']] = $row['text'];
        }
    }
}
