<?php
// Root router shim: delegate to unified public router
// Allows: php -S localhost:8000 router.php from repo root
require __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'index.php';

