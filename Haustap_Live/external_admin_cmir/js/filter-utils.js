/**
 * Comprehensive Filter Utilities for Admin Dashboard
 * Handles filter dropdown toggles, date filters, status filters, and search
 * Provides consistent functionality across all admin pages
 */

// ============================================================================
// FILTER DROPDOWN TOGGLE - Works with any filter button and dropdown-content
// ============================================================================
function initFilterDropdown(filterBtnSelector = '.filter-btn', dropdownSelector = '.dropdown-content') {
  const filterBtn = document.querySelector(filterBtnSelector);
  let dropdownContent = null;
  
  if (!filterBtn) {
    console.warn('Filter button not found:', filterBtnSelector);
    return null;
  }
  
  // Try to find dropdown in parent first, then document-wide
  if (filterBtn.parentElement) {
    dropdownContent = filterBtn.parentElement.querySelector(dropdownSelector);
  }
  if (!dropdownContent) {
    dropdownContent = document.querySelector(dropdownSelector);
  }
  
  if (!dropdownContent) {
    console.warn('Dropdown content not found:', dropdownSelector);
    return null;
  }
  
  // Toggle dropdown on button click
  filterBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdownContent.classList.toggle('show');
    
    // Update button icon/text if needed
    const isOpen = dropdownContent.classList.contains('show');
    updateFilterButtonIcon(filterBtn, isOpen);
  });
  
  // Close dropdown when clicking outside
  window.addEventListener('click', (e) => {
    if (!dropdownContent.contains(e.target) && !filterBtn.contains(e.target)) {
      dropdownContent.classList.remove('show');
      updateFilterButtonIcon(filterBtn, false);
    }
  });
  
  // Prevent closing when clicking inside dropdown
  dropdownContent.addEventListener('click', (e) => {
    e.stopPropagation();
  });
  
  return { filterBtn, dropdownContent };
}

// ============================================================================
// UPDATE FILTER BUTTON ICON
// ============================================================================
function updateFilterButtonIcon(filterBtn, isOpen) {
  if (!filterBtn) return;
  
  const html = filterBtn.innerHTML;
  // Check if it has an arrow indicator
  if (html.includes('▲') || html.includes('▼')) {
    filterBtn.innerHTML = html.replace(/▲|▼/, isOpen ? '▲' : '▼');
  }
  
  // Set aria-expanded for accessibility
  filterBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
}

