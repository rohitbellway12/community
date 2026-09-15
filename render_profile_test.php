<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$profile = \App\Models\Profile::first();
auth()->login($profile->user);
view()->share('errors', new \Illuminate\Support\ViewErrorBag());

$controller = app(\App\Http\Controllers\Community\UserProfileController::class);
$response = $controller->show($profile->username);
$html = $response->render();

echo "createDiscussionModal script found: " . (strpos($html, 'function createDiscussionModal') !== false ? 'YES' : 'NO') . "\n";
