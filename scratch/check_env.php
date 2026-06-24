<?php
$dirs = glob('c:\xampp\htdocs\mian-traders*', GLOB_ONLYDIR);
foreach ($dirs as $dir) {
    $envPath = $dir . '/.env';
    if (file_exists($envPath)) {
        $lines = file($envPath);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), 'DB_DATABASE=')) {
                echo basename($dir) . ": " . trim($line) . "\n";
            }
        }
    }
}
