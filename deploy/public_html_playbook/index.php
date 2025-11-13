<?php
// Bridge front controller that lives in public_html/playbook
// It bootstraps the Laravel app which may be outside webroot (../../playbook)
// or nested under this directory (./app), depending on the hosting layout.

// Try to detect the application base path dynamically.
$candidates = [
    // App nested under this web dir (common when placing project at public_html/playbook/app)
    __DIR__ . '/app',
    // App at current directory (rare but possible if project root is public_html/playbook)
    __DIR__,
    // App outside webroot
    __DIR__ . '/../../playbook',
    // One-level up nested variant
    __DIR__ . '/../app',
];

$appBase = null;
foreach ($candidates as $base) {
    if (is_file($base . '/vendor/autoload.php') && is_file($base . '/bootstrap/app.php')) {
        $appBase = $base;
        break;
    }
}

if (!$appBase) {
    http_response_code(500);
    echo 'Laravel app base not found. Checked: ' . htmlspecialchars(implode(', ', $candidates));
    exit;
}

require $appBase . '/vendor/autoload.php';
$app = require_once $appBase . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);
