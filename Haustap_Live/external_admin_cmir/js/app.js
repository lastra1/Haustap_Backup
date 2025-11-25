// Shared admin helpers for fetching APIs and rendering UI
(function(){
  const API_BASE = '/api/admin';

  async function apiFetch(path, params = {}, options = {}) {
    const qs = new URLSearchParams(params).toString();
    const url = API_BASE + path + (qs ? ('?' + qs) : '');
    const resp = await fetch(url, {
      method: options.method || 'GET',
      headers: Object.assign({'Accept':'application/json'}, options.headers || {}),
      body: options.body || undefined,
      credentials: 'same-origin'
    });
    if (!resp.ok) {
      throw new Error('API error: ' + resp.status + ' ' + resp.statusText);
    }
    return resp.json();
  }

  // Dashboard: populate summary cards if present
  async function initDashboard() {
    const elTotalBookings = document.getElementById('totalBookings');
    const elPendingJobs = document.getElementById('pendingJobs');
    const elVerifiedProviders = document.getElementById('verifiedProviders');
    const elTotalClients = document.getElementById('totalClients');
    if (!elTotalBookings || !elPendingJobs || !elVerifiedProviders || !elTotalClients) return;
    try {
      const data = await apiFetch('/analytics/summary');
      const s = (data && data.data) || {};
      elTotalBookings.textContent = s.totalBookings ?? '—';
      elPendingJobs.textContent = s.pendingJobs ?? '—';
      elVerifiedProviders.textContent = s.verifiedProviders ?? '—';
      elTotalClients.textContent = s.totalClients ?? '—';
    } catch (err) {
      console.error('Failed to load analytics summary', err);
    }
  }

  // Manage Applicants: dynamic table rendering
  function statusClass(s){
    const m = {
      hired: 'hired',
      pending_review: 'pending',
      scheduled: 'scheduled',
      rejected: 'rejected'
    };
    return m[s] || 'pending';
  }

  async function initApplicants() {
    const tableBody = document.getElementById('applicantTableBody');
    const tabs = document.querySelectorAll('.tabs .tab');
    const searchInput = document.getElementById('searchInput');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    const pgInfo = document.getElementById('paginationInfo');
    if (!tableBody || tabs.length === 0) return;

    const state = { status: 'all', search: '', page: 1, limit: 10, total: 0 };

    async function load(){
      try {
        const res = await apiFetch('/applicants', { status: state.status, search: state.search, page: state.page, limit: state.limit });
        const items = res.items || [];
        state.total = res.total || items.length;
        tableBody.innerHTML = items.map(it => `
          <tr>
            <td>${it.id}</td>
            <td>${escapeHtml(it.name)}</td>
            <td>${escapeHtml(formatDate(it.applied_at))}</td>
            <td><span class="status ${statusClass(it.status)}">${labelStatus(it.status)}</span></td>
            <td class="arrow">›</td>
          </tr>
        `).join('');
        const start = (state.page - 1) * state.limit + 1;
        const end = Math.min(state.page * state.limit, state.total);
        if (pgInfo) pgInfo.textContent = `Showing ${start}–${end} of ${state.total}`;
      } catch(err){
        console.error('Failed to load applicants', err);
        tableBody.innerHTML = `<tr><td colspan="5">Failed to load applicants.</td></tr>`;
        if (pgInfo) pgInfo.textContent = '';
      }
      if (prevBtn) prevBtn.classList.toggle('disabled', state.page <= 1);
      const maxPage = Math.max(1, Math.ceil(state.total / state.limit));
      if (nextBtn) nextBtn.classList.toggle('disabled', state.page >= maxPage);
    }

    // Tab clicks
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const text = tab.textContent.trim().toLowerCase();
        state.status = ({
          'all': 'all',
          'pending review': 'pending_review',
          'scheduled': 'scheduled',
          'hired': 'hired',
          'rejected': 'rejected'
        })[text] || 'all';
        state.page = 1;
        load();
      });
    });

    // Search
    if (searchInput) {
      let to;
      searchInput.addEventListener('input', () => {
        clearTimeout(to);
        to = setTimeout(() => {
          state.search = searchInput.value.trim();
          state.page = 1;
          load();
        }, 250);
      });
    }

    // Pagination
    if (prevBtn) prevBtn.addEventListener('click', () => { if (state.page > 1) { state.page--; load(); } });
    if (nextBtn) nextBtn.addEventListener('click', () => {
      const maxPage = Math.max(1, Math.ceil(state.total / state.limit));
      if (state.page < maxPage) { state.page++; load(); }
    });

    load();
  }

  // Helpers
  function escapeHtml(str){
    return String(str || '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[s]));
  }
  function formatDate(d){
    if (!d) return '';
    try {
      const dt = new Date(d);
      return dt.toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' });
    } catch { return d; }
  }
  function labelStatus(s){
    const m = {
      pending_review: 'Pending Review',
      scheduled: 'Scheduled',
      hired: 'Hired',
      rejected: 'Rejected'
    };
    return m[s] || s;
  }

  // Bootstrap
  document.addEventListener('DOMContentLoaded', function(){
    initDashboard();
    initApplicants();
  });
})();

