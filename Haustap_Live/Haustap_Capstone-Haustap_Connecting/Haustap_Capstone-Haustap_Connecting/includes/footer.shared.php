<?php
// Shared footer partial for both guest and client contexts
// Usage: set $context = 'guest' or 'client' before requiring this file

$context = isset($context) ? $context : 'guest';
$logoSrc = $context === 'client' ? '/client/images/logo.png' : '/guest/images/logo.png';
?>
<!-- FOOTER -->
<footer>
  <div class="footer-content">
    <!-- Left Section -->
    <div class="footer-left">
      <h4>ABOUT HausTap</h4>
      <ul>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Policies</a></li>
        <li><a href="#">Our Sitemap</a></li>
        <li><a href="#">Our Services</a></li>
        <li><a href="#">Contact</a></li>
        <li><a href="#">Testimonials</a></li>
      </ul>
    </div>

    <!-- Center Section -->
    <div class="footer-center">
      <img src="<?= $logoSrc ?>" alt="HausTap Logo" />
      <p>Your space. Your peace. Your Glow</p>
    </div>

    <!-- Right Section -->
    <div class="footer-right">
      <h4>FOLLOW US</h4> <br>
      <ul>
        <li><i class="fab fa-facebook-f"></i> Facebook</li>
        <li><i class="fab fa-instagram"></i> Instagram</li>
      </ul>
      <div class="contact-info">
        <p>
          Address: Abc Road 12345<br />
          Philippines<br />
          Phone: +65 949 9226 246<br />
          Email: HAUSTAP_PH@gmail.com
        </p>
      </div>
    </div>
  </div>
  <div class="footer-bottom">2025 HausTap. All Rights Reserved.</div>
