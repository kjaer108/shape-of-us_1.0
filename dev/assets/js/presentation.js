(function(){
  // --- URL param helpers ---
  function getIntParam(name, def){
    try {
      var sp = new URLSearchParams(window.location.search || '');
      var v = sp.get(name);
      if (v === null || v === '') return def;
      var n = parseInt(v, 10);
      return (isNaN(n) ? def : n);
    } catch(_) { return def; }
  }

  // Map speed 1..5 to a time multiplier. Defaults to 3 => 1x.
  // Requirements: 1 => 0.5x (twice as fast), 3 => 1x (today), 5 => 2x (twice as slow)
  // We also accept 0 for photo_speed meaning no pause between swaps.
  function speedToFactor(n){
    if (n <= 1) return 0.5;
    if (n === 2) return 0.75;
    if (n === 3) return 1.0;
    if (n === 4) return 1.5;
    return 2.0; // n >= 5
  }

  function parseCaptions() {
    var dataEl = document.getElementById('captions-data');
    if (!dataEl) return [];
    try {
      var json = dataEl.textContent || dataEl.innerText || '[]';
      var arr = JSON.parse(json);
      if (!Array.isArray(arr)) return [];
      return arr.map(function(item){
        return {
          text: (item && typeof item.text === 'string') ? item.text : '',
          display_time: Math.max(500, parseInt(item && item.display_time, 10) || 3000)
        };
      }).filter(function(it){ return it.text.length > 0; });
    } catch(e) {
      console.warn('Invalid captions JSON', e);
      return [];
    }
  }

  function parseRotateOrder() {
    var el = document.getElementById('image-rotate');
    if (!el) return null;
    try {
      var json = el.textContent || el.innerText || '[]';
      var arr = JSON.parse(json);
      return Array.isArray(arr) ? arr : null;
    } catch(e) {
      console.warn('Invalid image-rotate JSON', e);
      return null;
    }
  }

  function startTwoSlotRotation(captions, container, textFactor) {
    var slots = container.querySelectorAll('.msg');
    if (!slots || slots.length < 2) return;

    // Scale display_time by textFactor (defaults to 1)
    var factor = (typeof textFactor === 'number' && isFinite(textFactor) && textFactor > 0) ? textFactor : 1;
    captions = captions.map(function(c){
      return { text: c.text, display_time: Math.max(300, Math.round(c.display_time * factor)) };
    });

    var i = 0;            // index for next message to show
    var active = 0;       // which slot is currently active (0 or 1)
    var timer = null;

    // seed first message into the active slot
    slots[active].textContent = captions[0].text;

    function scheduleNext(delay) {
      timer = setTimeout(step, delay);
    }

    function step() {
      // compute next slot and next message index
      var nextSlot = 1 - active;
      i = (i + 1) % captions.length;
      var nextMsg = captions[i];

      // put next text into the hidden slot
      slots[nextSlot].textContent = nextMsg.text;

      // crossfade via class toggle
      slots[active].classList.remove('is-active');
      slots[nextSlot].classList.add('is-active');

      // flip active
      active = nextSlot;

      // Since caption length can change number of lines, recompute viewport fit
      try { if (typeof recomputeTvScale === 'function') requestAnimationFrame(recomputeTvScale); } catch(_) {}

      // schedule according to the message we just switched TO
      scheduleNext(nextMsg.display_time);
    }

    // If there is only one caption, we seeded it above and stop here
    if (captions.length === 1) return;

    // Start after the first message's display_time
    scheduleNext(captions[0].display_time);

    // Pause/resume on visibility change to avoid drift
    document.addEventListener('visibilitychange', function(){
      if (document.hidden) {
        if (timer) clearTimeout(timer);
      } else {
        // resume from current active message timing
        var text = slots[active].textContent;
        var idx = captions.findIndex(function(c){ return c.text === text; });
        var delay = (idx >= 0 ? captions[idx].display_time : 3000);
        scheduleNext(delay);
      }
    });

    // Clean up on page hide (bfcache)
    window.addEventListener('pagehide', function(){
      if (timer) clearTimeout(timer);
    });
  }

  // -----------------------------
  // Crossfade helper for swapping an <img> src without flash
  // -----------------------------
  function crossfadeSwap(targetImg, newUrl, duration) {
    return new Promise(function(resolve){
      try {
        if (!targetImg || !newUrl) { resolve(); return; }
        if (targetImg.__fading) { resolve(); return; } // prevent concurrent fades on same element
        targetImg.__fading = true;

        var parent = targetImg.closest ? targetImg.closest('.ratio') : targetImg.parentNode;
        if (!parent) parent = targetImg.parentNode;

        // Create overlay image positioned on top
        var overlay = document.createElement('img');
        overlay.className = 'fade-overlay';
        overlay.alt = '';
        overlay.decoding = 'async';
        overlay.src = newUrl; // already preloaded by caller

        // Allow custom duration
        var ms = Math.max(100, parseInt(duration, 10) || 500);
        overlay.style.transition = 'opacity ' + ms + 'ms ease';
        overlay.style.opacity = '0';

        parent.appendChild(overlay);

        // Force reflow then fade in
        // eslint-disable-next-line no-unused-expressions
        overlay.offsetHeight;
        overlay.style.opacity = '1';

        function cleanupAndResolve(){
          setTimeout(function(){
            if (overlay && overlay.parentNode) overlay.parentNode.removeChild(overlay);
            targetImg.__fading = false;
            resolve();
          }, 180);
        }

        function done() {
          overlay.removeEventListener('transitionend', done);
          // Swap the underlying image src now that overlay is fully visible
          try { targetImg.src = newUrl; } catch(e) {}
          // Fade overlay out quickly to smooth any tiny mismatch, then remove
          overlay.style.transition = 'opacity 150ms ease';
          overlay.style.opacity = '0';
          cleanupAndResolve();
        }

        overlay.addEventListener('transitionend', done);
      } catch (e) {
        // Fallback to direct swap on any unexpected error
        try { targetImg.src = newUrl; } catch(_) {}
        targetImg.__fading = false;
        resolve();
      }
    });
  }

  // -----------------------------
  // Presentation grid image swap
  // -----------------------------
  function startRandomImageReload(photoFactor, noPause, rotateOrder) {
    var grid = document.querySelector('.tv-grid');
    if (!grid) return; // no grid on this page

    var imgs = Array.prototype.slice.call(grid.querySelectorAll('img'));
    if (!imgs.length) return;

    // Build deterministic rotation order based on PHP-provided $image_rotate (1..12, TL->BR)
    var order = Array.isArray(rotateOrder) ? rotateOrder.slice() : [];
    order = order
      .map(function(n){ return parseInt(n, 10); })
      .filter(function(n){ return !isNaN(n) && n >= 1; })
      .map(function(n){ return n - 1; }) // to 0-based indices
      .filter(function(idx){ return idx >= 0 && idx < imgs.length; });
    if (!order.length) {
      // Fallback to linear order over available images
      order = imgs.map(function(_, i){ return i; });
    }
    var rotateIdx = 0; // pointer into order

    var queue = [];
    var fetching = false;

    function fetchMore(limit) {
      if (fetching) return Promise.resolve([]);
      fetching = true;
      var body = 'limit=' + encodeURIComponent(limit || 50);
      return fetch('src/xhr-presentation.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: body,
        credentials: 'same-origin'
      }).then(function(res){
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
      }).then(function(list){
        // list is [{id, url}, ...]
        if (Array.isArray(list)) {
          queue = queue.concat(list);
        }
      }).catch(function(err){
        console.warn('Could not fetch images for rotation', err);
      }).finally(function(){
        fetching = false;
      });
    }

    function ensureQueue() {
      if (queue.length < 10) {
        fetchMore(50);
      }
    }

    function preload(url) {
      return new Promise(function(resolve, reject){
        var im = new Image();
        im.onload = function(){ resolve(url); };
        im.onerror = reject;
        // Avoid interfering with layout; just warm cache
        im.decoding = 'async';
        im.src = url;
      });
    }

    var timer = null;
    var busy = false; // prevent concurrent ticks
    var base = 2000; // baseline from today
    var interval = Math.max(0, Math.round(base * (photoFactor && photoFactor > 0 ? photoFactor : 1)));

    function clearTimer(){ if (timer) { clearTimeout(timer); timer = null; } }
    function scheduleNext(){ if (!timer && !document.hidden) { timer = setTimeout(tick, interval); } }

    function tick() {
      // mark the previous timeout as consumed so we can schedule the next one
      clearTimer();
      if (busy) return; // already processing
      busy = true;

      ensureQueue();
      if (!queue.length) {
        busy = false;
        if (!noPause) scheduleNext();
        else if (!document.hidden) { timer = setTimeout(tick, 50); } // small retry when queue fills
        return;
      }

      var next = queue.shift();
      var targetIdx = order[rotateIdx]; // follow deterministic order
      rotateIdx = (rotateIdx + 1) % order.length; // advance with wraparound
      var target = imgs[targetIdx];

      // Preload then crossfade swap to avoid visible blanks/flash
      preload(next.url)
        .then(function(){
          return crossfadeSwap(target, next.url, 600);
        })
        .catch(function(){
          // ignore failed image, continue chain
        })
        .finally(function(){
          busy = false;
          if (document.hidden) { clearTimer(); return; }
          if (noPause) {
            // schedule immediately after the swap completes
            timer = setTimeout(tick, 0);
          } else {
            scheduleNext();
          }
        });
    }

    // Prime the queue and start the loop
    fetchMore(50).then(function(){
      if (noPause) {
        tick();
      } else {
        scheduleNext();
      }
    });

    // Pause on background to save resources
    document.addEventListener('visibilitychange', function(){
      if (document.hidden) {
        clearTimer();
      } else {
        if (noPause) {
          tick();
        } else {
          scheduleNext();
        }
      }
    });

    window.addEventListener('pagehide', function(){
      clearTimer();
    });
  }

  function init() {
    // Read URL params
    var textSpeed = getIntParam('text_speed', 3); // 1..5 (3 is default)
    var photoSpeed = getIntParam('photo_speed', 3); // 0 or 1..5 (3 is default)

    var textFactor = speedToFactor(Math.max(1, Math.min(5, textSpeed)));
    var noPause = (photoSpeed === 0);
    var photoFactor = noPause ? 1 : speedToFactor(Math.max(1, Math.min(5, photoSpeed)));

    var container = document.getElementById('caption-rotator');
    if (container) {
      var captions = parseCaptions();
      if (captions.length) {
        startTwoSlotRotation(captions, container, textFactor);
      }
    }

    // Start swapping photos with configured speed, following the server-provided rotate order if present
    var rotateOrder = parseRotateOrder();
    startRandomImageReload(photoFactor, noPause, rotateOrder);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
// --- Viewport fit safeguard: downscale the whole presentation if it would overflow ---
function debounce(fn, wait){
  var t; return function(){
    var ctx=this, args=arguments; clearTimeout(t); t=setTimeout(function(){ fn.apply(ctx,args); }, wait||100);
  };
}

function computeAndApplyTvScale(){
  try {
    var root = document.body;
    if (!root || !root.classList || !root.classList.contains('presentation-tv')) return;
    var wrapper = document.querySelector('main.content-wrapper');
    if (!wrapper) return;

    // Reset to natural size to measure
    root.style.setProperty('--tv-scale','1');

    requestAnimationFrame(function(){
      // Use scrollHeight to account for intrinsic (unscaled) content height
      var naturalHeight = wrapper.scrollHeight;
      var vh = window.innerHeight || document.documentElement.clientHeight || 1080;
      if (!naturalHeight) return;

      var scaleY = vh / naturalHeight;
      // Keep within sensible bounds to avoid too tiny UI on extreme cases
      var minScale = 0.6; // configurable
      var scale = Math.max(Math.min(scaleY, 1), minScale);

      root.style.setProperty('--tv-scale', String(scale));
    });
  } catch(e) {
    // ignore
  }
}

var recomputeTvScale = debounce(computeAndApplyTvScale, 120);

window.addEventListener('resize', recomputeTvScale);
window.addEventListener('orientationchange', recomputeTvScale);
window.addEventListener('load', recomputeTvScale);
