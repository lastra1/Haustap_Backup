// Global lightweight notification listener for the client web app
(function(){
  var SOCKET_URL = window.SOCKET_URL || 'http://localhost:3000';
  var socket = null;

  function getUser(){
    try { return JSON.parse(localStorage.getItem('haustap_user') || 'null'); } catch { return null; }
  }
  function getUserId(u){
    if (!u) return null;
    return u.id || u.user_id || u.client_id || null;
  }
  function getRole(u){
    var r = (u && u.role && u.role.name) ? String(u.role.name).toLowerCase() : '';
    return r || null;
  }

  function connect(){
    try {
      // eslint-disable-next-line no-undef
      socket = io(SOCKET_URL, { transports: ['websocket'], withCredentials: true });
    } catch (e) {
      console.warn('Socket.IO not available for notifications', e);
      return;
    }

    socket.on('connect', function(){
      var u = getUser();
      var uid = getUserId(u);
      var role = getRole(u);
      if (uid) { socket.emit('user:join', { user_id: uid }); }
      if (role) { socket.emit('role:join', { role: role }); }
    });

    socket.on('notify', function(payload){
      try {
        var detail = Object.assign({ ts: Date.now() }, payload || {});
        var ev = new CustomEvent('haus:notify', { detail: detail });
        window.dispatchEvent(ev);
        // Provide a minimal dev log without UI changes
        if (detail && detail.type) {
          console.info('[Notify]', detail.type, detail);
        }
      } catch (e) {}
    });
  }

  // Small helper to allow pages to subscribe without DOM changes
  window.HausTapNotify = {
    subscribe: function(fn){
      if (typeof fn !== 'function') return;
      window.addEventListener('haus:notify', function(e){ fn(e.detail); });
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', connect);
  } else {
    connect();
  }
})();

