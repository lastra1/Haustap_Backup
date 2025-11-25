// Inject a "Clear All Cancelled" button on bookings page and wire action
(function(){
  document.addEventListener('DOMContentLoaded', function(){
    try {
      var container = document.querySelector('.booking-list[data-status="cancelled"]');
      if (!container) return;
      if (document.getElementById('ht-clear-cancelled-btn')) return;
      var wrap = document.createElement('div');
      wrap.className = 'bulk-actions cancelled-actions';
      wrap.style.margin = '10px 0';
      var btn = document.createElement('button');
      btn.id = 'ht-clear-cancelled-btn';
      btn.className = 'btn btn-danger btn-sm';
      btn.textContent = 'Clear All Cancelled';
      wrap.appendChild(btn);
      container.parentNode.insertBefore(wrap, container.nextSibling);
      btn.addEventListener('click', function(){
        if (!window.HausTapBookingAPI || typeof window.HausTapBookingAPI.clearCancelled !== 'function') {
          alert('Bulk clear is not available in preview mode.');
          return;
        }
        var confirmFn = window.htConfirm || function(msg){ return Promise.resolve(window.confirm(msg||'')); };
        confirmFn('Delete all cancelled bookings?', { title: 'Clear Cancelled', okText: 'Delete All', cancelText: 'Keep' })
          .then(function(go){
            if (!go) return;
            window.HausTapLoading && window.HausTapLoading.show && window.HausTapLoading.show();
            window.HausTapBookingAPI.clearCancelled()
              .then(function(){ window.location.reload(); })
              .catch(function(err){ console.error('Bulk clear failed', err); alert('Unable to clear cancelled bookings right now.'); })
              .finally(function(){ window.HausTapLoading && window.HausTapLoading.hide && window.HausTapLoading.hide(); });
          });
      });
    } catch(e) { /* ignore */ }
  });
})();
