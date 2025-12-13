<?php
echo "<pre>";
print_r(scandir(__DIR__));
echo "\n\n--- ROOT TREE ---\n";
function scan($dir, $prefix = '') {
    $files = scandir($dir);
    foreach ($files as $f) {
        if ($f === '.' || $f === '..') continue;
        $path = $dir . '/' . $f;
        echo $prefix . $f . "\n";
        if (is_dir($path)) {
            scan($path, $prefix . '   ');
        }
    }
}
scan(__DIR__);
echo "</pre>";
?>
