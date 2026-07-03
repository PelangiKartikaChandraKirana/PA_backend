<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "Testing Python API...\n";
$start = microtime(true);
$response = Http::timeout(60)->attach(
    'face_image',
    file_get_contents('/Users/elrya/fullstacksiapman/siapman_baru/assets/images/batik_pattern.png'),
    'test.png'
)->post('http://127.0.0.1:5001/verify_face_only', [
    'user_id' => 1,
]);

echo "Time taken: " . (microtime(true) - $start) . " seconds\n";
echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";
