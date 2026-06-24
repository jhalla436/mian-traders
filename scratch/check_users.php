<?php
require 'C:/xampp/htdocs/mian-traders/vendor/autoload.php';
$app = require_once 'C:/xampp/htdocs/mian-traders/bootstrap/app.php';

$consoleKernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$consoleKernel->bootstrap();

use App\Models\User;

try {
    $users = User::all();
    echo "Found " . $users->count() . " users in the database:\n";
    foreach ($users as $u) {
        echo " - ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Role: {$u->role} | Password (hash): " . substr($u->password, 0, 20) . "...\n";
    }
} catch (\Exception $e) {
    echo "Error checking users: " . $e->getMessage() . "\n";
}
