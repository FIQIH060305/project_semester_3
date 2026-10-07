<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$admin = \App\Models\Admin::first();
foreach (['admin123','password123','admin','123456','secret','password'] as $pw) {
    echo $pw . ' => ' . (Illuminate\Support\Facades\Hash::check($pw, $admin->password) ? 'YES' : 'NO') . PHP_EOL;
}
