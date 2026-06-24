<?php
require 'C:/xampp/htdocs/mian-traders/vendor/autoload.php';
$app = require_once 'C:/xampp/htdocs/mian-traders/bootstrap/app.php';

$consoleKernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$consoleKernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    $admin = User::where('email', 'ww.alimasood@gmail.com')->first();
    if ($admin) {
        $admin->password = Hash::make('password');
        $admin->save();
        echo "Successfully reset password for ww.alimasood@gmail.com to 'password'\n";
    } else {
        echo "User ww.alimasood@gmail.com not found.\n";
    }

    $cashier = User::where('email', 'miancashier1@gmail.com')->first();
    if ($cashier) {
        $cashier->password = Hash::make('password');
        $cashier->save();
        echo "Successfully reset password for miancashier1@gmail.com to 'password'\n";
    } else {
        echo "User miancashier1@gmail.com not found.\n";
    }
} catch (\Exception $e) {
    echo "Error resetting passwords: " . $e->getMessage() . "\n";
}
