// Lightweight lazy-loading enhancer for images across the site
// - Adds `loading="lazy"` to non-critical images
// - Leaves header logos or elements marked with `data-critical="true"` untouched
// - Optional: supports elements with `data-bg-src` for lazy background images

(function(){
  try {
    var onReady = function(fn){
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fn);
      } else { fn(); }
    };

    onReady(function(){
      var imgs = Array.prototype.slice.call(document.getElementsByTagName('img'));
      imgs.forEach(function(img){
        var isCritical = img.dataset && img.dataset.critical === 'true';
        var isLogo = img.classList && img.classList.contains('logo-img');
        if (isCritical || isLogo) { return; }
        if (!img.hasAttribute('loading')) {
          img.setAttribute('loading', 'lazy');
        }
      });

      // Lazy-load background images if authors mark them with data-bg-src
      var bgTargets = document.querySelectorAll('[data-bg-src]');
      if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries){
          entries.forEach(function(entry){
            if (entry.isIntersecting) {
              var el = entry.target;
              var src = el.getAttribute('data-bg-src');
              if (src) {
                el.style.backgroundImage = 'url("' + src + '")';
                el.removeAttribute('data-bg-src');
              }
              observer.unobserve(el);
            }
          });
        }, { root: null, rootMargin: '200px', threshold: 0 });
        bgTargets.forEach(function(el){ observer.observe(el); });
      } else {
        // Fallback: load immediately
        bgTargets.forEach(function(el){
          var src = el.getAttribute('data-bg-src');
          if (src) {
            el.style.backgroundImage = 'url("' + src + '")';
            el.removeAttribute('data-bg-src');
          }
        });
      }
    });
  } catch (e) {
    // Silently ignore to avoid breaking pages
  }
})();

