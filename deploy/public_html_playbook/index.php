<?php
// Bridge front controller that lives in public_html/playbook
// It bootstraps the Laravel app located outside webroot in ../playbook

// Assumes this layout:
// - App:   /home/USER/playbook
// - Web:   /home/USER/public_html/playbook (this file)
// So from here to the app: ../../playbook

require __DIR__ . '/../../playbook/vendor/autoload.php';
$app = require_once __DIR__ . '/../../playbook/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);
