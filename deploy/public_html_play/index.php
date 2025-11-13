<?php
// Bridge front controller that lives in public_html/play
// It bootstraps the Laravel app located outside webroot in ../play

// Adjust these paths if your hosting layout differs.
// This assumes:
// - App:   /home/USER/play
// - Web:   /home/USER/public_html/play (this file)
// So from here to the app: ../../play

require __DIR__ . '/../../play/vendor/autoload.php';
$app = require_once __DIR__ . '/../../play/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);
