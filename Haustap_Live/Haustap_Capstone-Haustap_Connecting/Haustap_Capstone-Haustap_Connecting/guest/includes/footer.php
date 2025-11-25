<?php
// Guest footer now delegates to the shared footer component
$context = 'guest';
require dirname(__DIR__, 2) . '/includes/footer.shared.php';
?>