// ============================================================================
// DATE FILTER - Filter rows by date range
// ============================================================================
function initDateFilter(options = {}) {
  const {
    fromInputId = 'from-date',
    toInputId = 'to-date',
    applyBtnSelector = '.apply-btn',
    tableSelector = '.table-container tbody',
    dateColumnIndex = 4, // 0-indexed column containing date
    onFilterApplied = null
  } = options;
  
  const fromInput = document.getElementById(fromInputId);
  const toInput = document.getElementById(toInputId);
  const applyBtn = document.querySelector(applyBtnSelector);
  const tableBody = document.querySelector(tableSelector);
  
  if (!fromInput || !toInput) {
    console.warn('Date filter inputs not found');
    return null;
  }
  
  // Parse date string in various formats
  function parseRowDate(text) {
    if (!text) return null;
    
    // Try to match YYYY-MM-DD HH:MM format
    const m = text.match(/(\d{4})\D(\d{2})\D(\d{2})(?:[^\d]*(\d{2}):?(\d{2}))?/);
    if (m) {
      const y = m[1], mo = m[2], d = m[3];
      const hh = m[4] || '00', mm = m[5] || '00';
      const iso = `${y}-${mo}-${d}T${hh}:${mm}:00`;
      const dt = new Date(iso);
      if (!isNaN(dt.getTime())) return dt;
    }
    
    // Fallback to native Date parsing
    const p = Date.parse(text);
    if (!isNaN(p)) return new Date(p);
    return null;
  }
  
  // Update row visibility based on all active filters
  function updateRowVisibility() {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
      const fHidden = row.dataset.filterHidden === 'true';
      const sHidden = row.dataset.searchHidden === 'true';
      row.style.display = (fHidden || sHidden) ? 'none' : '';
    });
  }
  
  // Apply date filter
  function applyDateFilter() {
    const fromVal = fromInput.value;
    const toVal = toInput.value;
    const fromDate = fromVal ? new Date(fromVal) : null;
    const toDateRaw = toVal ? new Date(toVal) : null;
    const toDate = toDateRaw ? new Date(toDateRaw.getTime() + 86399999) : null; // End of day
    
    let matched = 0;
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
      const dateCell = row.querySelector(`td:nth-child(${dateColumnIndex + 1})`);
      if (!dateCell) {
        row.dataset.filterHidden = '';
        return;
      }
      
      const rowDate = parseRowDate(dateCell.textContent.trim());
      if (!rowDate) {
        row.dataset.filterHidden = '';
        return;
      }
      
      const within = (!fromDate || rowDate >= fromDate) && (!toDate || rowDate <= toDate);
      row.dataset.filterHidden = within ? '' : 'true';
      if (within) matched++;
    });
    
    updateRowVisibility();
    console.debug('Date filter applied:', { fromVal, toVal, matched, total: rows.length });
    
    if (onFilterApplied) onFilterApplied(matched);
    return matched;
  }
  
  // Attach event listeners
  if (applyBtn) {
    applyBtn.addEventListener('click', (e) => {
      e.preventDefault();
      applyDateFilter();
      
      // Close dropdown if available
      const dropdown = applyBtn.closest('.dropdown-content') || applyBtn.closest('.filter-dropdown');
      if (dropdown) dropdown.classList.remove('show');
    });
  }
  
  // Apply on date input change
  fromInput.addEventListener('change', applyDateFilter);
  toInput.addEventListener('change', applyDateFilter);
  
  // Initial state
  updateRowVisibility();
  
  return { fromInput, toInput, applyBtn, applyDateFilter };
}

// ============================================================================
// STATUS FILTER - Filter rows by status checkboxes
// ============================================================================
function initStatusFilter(options = {}) {
  const {
    dropdownSelector = '#filterBox',
    checkboxSelector = 'input[type="checkbox"]',
    applyBtnSelector = '.apply-btn',
    tableSelector = '.table-wrapper tbody',
    statusColumnIndex = 3, // 0-indexed column containing status
    onFilterApplied = null
  } = options;
  
  const dropdown = document.querySelector(dropdownSelector);
  if (!dropdown) {
    console.warn('Filter dropdown not found:', dropdownSelector);
    return null;
  }
  
  const checkboxes = dropdown.querySelectorAll(checkboxSelector);
  const applyBtn = dropdown.querySelector(applyBtnSelector);
  const tableBody = document.querySelector(tableSelector);
  
  if (checkboxes.length === 0) {
    console.warn('No checkboxes found in filter dropdown');
    return null;
  }
  
  // Apply status filter
  function applyStatusFilter() {
    const selected = new Set(
      Array.from(checkboxes)
        .filter(cb => cb.checked)
        .map(cb => cb.value)
    );
    
    let matched = 0;
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
      const badge = row.querySelector('.status');
      let status = '';
      
      if (badge) {
        // Determine status from class
        if (badge.classList.contains('active')) status = 'active';
        else if (badge.classList.contains('inactive')) status = 'inactive';
        else if (badge.classList.contains('suspended')) status = 'suspended';
        else if (badge.classList.contains('pending')) status = 'pending';
        else if (badge.classList.contains('ongoing')) status = 'ongoing';
        else if (badge.classList.contains('complete') || badge.classList.contains('completed')) status = 'complete';
        else if (badge.classList.contains('cancelled')) status = 'cancelled';
        else if (badge.classList.contains('return')) status = 'return';
      }
      
      const shouldShow = selected.size === 0 || selected.has(status);
      row.style.display = shouldShow ? '' : 'none';
      if (shouldShow) matched++;
    });
    
    console.debug('Status filter applied:', { selected: Array.from(selected), matched, total: rows.length });
    if (onFilterApplied) onFilterApplied(matched);
    return matched;
  }
  
  // Attach event listeners
  if (applyBtn) {
    applyBtn.addEventListener('click', (e) => {
      e.preventDefault();
      applyStatusFilter();
      dropdown.style.display = 'none';
    });
  }
  
  // Apply on checkbox change (optional - immediate feedback)
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', applyStatusFilter);
  });
  
  // Initial state
  applyStatusFilter();
  
  return { checkboxes, applyBtn, applyStatusFilter };
}

