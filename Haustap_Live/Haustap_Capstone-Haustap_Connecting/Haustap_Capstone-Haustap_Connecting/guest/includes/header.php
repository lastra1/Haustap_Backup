<?php
// Guest header now delegates to the shared header component
$context = 'guest';
require dirname(__DIR__, 2) . '/includes/header.shared.php';
?>
<script src="/js/lazy-images.js" defer></script>
<script src="/client/js/multi-select-services.js" defer></script>
<script>
  (function(){
    try {
      if (!localStorage.getItem('haustap_token')) return;
      var add = function(src){ var s=document.createElement('script'); s.src=src; s.defer=true; document.head.appendChild(s); };
      add('https://cdn.socket.io/4.7.5/socket.io.min.js');
      add('/client/js/notify.js');
      add('/client/js/toast.js');
      add('/client/js/notify-ui.js');
    } catch(e) {}
  })();
</script>
