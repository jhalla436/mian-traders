<?php
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=laravel", "root", "");
    
    echo "=== Companies in laravel ===\n";
    $companies = $db->query("SELECT id, name FROM companies")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($companies as $c) {
        echo "ID: {$c['id']} - Name: {$c['name']}\n";
    }

    echo "\n=== Products in laravel ===\n";
    $products = $db->query("SELECT id, name FROM products")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($products as $p) {
        echo "ID: {$p['id']} - Name: {$p['name']}\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
