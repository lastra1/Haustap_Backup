// Lightweight Booking API helper for the legacy UI
// Relies on window.API_BASE configured by /login_sign up/js/api.js
(function() {
  const API_BASE = (
    (window.FIREBASE_API_BASE || (((window.API_BASE||'').replace(/\/+$/, '')) + '/firebase'))
  ).replace(/\/+$/, '') || 'http://127.0.0.1:8000/api/firebase';

  function getToken() {
    return localStorage.getItem('haustap_token') || '';
  }

  async function request(path, opts) {
    const url = `${API_BASE}${path}`;
    const headers = Object.assign({
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    }, opts && opts.headers);
    const token = getToken();
    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
    }
    const fetchOpts = Object.assign({ method: 'GET', headers }, opts);
    const res = await fetch(url, fetchOpts);
    const ct = res.headers.get('content-type') || '';
    let data = null;
    if (ct.includes('application/json')) {
      data = await res.json();
    } else {
      const text = await res.text();
      try { data = JSON.parse(text); } catch { data = { message: text }; }
    }
    if (!res.ok) {
      const message = (data && (data.message || data.error)) || `HTTP ${res.status}`;
      const err = new Error(message);
      err.response = res;
      err.data = data;
      throw err;
    }
    return data;
  }

  async function createBooking(payload) {
    return request('/bookings', { method: 'POST', body: JSON.stringify(payload) });
  }

  async function listBookings(query) {
    const qs = query ? `?${new URLSearchParams(query).toString()}` : '';
    return request('/bookings' + qs, { method: 'GET' });
  }

  async function getBooking(id) {
    return request(`/bookings/${id}`, { method: 'GET' });
  }

  async function updateStatus(id, status) {
    return request(`/bookings/${id}/status`, { method: 'POST', body: JSON.stringify({ status }) });
  }

  async function cancelBooking(id, payload) {
    const body = (payload && payload.reason) ? JSON.stringify({ reason: String(payload.reason) }) : undefined;
    return request(`/bookings/${id}/cancel`, { method: 'POST', body });
  }

  async function rateBooking(id, rating) {
    return request(`/bookings/${id}/rate`, { method: 'POST', body: JSON.stringify({ rating }) });
  }

  async function requestReturn(id, payload) {
    const body = {
      issues: Array.isArray(payload && payload.issues) ? payload.issues : [],
      notes: (payload && payload.notes) ? String(payload.notes) : ''
    };
    return request(`/bookings/${id}/return`, { method: 'POST', body: JSON.stringify(body) });
  }

  async function listReturns() {
    return request('/bookings/returns', { method: 'GET' });
  }

  window.HausTapBookingAPI = {
    createBooking,
    listBookings,
    getBooking,
    listReturns,
    updateStatus,
    cancelBooking,
    rateBooking,
    requestReturn,
    getToken,
    API_BASE,
  };
})();