// ============================================================================
// SEARCH FILTER - Filter rows by search text
// ============================================================================
function initSearchFilter(options = {}) {
  const {
    searchInputSelector = '.search-input, .search-bar',
    tableSelector = 'tbody',
    searchableColumns = null, // null = all columns, or array of indices
    debounceMs = 180,
    onSearchApplied = null
  } = options;
  
  const searchInput = document.querySelector(searchInputSelector);
  if (!searchInput) {
    console.warn('Search input not found:', searchInputSelector);
    return null;
  }
  
  let debounceTimer = null;
  
  // Normalize text for comparison
  function normalize(text) {
    return (text || '')
      .toString()
      .replace(/\s+/g, ' ')
      .trim()
      .toLowerCase();
  }
  
  // Apply search filter
  function applySearch(queryText) {
    const query = normalize(queryText);
    const rows = document.querySelectorAll('tbody tr');
    let matched = 0;
    
    rows.forEach(row => {
      let rowText = '';
      
      if (searchableColumns === null || searchableColumns.length === 0) {
        // Search all columns
        rowText = normalize(row.textContent);
      } else {
        // Search specific columns
        const cells = row.querySelectorAll('td');
        rowText = searchableColumns
          .map(idx => cells[idx] ? normalize(cells[idx].textContent) : '')
          .join(' ');
      }
      
      const matches = !query || rowText.includes(query);
      row.dataset.searchHidden = matches ? '' : 'true';
      row.style.display = matches ? '' : 'none';
      if (matches) matched++;
    });
    
    console.debug('Search filter applied:', { query, matched, total: rows.length });
    if (onSearchApplied) onSearchApplied(matched);
    return matched;
  }
  
  // Attach event listeners with debounce
  searchInput.addEventListener('input', (e) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => applySearch(e.target.value), debounceMs);
  });
  
  // Allow Escape to clear search
  searchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      searchInput.value = '';
      applySearch('');
    }
    if (e.key === 'Enter') {
      e.preventDefault();
      clearTimeout(debounceTimer);
      applySearch(searchInput.value);
    }
  });
  
  // Initial search
  applySearch(searchInput.value);
  
  return { searchInput, applySearch };
}

// ============================================================================
// INITIALIZE ALL FILTERS ON PAGE
// ============================================================================
function initAllFilters(config = {}) {
  const {
    filterDropdown = true,
    dateFilter = false,
    statusFilter = false,
    searchFilter = false,
    dateFilterOptions = {},
    statusFilterOptions = {},
    searchFilterOptions = {}
  } = config;
  
  const results = {};
  
  if (filterDropdown) {
    results.filterDropdown = initFilterDropdown();
  }
  
  if (dateFilter) {
    results.dateFilter = initDateFilter(dateFilterOptions);
  }
  
  if (statusFilter) {
    results.statusFilter = initStatusFilter(statusFilterOptions);
  }
  
  if (searchFilter) {
    results.searchFilter = initSearchFilter(searchFilterOptions);
  }
  
  return results;
}

// Export for use in other scripts
if (typeof window !== 'undefined') {
  window.FilterUtils = {
    initFilterDropdown,
    initDateFilter,
    initStatusFilter,
    initSearchFilter,
    initAllFilters,
    updateFilterButtonIcon
  };
}
