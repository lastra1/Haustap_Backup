const express = require('express');
const http = require('http');
const https = require('https');
const router = express.Router();
const path = require('path');

function fetchPhp(pathname, cb, errCb) {
  const options = { hostname: 'localhost', port: 8003, path: `/${pathname}`, method: 'GET' };
  const req = http.request(options, cb);
  req.on('error', errCb || (() => {}));
  req.end();
}

function rewriteHtml(html, basePrefix) {
  const prefix = basePrefix.endsWith('/') ? basePrefix : basePrefix + '/';
  return html
    .replace(/href="\s*css\//gi, `href="${prefix}css/`)
    .replace(/src="\s*css\//gi, `src="${prefix}css/`)
    .replace(/href="\s*js\//gi, `href="${prefix}js/`)
    .replace(/src="\s*js\//gi, `src="${prefix}js/`)
    .replace(/href="\s*images\//gi, `href="${prefix}images/`)
    .replace(/src="\s*images\//gi, `src="${prefix}images/`)
    .replace(/src=['"]https?:\/\/cdn\.socket\.io\/[^'\"]+['"]/gi, 'src="/client/socket.io.js"');
}

function streamHtml(pathname, res, basePrefix) {
  fetchPhp(pathname, r => {
    const chunks = [];
    r.on('data', c => chunks.push(c));
    r.on('end', () => {
      const raw = Buffer.concat(chunks).toString('utf8');
      const html = rewriteHtml(raw, basePrefix);
      res.status(r.statusCode || 200).set('Content-Type', 'text/html; charset=utf-8').send(html);
    });
  }, () => res.status(502).send('Bad gateway'));
}

function proxyToPhp(pathname, res) {
  fetchPhp(pathname, r => {
    res.status(r.statusCode || 200);
    const ct = r.headers['content-type'] || 'text/html; charset=utf-8';
    res.set('Content-Type', ct);
    r.pipe(res);
  }, () => res.status(502).send('Bad gateway'));
}

router.get('/socket.io.js', (req, res) => {
  https.get('https://cdn.socket.io/4.7.5/socket.io.min.js', r => {
    res.status(r.statusCode || 200);
    res.set('Content-Type', 'application/javascript');
    r.pipe(res);
  }).on('error', () => res.status(502).send('Bad gateway'));
});

router.get('/gardening', (req, res) => streamHtml('Outdoor_Services/Gardening.php', res, '/client/Outdoor_Services'));
router.get('/cleaning', (req, res) => streamHtml('Homecleaning/cleaning.php', res, '/client/Homecleaning'));

router.get('/Outdoor_Services/*', (req, res) => {
  const tail = req.params[0] || '';
  proxyToPhp('Outdoor_Services/' + tail, res);
});

router.get('/Homecleaning/*', (req, res) => {
  const tail = req.params[0] || '';
  proxyToPhp('Homecleaning/' + tail, res);
});

function proxyWithFallback(primary, secondary, res) {
  fetchPhp(primary, r => {
    if ((r.statusCode || 200) === 404) {
      fetchPhp(secondary, r2 => {
        res.status(r2.statusCode || 200);
        res.set('Content-Type', r2.headers['content-type'] || 'text/plain');
        r2.pipe(res);
      }, () => res.status(404).send('Not found'));
      return;
    }
    res.status(r.statusCode || 200);
    res.set('Content-Type', r.headers['content-type'] || 'text/plain');
    r.pipe(res);
  }, () => res.status(502).send('Bad gateway'));
}

router.get('/css/*', (req, res) => {
  const tail = req.params[0] || '';
  proxyToPhp('client/css/' + tail, res);
});

router.get('/js/multi-select-services.js', (req, res) => {
  const file = path.join(__dirname, '..', '..', 'client', 'js', 'multi-select-services.js');
  res.sendFile(file);
});

router.get('/js/*', (req, res) => {
  const tail = req.params[0] || '';
  proxyToPhp('client/js/' + tail, res);
});

router.get('/images/*', (req, res) => {
  const tail = req.params[0] || '';
  proxyToPhp('client/images/' + tail, res);
});

router.get('/my_account/*', (req, res) => {
  const tail = req.params[0] || '';
  proxyToPhp('my_account/' + tail, res);
});

router.get('/homepage.php', (req, res) => streamHtml('client/homepage.php', res, '/client'));
router.get('/services.php', (req, res) => streamHtml('client/services.php', res, '/client'));
router.get('/About.php', (req, res) => streamHtml('client/About.php', res, '/client'));
router.get('/contact_client.php', (req, res) => streamHtml('client/contact_client.php', res, '/client'));

router.get('/*.php', (req, res) => {
  const tail = (req.path || '').replace(/^\/+/, '');
  proxyToPhp('client/' + tail, res);
});
router.get('*', (req, res) => streamHtml('Outdoor_Services/Gardening.php', res, '/client/Outdoor_Services'));

module.exports = router;
