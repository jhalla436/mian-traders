<?php
$lines = file('resources/views/mt/pos/index.blade.php');
foreach ($lines as $i => $line) {
    if (preg_match('/position:\s*(fixed|absolute)/i', $line)) {
        echo "Line " . ($i + 1) . ": " . trim($line) . "\n";
    }
}
