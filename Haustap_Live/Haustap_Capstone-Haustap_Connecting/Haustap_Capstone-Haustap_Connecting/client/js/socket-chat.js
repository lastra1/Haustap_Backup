// Socket.IO-based chat wiring for contact_client.php (nested web app)
(function(){
  function $(sel){ return document.querySelector(sel); }
  function qs(name){ try{ return new URLSearchParams(window.location.search).get(name); }catch(e){ return null; } }
  function fmtTime(ts){ try { var d = new Date(ts); return d.toLocaleTimeString(); } catch(e){ return ''; } }
  function safe(fn){ try { fn(); } catch(e){} }
  var lastTs = 0;

  var SOCKET_URL = window.SOCKET_URL || 'http://localhost:3000';
  var bookingId = parseInt(qs('booking_id') || '0', 10) || null;
  var senderRole = (window.CHAT_ROLE || 'client');
  var senderId = window.CHAT_USER_ID || null;

  var messagesEl = $('.chat-messages');
  var inputEl = document.querySelector('.chat-input input[type="text"]');
  var sendBtn = document.querySelector('.chat-input button');

  function renderMessage(m){
    var side = (m.sender === 'client') ? 'right' : 'left';
    var div = document.createElement('div');
    div.className = 'message ' + side;
    var bubble = document.createElement('div');
    bubble.className = 'bubble';
    bubble.textContent = m.text + ' \u00A0 ' + fmtTime(m.ts);
    if (side === 'left') {
      var iconL = document.createElement('i'); iconL.className = 'fa-solid fa-user';
      div.appendChild(iconL); div.appendChild(bubble);
    } else {
      var iconR = document.createElement('i'); iconR.className = 'fa-solid fa-user';
      div.appendChild(bubble); div.appendChild(iconR);
    }
    messagesEl.appendChild(div);
    messagesEl.scrollTop = messagesEl.scrollHeight;
  }

  var socket = null;
  function connect(){
    if (!bookingId) return;
    // eslint-disable-next-line no-undef
    socket = io(SOCKET_URL, { transports: ['websocket'], withCredentials: true });

    socket.on('connect', function(){
      socket.emit('booking:join', { booking_id: bookingId, role: senderRole, user_id: senderId });
    });

    socket.on('booking:history', function(payload){
      var msgs = (payload && payload.messages) || [];
      msgs.forEach(renderMessage);
    });

    socket.on('booking:message:new', function(payload){
      var m = payload && payload.message; if (m) renderMessage(m);
    });

    // Fallback to HTTP polling if socket fails
    socket.on('connect_error', function(err){
      console.warn('Socket connect error, switching to HTTP chat', err && (err.message||err));
      enableHttpFallback();
    });
  }

  function send(){
    if (!socket || !bookingId) return;
    var text = (inputEl.value||'').trim();
    if (!text) return;
    inputEl.value='';
    socket.emit('booking:message', { booking_id: bookingId, text: text, sender: senderRole, sender_id: senderId });
  }

  document.addEventListener('DOMContentLoaded', function(){
    window.CHAT_TRANSPORT = 'socket';
    if (!bookingId) return;
    connect();
    if (sendBtn) sendBtn.addEventListener('click', send);
    if (inputEl) inputEl.addEventListener('keydown', function(e){ if (e.key==='Enter'){ e.preventDefault(); send(); } });

    // If socket.io is unavailable, fallback after a short grace period
    setTimeout(function(){
      if (!socket || (socket && socket.disconnected)) { enableHttpFallback(); }
    }, 1500);
  });

  // --- HTTP fallback using HausTapChatAPI ---
  function enableHttpFallback(){
    if (!bookingId) return;
    if (window.CHAT_TRANSPORT === 'http') return; // Already enabled
    window.CHAT_TRANSPORT = 'http';
    console.info('HTTP chat fallback enabled');
    // Open conversation and start polling
    safe(function(){ if (window.HausTapChatAPI) { window.HausTapChatAPI.openConversation(bookingId).catch(function(){}); } });
    if (sendBtn) {
      sendBtn.removeEventListener('click', send);
      sendBtn.addEventListener('click', httpSend);
    }
    if (inputEl) {
      inputEl.removeEventListener('keydown', function(){});
      inputEl.addEventListener('keydown', function(e){ if (e.key==='Enter'){ e.preventDefault(); httpSend(); } });
    }
    setInterval(httpPoll, 2000);
  }

  function httpSend(){
    if (!bookingId || !window.HausTapChatAPI) return;
    var text = (inputEl.value||'').trim();
    if (!text) return;
    inputEl.value='';
    window.HausTapChatAPI.sendMessage(bookingId, { text: text, sender: senderRole, sender_id: senderId })
      .then(function(resp){ var m = resp && resp.message; if (m){ renderMessage(m); lastTs = Math.max(lastTs, m.ts||0); } })
      .catch(function(){ alert('Failed to send'); });
  }

  function httpPoll(){
    if (!bookingId || !window.HausTapChatAPI) return;
    window.HausTapChatAPI.listMessages(bookingId, lastTs)
      .then(function(resp){
        var arr = (resp && resp.messages) || []; arr.forEach(function(m){ renderMessage(m); lastTs = Math.max(lastTs, m.ts||0); });
      }).catch(function(){});
  }
})();
