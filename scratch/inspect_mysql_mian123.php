<?php
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=mian_traders", "root", "");
    $tablesQuery = $db->query("SHOW TABLES");
    $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

    echo "Tables in MySQL database mian_traders:\n";
    foreach ($tables as $table) {
        $countQuery = $db->query("SELECT count(*) FROM `$table`");
        $count = $countQuery->fetchColumn();
        echo "- $table: $count rows\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
