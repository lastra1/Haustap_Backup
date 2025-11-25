<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Booking Location</title>
  <link rel="stylesheet" href="/css/global.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/booking_process/css/booking_location.css?v=human-pin-pro-1" />
  <link rel="stylesheet" href="/client/css/homepage.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <!-- Leaflet Map CSS -->
  <link rel="stylesheet" href="/booking_process/vendor/leaflet/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <!-- Fallback CSS if unpkg is blocked -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
</head>

<body>
  <!-- HEADER -->
  <?php include dirname(__DIR__) . "/client/includes/header.php"; ?>
  <script>
    // Guests can proceed; login will be requested at final booking confirmation
  </script>

 <main class="booking-location-page">
    <h1 class="page-title">Booking Location</h1>
    <button class="subcategory-btn"><b>Bungalow</b></button>

    <section class="booking-layout">
      <!-- LEFT SIDE -->
      <div class="left-column">
        <!-- Pin Location Box -->
        <div class="pin-box">
<label for="pinLocation"><i class="fa-solid fa-map-marker" aria-hidden="true"></i> Pin Location:</label>
          <input type="text" id="pinLocation" placeholder="Pin your location" />
        </div>

        <!-- Insert Type of House Box -->
        <div class="house-type-box">
<label for="houseType"><i class="fa-solid fa-home" aria-hidden="true"></i> Insert Type of House:</label>
          <input type="text" id="houseType" placeholder="Insert Type of House #" />
        </div>

        <!-- Map Container -->
        <div id="map" class="map-container" style="min-height:350px;height:350px"></div>
      </div>

      <!-- RIGHT SIDE -->
      <div class="right-column">
        <!-- Set Address Box -->
        <div class="address-box">
          <div class="address-header">
            <i class="fa-solid fa-house icon"></i>
            <h3>Set Address</h3>
          </div>
          <div class="address-body">
            <p>Blk 11 Lot 6 Mary St. Saint Joseph Village 10<br>
              Brgy. Langgam San Pedro City, Laguna</p>
            <button class="edit-btn">Edit</button>
          </div>
        </div>

        <!-- Saved Address Box -->
        <div class="address-box">
          <div class="address-header">
            <div class="header-left">
              <i class="fa-solid fa-floppy-disk icon"></i>
              <h3>Saved Address</h3>
            </div>
            <input type="radio" name="savedAddress" />
          </div>
          <div class="address-body">
            <p>Blk 11 Lot 6 Apple St. Saint Joseph Village 10<br>
              Brgy. Langgam San Pedro City, Laguna</p>
          </div>
        </div>
      </div>
    </section>

    <!-- PAGINATION -->
    <div class="pagination">
      <button>&lt;</button>
      
      
      
      
      <button>&gt;</button>
    </div>
  </main>


  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  <!-- Leaflet Map JS (local first, with CDN fallback inside script) -->
  <script src="/booking_process/vendor/leaflet/leaflet.js"></script>
  <script>
    // Wire pagination to proceed to Booking Schedule and persist address info
    document.addEventListener('DOMContentLoaded', function(){
      var pag = document.querySelector('.pagination');
      if (!pag) return;
      var btns = Array.prototype.slice.call(pag.querySelectorAll('button'));
      var back = btns.length ? btns[0] : null;
      var next = btns.length ? btns[btns.length-1] : null;

      var pinInput = document.getElementById('pinLocation');
      var houseInput = document.getElementById('houseType');
      var savedRadio = document.querySelector('input[type="radio"][name="savedAddress"]');
      var savedAddressText = '';
      if (savedRadio) {
        var savedBox = savedRadio.closest('.address-box');
        var savedP = savedBox ? savedBox.querySelector('.address-body p') : null;
        savedAddressText = savedP ? (savedP.textContent || '').trim() : '';
        savedRadio.addEventListener('change', function(){
          if (savedRadio.checked && savedAddressText) {
            try { localStorage.setItem('booking_address', savedAddressText); } catch(e){}
          }
        });
      }
      var subcat = document.querySelector('.subcategory-btn');

      // Determine correct service label to show on this page
      try {
        var params = new URLSearchParams(window.location.search);
        var house = params.get('house');
        var cleaning = params.get('cleaning');
        var serviceParam = params.get('service');
        var priceParam = params.get('price');
        var stored = localStorage.getItem('selected_service_name');

        function toTitle(s){ return String(s||'').replace(/-/g,' ').replace(/\b\w/g, function(m){ return m.toUpperCase(); }); }
        function cap(s){ return String(s||'').charAt(0).toUpperCase() + String(s||'').slice(1); }

        var computed = '';
        if (house && cleaning) {
          computed = toTitle(house) + ' - ' + cap(cleaning) + ' Cleaning';
        } else if (serviceParam) {
          computed = toTitle(serviceParam);
        } else if (stored) {
          computed = stored;
        }

        if (subcat && computed) {
          subcat.textContent = computed;
          localStorage.setItem('selected_service_name', computed);
        }
        // Persist price from query if present (extra robustness if global capture missed it)
        if (priceParam) {
          var num = Number(String(priceParam).replace(/,/g,''));
          if (!isNaN(num)) {
            localStorage.setItem('selected_service_price', String(num));
          }
        }
      } catch(e){}

      if (back) back.addEventListener('click', function(){
        // Default back behavior: go to previous page
        window.history.back();
      });

      // === Map Integration (Leaflet + Geolocation + Reverse Geocoding) ===
      var mapEl = document.getElementById('map');
      var marker = null;
      var map = null;
      var mapInitialized = false;
      var humanIcon = null; // custom DivIcon for human-shaped marker

      function getLS(k){ try { return localStorage.getItem(k); } catch(e){ return null; } }
      function setLS(k,v){ try { localStorage.setItem(k, v); } catch(e){} }

      function updatePinInput(text){
        if (!text) return;
        if (pinInput) pinInput.value = text;
        setLS('booking_address', text);
      }

      function setMarker(latlng){
        if (!map) return;
        if (marker) { marker.setLatLng(latlng); }
        else {
          marker = L.marker(latlng, { draggable: true, icon: humanIcon || undefined }).addTo(map);
          marker.on('dragend', function(){
            var ll = marker.getLatLng();
            setLS('booking_lat', String(ll.lat));
            setLS('booking_lng', String(ll.lng));
            reverseGeocode(ll);
          });
        }
      }

      function reverseGeocode(latlng){
        // Use OpenStreetMap Nominatim for lightweight reverse geocoding
        var url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + encodeURIComponent(latlng.lat) + '&lon=' + encodeURIComponent(latlng.lng) + '&accept-language=en';
        fetch(url, { headers: { 'Accept': 'application/json' }})
          .then(function(r){ return r.ok ? r.json() : Promise.reject(new Error('Reverse geocoding failed')); })
          .then(function(data){
            var display = (data && (data.display_name || (data.address && data.address.road))) || '';
            updatePinInput(display || (latlng.lat.toFixed(5) + ', ' + latlng.lng.toFixed(5)));
          })
          .catch(function(){
            updatePinInput(latlng.lat.toFixed(5) + ', ' + latlng.lng.toFixed(5));
          });
      }

      function initMap(){
        if (!mapEl || typeof L === 'undefined' || mapInitialized) return;
        mapInitialized = true;
        // Default center: Manila
        var DEFAULT = { lat: 14.5995, lng: 120.9842, zoom: 13 };
        map = L.map('map');
        addTileLayerWithFallback();
        // Build a custom human DivIcon (inline SVG silhouette) once Leaflet is ready
        try {
          // Person-in-pin marker matching the reference (gradient pin, inner person, side arcs, base)
          humanIcon = L.divIcon({
            className: 'human-pin-pro',
            html:
              '<svg viewBox="0 0 64 90" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">' +
              '<defs>' +
              '  <linearGradient id="pinGrad" x1="0" y1="0" x2="1" y2="1">' +
              '    <stop offset="0%" class="grad-stop-1" />' +
              '    <stop offset="100%" class="grad-stop-2" />' +
              '  </linearGradient>' +
              '</defs>' +
              // side arcs
              '<path class="arcs" d="M6 26 a24 24 0 0 1 0 38" fill="none" stroke-width="3" />' +
              '<path class="arcs" d="M58 26 a24 24 0 0 0 0 38" fill="none" stroke-width="3" />' +
              // pin body (circle + pointer)
              '<g>' +
              '  <circle cx="32" cy="30" r="22" fill="url(#pinGrad)" class="pin" />' +
              '  <circle cx="32" cy="30" r="22" fill="none" class="ring" stroke-width="3" />' +
              '  <path d="M32 52 L23 66 L41 66 Z" fill="url(#pinGrad)" class="pin-outline" stroke-width="2" />' +
              '</g>' +
              // base pedestal
              '<path class="base" d="M32 66 c-10 0 -15 6 -15 12 h30 c0 -6 -5 -12 -15 -12 z" fill="none" stroke-width="3" />' +
              // person silhouette inside circle
              '<g class="figure">' +
              '  <circle cx="32" cy="26" r="6" />' +
              '  <path d="M22 40 c0 -6 5 -11 10 -11 s10 5 10 11" />' +
              '</g>' +
              '</svg>',
            iconSize: [64, 90],
            iconAnchor: [32, 78],
            popupAnchor: [0, -64]
          });
        } catch(e) { humanIcon = null; }
        // Ensure Leaflet recalculates dimensions once visible
        setTimeout(function(){ try { map.invalidateSize(); } catch(e){} }, 250);
        // Re-run size checks until container is non-zero (fixes devtools-only rendering)
        (function ensureSized(attempt){
          var w = mapEl ? mapEl.clientWidth : 0;
          var h = mapEl ? mapEl.clientHeight : 0;
          if (w > 0 && h > 0) { try { map.invalidateSize(); } catch(e){} return; }
          if (attempt < 20) { setTimeout(function(){ ensureSized(attempt+1); }, 300); }
        })(0);
        // Keep map sized on resize and when page/tab becomes visible
        window.addEventListener('resize', function(){ try { map.invalidateSize(); } catch(e){} });
        document.addEventListener('visibilitychange', function(){ if (!document.hidden) { setTimeout(function(){ try { map.invalidateSize(); } catch(e){} }, 200); } });
        if (typeof ResizeObserver !== 'undefined' && mapEl) {
          try {
            var ro = new ResizeObserver(function(){ try { map.invalidateSize(); } catch(e){} });
            ro.observe(mapEl);
          } catch(e){}
        }

        // Use stored location if present
        var sLat = parseFloat(getLS('booking_lat'));
        var sLng = parseFloat(getLS('booking_lng'));
        if (!isNaN(sLat) && !isNaN(sLng)) {
          map.setView([sLat, sLng], DEFAULT.zoom);
          setMarker({ lat: sLat, lng: sLng });
        } else if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(pos){
            var ll = { lat: pos.coords.latitude, lng: pos.coords.longitude };
            map.setView([ll.lat, ll.lng], DEFAULT.zoom);
            setMarker(ll);
            reverseGeocode(ll);
          }, function(){
            map.setView([DEFAULT.lat, DEFAULT.lng], DEFAULT.zoom);
          }, { enableHighAccuracy: true, timeout: 8000 });
        } else {
          map.setView([DEFAULT.lat, DEFAULT.lng], DEFAULT.zoom);
        }

        map.on('click', function(ev){
          var ll = ev.latlng;
          setMarker(ll);
          setLS('booking_lat', String(ll.lat));
          setLS('booking_lng', String(ll.lng));
          reverseGeocode(ll);
        });
      }

      // Try multiple tile servers so the map still renders if one is blocked
      function addTileLayerWithFallback(){
        var providers = [
          { url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', attr: '&copy; OpenStreetMap contributors' },
          { url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png', attr: '&copy; OpenStreetMap contributors' },
          { url: 'https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png', attr: '&copy; OpenStreetMap &copy; Carto' },
          { url: 'https://stamen-tiles.a.ssl.fastly.net/terrain/{z}/{x}/{y}.jpg', attr: '&copy; Stamen, OpenStreetMap' }
        ];
        var currentLayer = null;
        function useProvider(i){
          var p = providers[i];
          currentLayer = L.tileLayer(p.url, { attribution: p.attr, maxZoom: 19, crossOrigin: true });
          var switched = false;
          currentLayer.on('tileerror', function(){
            if (switched) return; // avoid thrashing
            switched = true;
            try { map.removeLayer(currentLayer); } catch(e){}
            if (i + 1 < providers.length) {
              useProvider(i + 1);
            } else {
              if (pinInput) pinInput.placeholder = 'Map tiles failed to load. Enter address manually.';
            }
          });
          currentLayer.addTo(map);
        }
        useProvider(0);
      }

      // Initialize map after UI is ready, with CDN fallback if Leaflet didn't load
      function ensureLeafletAndInit(){
        if (typeof L !== 'undefined') { try { initMap(); } catch(e){} return; }
        // Fallback: try jsDelivr if unpkg fails
        var loaded = false;
        var css = document.createElement('link');
        css.rel = 'stylesheet';
        css.href = 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(css);
        var js = document.createElement('script');
        js.src = 'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js';
        js.onload = function(){ loaded = true; try { initMap(); } catch(e){} };
        js.onerror = function(){ console.warn('Leaflet failed to load from both CDNs'); };
        document.body.appendChild(js);
        // If neither CDN loads, show a minimal message in the pin input as fallback
        setTimeout(function(){ if (!loaded && pinInput) { pinInput.placeholder = 'Map failed to load. Enter address manually.'; } }, 3000);
      }
      try { ensureLeafletAndInit(); } catch(e){ try { initMap(); } catch(_){} }
      // Also attempt initialization once the full page loads (fonts/images/layout complete)
      window.addEventListener('load', function(){ try { initMap(); } catch(e){} });

      if (next) next.addEventListener('click', function(){
        // If user selected a saved address, use it directly
        if (savedRadio && savedRadio.checked) {
          var saved = savedAddressText;
          if (saved) {
            try { localStorage.setItem('booking_address', saved); } catch(e){}
            window.location.href = '/booking/schedule';
            return;
          }
        }

        // Prefer map pin (lat/lng) if available; otherwise fall back to manual inputs
        var lat = getLS('booking_lat');
        var lng = getLS('booking_lng');
        var pin = (pinInput && pinInput.value) ? pinInput.value.trim() : '';
        var house = (houseInput && houseInput.value) ? houseInput.value.trim() : '';

        if (lat && lng) {
          // Ensure a readable address string is stored alongside coordinates
          if (!pin) { pin = (Number(lat).toFixed(5) + ', ' + Number(lng).toFixed(5)); }
          setLS('booking_address', pin);
          window.location.href = '/booking/schedule';
          return;
        }

        // Fallback: require at least a pin or house type
        if (!pin && !house) {
          alert('Please set a pin location or house type before proceeding.');
          return;
        }
        var addrParts = [];
        if (house) addrParts.push('House: ' + house);
        if (pin) addrParts.push('Pin: ' + pin);
        var addr = addrParts.join(' | ');
        setLS('booking_address', addr);
        window.location.href = '/booking/schedule';
      });
    });
  </script>
</body>
</html>

