// Minimal admin scripts for Manage Bookings page
(function(){
  const API_BASE = '/api/admin';
  const API_LARAVEL = 'http://127.0.0.1:8001/api';

  function qs(sel){ return document.querySelector(sel); }
  function qsa(sel){ return Array.from(document.querySelectorAll(sel)); }

  async function apiGet(path, params={}){
    const buildUrl = (suffix='') => {
      const u = new URL(API_BASE + path + suffix, window.location.origin);
      Object.entries(params).forEach(([k,v]) => { if(v!==undefined && v!==null) u.searchParams.set(k, v); });
      return u;
    };
    let url = buildUrl('');
    let res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
    // Fallback for environments that require .php endpoints
    if(!res.ok && res.status === 404){
      url = buildUrl('.php');
      res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
    }
    if(!res.ok){ throw new Error('HTTP '+res.status); }
    return res.json();
  }

  async function apiPostLaravel(path, payload={}){
    const url = new URL(API_LARAVEL + path);
    const res = await fetch(url.toString(), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(payload)
    });
    if(!res.ok){ throw new Error('HTTP '+res.status); }
    return res.json();
  }

  function statusToClass(s){
    switch(String(s).toLowerCase()){case 'pending': return 'pending';case 'ongoing': return 'ongoing';case 'completed': return 'complete';case 'cancelled': return 'cancelled';case 'return': return 'return';default: return 'pending';}
  }

  function activeTabStatus(){
    const active = qs('.tabs button.active');
    const label = active ? active.textContent.trim().toLowerCase() : 'all';
    const map = { all:'all', pending:'pending', ongoing:'ongoing', completed:'completed', cancelled:'cancelled', return:'return' };
    return map[label] || 'all';
  }

  function extractClientFromNotes(notes){
    if(!notes) return '-';
    const m = String(notes).match(/Booked with\s+(.+)/i);
    return m ? m[1].trim() : '-';
  }

  function renderTable(items){
    const tbody = qs('#bookingTableBody');
    if(!tbody) return;
    if(!items || items.length===0){
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center">No bookings found</td></tr>';
      return;
    }
    const rows = items.map(it => {
      const id = it.id ?? it.booking_id ?? '-';
      const client = it.client_name ?? it.client ?? extractClientFromNotes(it.notes);
      const provider = it.provider_name ?? it.provider ?? (it.provider_id ?? '-');
      const service = it.service_name ?? it.service ?? '-';
      const when = (it.scheduled_date && it.scheduled_time) ? `${it.scheduled_date} ${it.scheduled_time}` : (it.scheduled_at ?? it.datetime ?? it.date_time ?? '-');
      const st = it.status ?? 'Pending';
      const stClass = statusToClass(st);
      return `<tr>
        <td>${id}</td>
        <td>${client}</td>
        <td>${provider}</td>
        <td>${service}</td>
        <td>${when}</td>
        <td><span class="status ${stClass}">${st}</span></td>
        <td class="arrow">›</td>
      </tr>`;
    }).join('');
    tbody.innerHTML = rows;
  }

  function updatePagination(page, limit, total){
    const info = qs('#paginationInfo');
    if(!info) return;
    const start = Math.min((page-1)*limit+1, total === 0 ? 0 : total);
    const end = Math.min(page*limit, total);
    info.textContent = `Showing ${start}–${end} of ${total} Bookings`;
    const prev = qs('#prevPage'); const next = qs('#nextPage');
    if(prev) prev.disabled = page <= 1;
    if(next) next.disabled = end >= total;
  }

  function initBookings(){
    const tbody = qs('#bookingTableBody');
    if(!tbody) return; // only run on Manage Bookings page
    let page = 1; const limit = 10; let search = '';

    async function load(){
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center">Loading bookings…</td></tr>';
      try{
        const status = activeTabStatus();
        const data = await apiGet('/bookings', { status, search, page, limit });
        renderTable(data.items || []);
        updatePagination(data.page || page, data.limit || limit, data.total || (data.items||[]).length);
      }catch(err){
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;color:#b00">Failed to load (${err.message})</td></tr>`;
      }
    }

    // Tabs
    qsa('.tabs button').forEach(btn => {
      btn.addEventListener('click', () => {
        qsa('.tabs button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        page = 1;
        load();
      });
    });

    // Search debounce
    const input = qs('#searchInput');
    let t;
    if(input){
      input.addEventListener('input', () => {
        clearTimeout(t);
        t = setTimeout(() => { search = input.value.trim(); page = 1; load(); }, 300);
      });
    }

    // Pagination
    const prev = qs('#prevPage'); const next = qs('#nextPage');
    if(prev) prev.addEventListener('click', () => { if(page>1){ page--; load(); } });
    if(next) next.addEventListener('click', () => { page++; load(); });

    load();
  }

  // Applicants list
  function applicantStatusClass(s){
    switch(String(s).toLowerCase()){
      case 'hired': return 'hired';
      case 'scheduled': return 'scheduled';
      case 'rejected': return 'rejected';
      case 'pending_review': return 'pending';
      default: return 'pending';
    }
  }

  function activeApplicantTab(){
    const active = document.querySelector('.tabs .tab.active');
    const label = active ? active.textContent.trim().toLowerCase() : 'all';
    const map = { 'all':'all', 'pending review':'pending_review', 'scheduled':'scheduled', 'hired':'hired', 'rejected':'rejected' };
    return map[label] || 'all';
  }

  function renderApplicants(items){
    const tbody = qs('#applicantTableBody');
    if(!tbody) return;
    if(!items || items.length===0){
      tbody.innerHTML = '<tr><td colspan="5" style="text-align:center">No applicants found</td></tr>';
      return;
    }
    const rows = items.map(it => {
      const id = it.id ?? '-';
      const name = it.name ?? '-';
      const applied = it.applied_at ?? it.joined_at ?? '-';
      const st = String(it.status || 'pending_review');
      const cls = applicantStatusClass(st);
      const label = st.replace('_',' ');
      const email = it.email || '';
      return `<tr>
        <td>${id}</td>
        <td>${name}</td>
        <td>${applied}</td>
        <td><span class="status ${cls}">${label}</span></td>
        <td><a href="applicant_details.php${email ? ('?email='+encodeURIComponent(email)) : ''}" class="arrow">›</a></td>
      </tr>`;
    }).join('');
    tbody.innerHTML = rows;
  }

  function updateApplicantPagination(page, limit, total){
    const info = qs('#paginationInfo');
    if(!info) return;
    const start = Math.min((page-1)*limit+1, total === 0 ? 0 : total);
    const end = Math.min(page*limit, total);
    info.textContent = `Showing ${start}–${end} of ${total} Applicants`;
    const prev = qs('#prevPage'); const next = qs('#nextPage');
    if(prev) prev.disabled = page <= 1;
    if(next) next.disabled = end >= total;
  }

  function initApplicants(){
    const tbody = qs('#applicantTableBody');
    if(!tbody) return; // only run on Manage Applicants page
    let page = 1; const limit = 10; let search = '';
    let filterStatus = 'all';

    async function load(){
      tbody.innerHTML = '<tr><td colspan="5" style="text-align:center">Loading applicants…</td></tr>';
      try{
        const tabStatus = activeApplicantTab();
        const status = filterStatus !== 'all' ? filterStatus : tabStatus;
        const data = await apiGet('/applicants', { status, search, page, limit });
        renderApplicants(data.items || []);
        updateApplicantPagination(data.page || page, data.limit || limit, data.total || (data.items||[]).length);
      }catch(err){
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;color:#b00">Failed to load (${err.message})</td></tr>`;
      }
    }

    // Tabs
    qsa('.tabs .tab').forEach(btn => {
      btn.addEventListener('click', () => {
        qsa('.tabs .tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        page = 1;
        load();
      });
    });

    // Search
    const input = qs('#searchInput');
    let t;
    if(input){
      input.addEventListener('input', () => {
        clearTimeout(t);
        t = setTimeout(() => { search = input.value.trim(); page = 1; load(); }, 300);
      });
    }

    // Filter dropdown buttons
    const filterMenu = qs('#statusFilterMenu');
    if(filterMenu){
      filterMenu.querySelectorAll('button[data-status]').forEach(b => {
        b.addEventListener('click', () => {
          filterStatus = b.getAttribute('data-status') || 'all';
          page = 1;
          load();
        });
      });
    }

    // Pagination
    const prev = qs('#prevPage'); const next = qs('#nextPage');
    if(prev) prev.addEventListener('click', () => { if(page>1){ page--; load(); } });
    if(next) next.addEventListener('click', () => { page++; load(); });

    load();
  }

  // Dashboard summary
  async function fetchSystemSummary(){
    try{
      const s = await apiGet('/system/summary');
      if(s && (s.success === true || s.summary)) return s.summary || s;
      throw new Error('Invalid summary');
    }catch(err){
      try{
        const a = await apiGet('/analytics/summary');
        if(a && (a.success === true || a.summary)) return a.summary || a;
      }catch(_){/* ignore */}
      return null;
    }
  }

  function applyDashboard(summary){
    const setText = (id, val) => { const el = qs(`#${id}`); if(el) el.textContent = String(val); };
    if(!summary) {
      setText('totalBookings', '—');
      setText('pendingJobs', '—');
      setText('verifiedProviders', '—');
      setText('totalClients', '—');
      return;
    }
    setText('totalBookings', summary.total_bookings ?? summary.bookings ?? '—');
    setText('pendingJobs', summary.pending_jobs ?? summary.pending ?? '—');
    setText('verifiedProviders', summary.verified_providers ?? summary.providers_verified ?? '—');
    setText('totalClients', summary.total_clients ?? summary.clients ?? '—');
  }

  async function initDashboard(){
    // Only run on pages that have dashboard cards
    if(!qs('#totalBookings')) return;
    const summary = await fetchSystemSummary();
    applyDashboard(summary);
  }

  // Applicant details page: wire Update Status to provider endpoints
  function initApplicantDetails(){
    const updateBtn = qs('.update-btn');
    const dropdown = qs('.status-dropdown');
    if(!updateBtn || !dropdown) return; // only run on applicant_details page
    const params = new URLSearchParams(location.search);
    const email = params.get('email');
    updateBtn.addEventListener('click', async () => {
      const choiceText = dropdown.value || dropdown.options[dropdown.selectedIndex]?.text || '';
      const choice = String(choiceText).trim().toLowerCase();
      if(!email){ alert('Missing applicant email.'); return; }
      try{
        if(choice === 'hired'){
          const r = await apiPostLaravel('/admin/providers/approve', { email });
          alert((r && r.success) ? 'Provider approved.' : (r?.message || 'Approve failed'));
        }else if(choice === 'rejected'){
          const r = await apiPostLaravel('/admin/providers/revoke', { email, remove_role: true });
          alert((r && r.success) ? 'Provider revoked.' : (r?.message || 'Revoke failed'));
        }else{
          alert('Status updated locally. No provider role change needed.');
        }
      }catch(err){
        alert('Request failed: ' + (err?.message || String(err)));
      }
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initDashboard();
    initBookings();
    initApplicants();
    initApplicantDetails();
  });
})();

