document.addEventListener('DOMContentLoaded', function () {
  const filterBtn = document.querySelector('.filter-btn');
  const dropdown = document.getElementById('filterDropdown');
  const applyBtn = document.querySelector('.apply-btn');

  if (!filterBtn || !dropdown) return;

  function openDropdown() {
    dropdown.classList.add('show');
    filterBtn.setAttribute('aria-expanded', 'true');
  }

  function closeDropdown() {
    dropdown.classList.remove('show');
    filterBtn.setAttribute('aria-expanded', 'false');
  }

  filterBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    if (dropdown.classList.contains('show')) {
      closeDropdown();
    } else {
      openDropdown();
    }
  });

  // prevent clicks inside dropdown from closing immediately
  dropdown.addEventListener('click', function (e) {
    e.stopPropagation();
  });

  // close when clicking outside
  document.addEventListener('click', function () {
    if (dropdown.classList.contains('show')) closeDropdown();
  });

  // close on escape
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeDropdown();
  });

  // close when Apply is clicked (could be extended to actually filter)
  if (applyBtn) {
    applyBtn.addEventListener('click', function () {
      // TODO: wire up actual filtering logic
      closeDropdown();
    });
  }

  // ----------------------
  // Search bar: live filter
  // ----------------------
  const searchInput = document.querySelector('.search-bar');
  const tableBody = document.querySelector('table tbody');

  if (searchInput && tableBody) {
    const rows = Array.from(tableBody.querySelectorAll('tr'));

    function filterRows(query) {
      const q = (query || '').trim().toLowerCase();
      if (!q) {
        rows.forEach(r => r.style.display = '');
        return;
      }

      rows.forEach(r => {
        const text = r.textContent.replace(/\s+/g, ' ').toLowerCase();
        r.style.display = text.indexOf(q) !== -1 ? '' : 'none';
      });
    }

    // simple debounce
    let debounceTimer = null;
    searchInput.addEventListener('input', function (e) {
      const q = e.target.value;
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => filterRows(q), 180);
    });

    // support pressing Enter to focus first visible row (optional behavior)
    searchInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        const firstVisible = rows.find(r => r.style.display !== 'none');
        if (firstVisible) {
          const cell = firstVisible.querySelector('td');
          if (cell) cell.focus && cell.focus();
        }
      }
    });
  }
});
