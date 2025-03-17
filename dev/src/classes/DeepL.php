<?php
class DeepL {
    private $apiKey;
    private $endpoint;
    private $useCache;
    private $pdo;
    private $targetLang;

    //print_performance
    private $pp;

    public function __construct($targetLang=null ,$pp = false, $useCache = true) {
        global $config, $pdo;

        $this->apiKey = $config["deepL"]["apiKey"];
        $this->endpoint = $config["deepL"]["endpoint"];
        $this->useCache = $useCache;
        $this->pdo = $pdo;
        $this->pp = $pp;
        $this->targetLang = strtoupper($targetLang);
    }


    public function gettext($text, $sourceLang = null) {
        if($this->pp){$start = microtime(true);}

        if($sourceLang !== null){
            $sourceLang = strtoupper($sourceLang);
        }
        if($this->targetLang == null ||
            $this->targetLang == $sourceLang){return $text;}

        $isArray = is_array($text);
        $textToTranslate = $isArray ? $text : [$text];

        //---CACHE---//
        if ($this->useCache) {
            $results = $this->checkCache($textToTranslate, $this->targetLang);
            if (count($results) === count($textToTranslate)) {
                if($this->pp){$end = microtime(true);$executionTime = $end - $start;echo "<br>Execution time with cache: {$executionTime} seconds <br>";}
                return $isArray ? $results : $results[0];
            }

            // Filter out texts that were found in cache
            $notFoundTranslates = [];
            foreach ($textToTranslate as $index => $t) {
                if (!isset($results[$index])) {
                    $notFoundTranslates[] = $t;
                }
            }
            $textToTranslate = $notFoundTranslates;
        }

        // If all texts were found in cache, don't need to call API
        if (empty($textToTranslate)) {
            if($this->pp){$end = microtime(true);$executionTime = $end - $start;echo "<br>Execution time with cache: {$executionTime} seconds <br>";}
            return $isArray ? $results : $results[0];
        }


        //---API REQUEST---//
        $url = $this->endpoint . 'v2/translate';
        $data = [
            'text' => $textToTranslate,
            'target_lang' => strtoupper($this->targetLang)
        ];

        if ($sourceLang) {
            $data['source_lang'] = strtoupper($sourceLang);
        }

        $result = $this->makeCurl($url, $data);

        if(empty($result)) {
            return $text;
        }

        // Extract translations
        $translations = [];
        foreach ($result['translations'] as $index => $translation) {
            $translations[$index] = $translation['text'];

            // Store in cache if enabled
            if ($this->useCache) {
                $this->storeInCache($textToTranslate[$index], $translation['text'], $this->targetLang);
            }
        }

        // Merge with cached results if needed
        if (isset($results) && !empty($results)) {
            $translations = $results + $translations;
            ksort($translations);
        }

        if($this->pp){$end = microtime(true);$executionTime = $end - $start;echo "<br>Execution time: {$executionTime} seconds <br>";}
        return $isArray ? $translations : $translations[0];
    }


    private function makeCurl($url, $data){
        // Make API request
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: DeepL-Auth-Key ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for cURL errors
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('cURL Error: ' . $error);
        }

        curl_close($ch);

        // Parse response
        $result = json_decode($response, true);

        // Check for API errors
        if ($httpCode !== 200 || !isset($result['translations'])) {
            $errorMessage = isset($result['message']) ? $result['message'] : 'Unknown error';
            throw new Exception('DeepL API Error: ' . $errorMessage);
            return null;
        }

        return $result;
    }


    private function checkCache($texts, $targetLang) {
        $results = [];

        foreach ($texts as $index => $text) {
            $sql = "SELECT text FROM translate_cache WHERE hash=:hash and language=:language ";
            $params = array(
                ':hash' =>  hash('sha256', $text),
                ':language' => strtoupper($targetLang)
            );
            $result = pdo_get_array($this->pdo, $sql, $params);

            if(!empty($result)) {
                if ($result[0]) {
                    $results[$index] = $result[0]['text'];
                }
            }

        }

        return $results;
    }


    private function storeInCache($sourceText, $translatedText, $targetLang) {

        $sql = "INSERT INTO `translate_cache` (`hash`, `language`, `source`, `text`) VALUES (:hash, :language, :source, :text)";
        $params = array(
            ":hash"=>  hash('sha256', $sourceText),
            ":language"=> $targetLang,
            ":source"=> mb_substr($sourceText, 0 ,200),
            ":text"=> $translatedText
        );
        $res1 = pdo_get_array($this->pdo, $sql, $params);

    }

}