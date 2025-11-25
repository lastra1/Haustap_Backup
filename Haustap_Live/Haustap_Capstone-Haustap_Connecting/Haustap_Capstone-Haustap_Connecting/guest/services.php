<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Homi</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="css/services.css">
    <link rel="stylesheet" href="css/homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

    <main class="main-content">
        <div class="services-header">
            <h1 class="services-title">Services</h1>
            <select class="category-select"></select>
        </div>

        <div class="services-grid"></div>
    </main>

  <?php include __DIR__ . '/includes/footer.php'; ?>
  <script>
    (function(){
      var select = document.querySelector('.category-select');
      var grid = document.querySelector('.services-grid');
      if (!select || !grid) return;
      var map = {
        cleaning: { href: '/services/cleaning', img: 'images/cleaning.png' },
        outdoor: { href: '/services/outdoor', img: 'images/outdoor.png' },
        repairs: { href: '/services/repairs', img: 'images/repair.png' },
        beauty: { href: '/services/beauty', img: 'images/beauty service.png' },
        wellness: { href: '/services/wellness', img: 'images/wellness.png' },
        tech: { href: '/services/tech', img: 'images/tech.png' }
      };
      function render(categories){
        select.innerHTML = '';
        var opt0 = document.createElement('option');
        opt0.value = '';
        opt0.textContent = 'CATEGORY';
        select.appendChild(opt0);
        grid.innerHTML = '';
        categories.forEach(function(cat){
          var opt = document.createElement('option');
          opt.value = cat.slug;
          opt.textContent = cat.name;
          select.appendChild(opt);
          var info = map[cat.slug] || { href: '/booking/choose-sp?category=' + cat.slug, img: '' };
          var a = document.createElement('a');
          a.href = info.href;
          a.className = 'service-card';
          a.style.textDecoration = 'none';
          a.style.color = 'inherit';
          if (info.img) {
            var img = document.createElement('img');
            img.className = 'service-image';
            img.alt = cat.name;
            img.src = info.img;
            a.appendChild(img);
          }
          var content = document.createElement('div');
          content.className = 'service-content';
          var h2 = document.createElement('h2');
          h2.className = 'service-title';
          h2.textContent = cat.name;
          var p = document.createElement('p');
          p.className = 'service-description';
          p.textContent = cat.description || '';
          content.appendChild(h2);
          content.appendChild(p);
          a.appendChild(content);
          grid.appendChild(a);
        });
        select.onchange = function(){
          var slug = select.value;
          if (!slug) return;
          var info = map[slug] || { href: '/booking/choose-sp?category=' + slug };
          window.location.href = info.href;
        };
      }
      function load(){
        fetch('/api/firebase/categories').then(function(r){ return r.json(); }).then(function(data){
          if (data && data.success && Array.isArray(data.categories)) { render(data.categories); }
        }).catch(function(){
          render([
            { slug: 'cleaning', name: 'Cleaning Services' },
            { slug: 'outdoor', name: 'Outdoor Services' },
            { slug: 'repairs', name: 'Home Repairs' },
            { slug: 'beauty', name: 'Beauty Services' },
            { slug: 'wellness', name: 'Wellness Services' },
            { slug: 'tech', name: 'Tech & Gadget Services' }
          ]);
        });
      }
      load();
    })();
  </script>
</body>
</html>
