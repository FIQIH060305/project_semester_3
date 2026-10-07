<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

if (!\App\Models\Admin::where('email', 'admin@gerak.test')->exists()) {
    \App\Models\Admin::create([
        'nama' => 'Admin GERAK',
        'email' => 'admin@gerak.test',
        'password' => Illuminate\Support\Facades\Hash::make('admin123'),
    ]);

    echo "created\n";
} else {
    echo "exists\n";
}
