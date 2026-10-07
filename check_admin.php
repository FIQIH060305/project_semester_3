<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (\App\Models\Admin::all() as $admin) {
    echo $admin->email . " | " . $admin->password . PHP_EOL;
}

var_dump(\App\Models\Admin::count());
