// Client chat page wiring: bind input, poll messages, render bubbles
(function(){
  function $(sel){ return document.querySelector(sel); }
  function qs(name){ try{ return new URLSearchParams(window.location.search).get(name); }catch(e){ return null; } }
  function fmtTime(ts){ try { var d = new Date(ts); return d.toLocaleTimeString(); } catch(e){ return ''; } }

  var bookingId = parseInt(qs('booking_id') || '0', 10) || null;
  var senderRole = 'client'; // in client app
  var senderId = null; // optional
  var lastTs = 0;

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
      var icon = document.createElement('i'); icon.className = 'fa-solid fa-user';
      div.appendChild(icon); div.appendChild(bubble);
    } else {
      var icon = document.createElement('i'); icon.className = 'fa-solid fa-user';
      div.appendChild(bubble); div.appendChild(icon);
    }
    messagesEl.appendChild(div);
    messagesEl.scrollTop = messagesEl.scrollHeight;
  }

  async function poll(){
    if (!bookingId || !window.HausTapChatAPI) return;
    try {
      var resp = await HausTapChatAPI.listMessages(bookingId, lastTs);
      var arr = (resp && resp.messages) || [];
      arr.forEach(function(m){ renderMessage(m); lastTs = Math.max(lastTs, m.ts||0); });
    } catch(e){ /* ignore transient errors */ }
  }

  async function send(){
    if (!bookingId || !window.HausTapChatAPI) return;
    var text = (inputEl.value||'').trim();
    if (!text) return;
    inputEl.value='';
    try {
      var resp = await HausTapChatAPI.sendMessage(bookingId, { text: text, sender: senderRole, sender_id: senderId });
      var m = resp && resp.message; if (m) { renderMessage(m); lastTs = Math.max(lastTs, m.ts||0); }
    } catch(e){ alert('Failed to send'); }
  }

  document.addEventListener('DOMContentLoaded', function(){
    if (!bookingId) {
      // No booking selected; rely on UI sidebar manual selection or future integration
      return;
    }
    if (window.HausTapChatAPI) {
      HausTapChatAPI.openConversation(bookingId).catch(function(){});
    }
    setInterval(poll, 2000);
    if (sendBtn) sendBtn.addEventListener('click', send);
    if (inputEl) inputEl.addEventListener('keydown', function(e){ if (e.key==='Enter'){ e.preventDefault(); send(); } });
  });
})();
