(function(){
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

  function startTwoSlotRotation(captions, container) {
    var slots = container.querySelectorAll('.msg');
    if (!slots || slots.length < 2) return;

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
    try {
      if (!targetImg || !newUrl) return;
      if (targetImg.__fading) return; // prevent concurrent fades on same element
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

      function done() {
        overlay.removeEventListener('transitionend', done);
        // Swap the underlying image src now that overlay is fully visible
        try { targetImg.src = newUrl; } catch(e) {}
        // Fade overlay out quickly to smooth any tiny mismatch, then remove
        overlay.style.transition = 'opacity 150ms ease';
        overlay.style.opacity = '0';
        setTimeout(function(){
          if (overlay && overlay.parentNode) overlay.parentNode.removeChild(overlay);
          targetImg.__fading = false;
        }, 180);
      }

      overlay.addEventListener('transitionend', done);
    } catch (e) {
      // Fallback to direct swap on any unexpected error
      try { targetImg.src = newUrl; } catch(_) {}
      targetImg.__fading = false;
    }
  }

  // -----------------------------
  // Presentation grid image swap
  // -----------------------------
  function startRandomImageReload() {
    var grid = document.querySelector('.tv-grid');
    if (!grid) return; // no grid on this page

    var imgs = Array.prototype.slice.call(grid.querySelectorAll('img'));
    if (!imgs.length) return;

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

    function tick() {
      ensureQueue();
      if (!queue.length) return; // nothing ready yet

      var next = queue.shift();
      var targetIdx = Math.floor(Math.random() * imgs.length); // 0..N-1
      var target = imgs[targetIdx];

      // Preload then crossfade swap to avoid visible blanks/flash
      preload(next.url).then(function(){
        crossfadeSwap(target, next.url, 600);
      }).catch(function(){
        // ignore failed image, try again next tick
      });
    }

    // Prime the queue and start the interval
    fetchMore(50).then(function(){
      timer = setInterval(tick, 2000); // every 2 seconds
    });

    // Pause on background to save resources
    document.addEventListener('visibilitychange', function(){
      if (document.hidden) {
        if (timer) { clearInterval(timer); timer = null; }
      } else {
        if (!timer) timer = setInterval(tick, 2000);
      }
    });

    window.addEventListener('pagehide', function(){
      if (timer) clearInterval(timer);
    });
  }

  function init() {
    var container = document.getElementById('caption-rotator');
    if (container) {
      var captions = parseCaptions();
      if (captions.length) {
        startTwoSlotRotation(captions, container);
      }
    }

    // Start swapping a random photo every 2 seconds
    startRandomImageReload();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();