<?php
$dbPath = __DIR__ . '/db_unzip/database/database.sqlite';
if (!file_exists($dbPath)) {
    die("Database file not found at: $dbPath\n");
}

$db = new PDO("sqlite:$dbPath");
$tablesQuery = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
$tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

echo "Tables in sqlite database:\n";
foreach ($tables as $table) {
    if (str_starts_with($table, 'sqlite_')) continue;
    $countQuery = $db->query("SELECT count(*) FROM `$table`");
    $count = $countQuery->fetchColumn();
    echo "- $table: $count rows\n";
}