</footer>
<script>
document.addEventListener('DOMContentLoaded', function () {
  try {
    var prevNavs = document.querySelectorAll('nav.pagination a[aria-label="Previous"]');
    prevNavs.forEach(function(el){
      el.addEventListener('click', function(e){ e.preventDefault(); window.history.back(); });
    });
    var pagDivs = document.querySelectorAll('div.pagination');
    pagDivs.forEach(function(pag){
      var buttons = pag.querySelectorAll('button');
      if (!buttons.length) return;
      var prev = buttons[0];
      if (prev && prev.textContent.trim() === '<') {
        prev.addEventListener('click', function(e){ e.preventDefault(); window.history.back(); });
      }
    });
  } catch (e) {}
});
</script>
<script>
// Hardened cleanup: scan on load and watch DOM mutations for path-like text overlays.
(function(){
  function isSvgPathish(s){
    if(!s) return false;
    var str = String(s).trim();
    if (str.length < 30) return false; // ignore very short strings
    // If the text contains normal alphabetic words, skip (excluding common path cmd letters)
    if (/[A-Za-z]{2,}/.test(str.replace(/[vVhHlLcCsStTaAzZ]/g, ''))) return false;
    // Path-like: mostly numbers, separators, and single-letter commands
    var cleaned = str.replace(/[0-9eE\s\.,\-+vVhHlLcCsStTaAzZ]/g, '');
    var ratio = cleaned.length / str.length; // portion that is non-pathish
    if (ratio < 0.05) return true; // 95%+ path-ish content
    // Heuristic: lots of decimals and scientific notation like "9e-3" or repeated "-"/"."
    var decimalRuns = (str.match(/\d+\.\d+/g) || []).length;
    var sciRuns = (str.match(/\d+e[\-+]?\d+/gi) || []).length;
    var dashes = (str.match(/\-/g) || []).length;
    var dots = (str.match(/\./g) || []).length;
    var tokens = str.split(/\s+/).length;
    var numericTokenRatio = (decimalRuns + sciRuns) / Math.max(tokens, 1);
    return numericTokenRatio > 0.4 || (dashes + dots) > 20;
  }

  function scanAndRemove(root){
    var removed = 0;
    try {
      var firstSample = null;
      // Remove path-like TEXT NODES
      var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null);
      var toRemove = [];
      var n;
      while((n = walker.nextNode())){
        var t = n.nodeValue || '';
        if (isSvgPathish(t)) { toRemove.push(n); if (!firstSample) firstSample = t.slice(0, 300); }
      }
      toRemove.forEach(function(node){ if(node.parentNode) node.parentNode.removeChild(node); removed++; });

      // Remove ELEMENTS whose direct textContent is path-like (allow all tags here for thorough cleanup)
      if (root.querySelectorAll) {
        var elements = root.querySelectorAll('*');
        elements.forEach(function(el){
          if (el.childElementCount === 0) {
            var text = (el.textContent || '').trim();
            if (isSvgPathish(text)) { el.remove(); removed++; }
          }
        });
      }
    } catch(e) {}
    try { if (removed && firstSample) { sessionStorage.setItem('ht_overlay_sample', firstSample); console.info('Removed overlay-like text nodes:', removed); } } catch(e) {}
    return removed;
  }

  function setupObserver(){
    try {
      var obs = new MutationObserver(function(mutations){
        mutations.forEach(function(m){
          if (m.type === 'childList'){
            m.addedNodes.forEach(function(node){
              if (node.nodeType === 3){
                if (isSvgPathish(node.nodeValue || '')){
                  if (node.parentNode) node.parentNode.removeChild(node);
                }
              } else if (node.nodeType === 1){
                scanAndRemove(node);
              }
            });
          }
          if (m.type === 'characterData'){
            var t = m.target && m.target.nodeValue || '';
            if (isSvgPathish(t)){
              var p = m.target.parentNode; if (p) p.removeChild(m.target);
            }
          }
        });
      });
      obs.observe(document.body, { childList: true, subtree: true, characterData: true });
    } catch(e) {}
  }

  document.addEventListener('DOMContentLoaded', function(){
    try {
      scanAndRemove(document.body);
      setupObserver();
    } catch(e) {}
  });
  // If DOM is already ready (script at footer), run immediately
  if (document.readyState && document.readyState !== 'loading') {
    try { scanAndRemove(document.body); setupObserver(); } catch(e) {}
  }
})();
</script>
<!-- Logout handler shared across guest/client pages -->
<script src="/client/js/logout.js" defer></script>
<script>
// Client-side repair for common mojibake sequences caused by prior mis-encoding
document.addEventListener('DOMContentLoaded', function () {
  try {
    var map = {
      'â€“': '–',
      'â€”': '—',
      'â€˜': '‘',
      'â€™': '’',
      'â€œ': '“',
      'â€': '”',
      'â€¢': '•',
      'â€¦': '…',
      'Â ': ' ',
      'Â': '',
      'â‚±': '₱',
      'â˜…': '★',
      'âœ”': '✔',
      'â†�': '←',
      'ðŸ“·': '📷',
      'ðŸŽ¥': '🎥'
    };
    function replaceText(s){
      var out = s;
      for (var k in map) {
        if (!Object.prototype.hasOwnProperty.call(map, k)) continue;
        out = out.split(k).join(map[k]);
      }
      return out;
    }
    var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null);
    var node;
    var changed = 0;
    while (node = walker.nextNode()) {
      var t = node.nodeValue;
      var r = replaceText(t);
      if (r !== t) { node.nodeValue = r; changed++; }
    }
    // Optionally log to console for debugging
    // if (changed) console.debug('Mojibake text repaired:', changed);
  } catch (e) {}
});
</script>
<script>
// Defensive cleanup: remove accidental large SVG path-like text nodes that can overlay the UI.
// This targets strings composed almost entirely of digits, punctuation, and SVG path commands.
document.addEventListener('DOMContentLoaded', function () {
  try {
    var isSvgPathish = function (s) {
      if (!s || s.length < 80) return false; // ignore short strings
      // If the text contains normal alphabetic words, skip (excluding common path cmd letters)
      if (/[A-Za-z]{2,}/.test(s.replace(/[vVhHlLcCsStTaAzZ]/g, ''))) return false;
      // Path-like: mostly numbers, separators, and single-letter commands
      var cleaned = s.replace(/[0-9eE\s\.,\-+vVhHlLcCsStTaAzZ]/g, '');
      var ratio = cleaned.length / s.length; // how much is non-pathish
      return ratio < 0.05; // 95%+ path-ish content
    };

    var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null);
    var node;
    var removed = 0;
    while ((node = walker.nextNode())) {
      var t = node.nodeValue || '';
      if (isSvgPathish(t)) {
        // Remove the text node to eliminate the visual overlay
        var parent = node.parentNode;
        if (parent) parent.removeChild(node);
        removed++;
      }
    }
    // Optional: console.debug('Removed overlay-like text nodes:', removed);
  } catch (e) {}
});
</script>
