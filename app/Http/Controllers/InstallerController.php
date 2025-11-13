<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InstallerController extends Controller
{
    protected function lockPath(): string
    {
        return storage_path('app/install.lock');
    }

    public function index()
    {
        if (File::exists($this->lockPath())) {
            abort(404);
        }
        return view('install.index');
    }

    public function install(Request $request)
    {
        if (File::exists($this->lockPath())) {
            abort(404);
        }

        $data = $request->validate([
            'app_url' => 'required|url',
            'db_host' => 'required|string',
            'db_port' => 'nullable|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
            'mail_mailer' => 'nullable|string',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|numeric',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string',
        ]);

        // Write .env
        $env = $this->buildEnv($data);
        File::put(base_path('.env'), $env);

        // Clear cached config in case it's present from packaging
        try { Artisan::call('config:clear'); } catch (\Throwable $e) {}

        // Generate app key
        try { Artisan::call('key:generate', ['--force' => true]); } catch (\Throwable $e) {}

        // Run migrations
        Artisan::call('migrate', ['--force' => true]);

        // Try to create storage link; if not supported, copy as fallback
        try {
            Artisan::call('storage:link');
        } catch (\Throwable $e) {
            $publicStorage = public_path('storage');
            if (!File::exists($publicStorage)) {
                File::makeDirectory($publicStorage, 0755, true);
            }
            // Copy only once; best-effort
            $from = storage_path('app/public');
            if (File::exists($from)) {
                File::copyDirectory($from, $publicStorage);
            }
        }

        // Cache for performance (optional, safe on shared hosting)
        try { Artisan::call('config:cache'); } catch (\Throwable $e) {}
        try { Artisan::call('route:cache'); } catch (\Throwable $e) {}
        try { Artisan::call('view:cache'); } catch (\Throwable $e) {}

        // Lock installer
        File::put($this->lockPath(), now()->toDateTimeString());

        return redirect()->route('install.success');
    }

    protected function buildEnv(array $d): string
    {
        $appUrl = rtrim($d['app_url'], '/');
        $dbPort = $d['db_port'] ?? 3306;
        $mail = [
            'mailer' => $d['mail_mailer'] ?? 'smtp',
            'host' => $d['mail_host'] ?? '',
            'port' => $d['mail_port'] ?? '',
            'username' => $d['mail_username'] ?? '',
            'password' => $d['mail_password'] ?? '',
            'encryption' => $d['mail_encryption'] ?? 'tls',
            'from_address' => $d['mail_from_address'] ?? 'no-reply@localhost',
            'from_name' => $d['mail_from_name'] ?? config('app.name', 'Laravel'),
        ];

        return implode("\n", [
            'APP_NAME="'.addslashes(config('app.name', 'Laravel')).'"',
            'APP_ENV=production',
            'APP_KEY=',
            'APP_DEBUG=false',
            'APP_URL='.$appUrl,
            '',
            'LOG_CHANNEL=stack',
            'LOG_LEVEL=warning',
            '',
            'DB_CONNECTION=mysql',
            'DB_HOST='.$d['db_host'],
            'DB_PORT='.$dbPort,
            'DB_DATABASE='.$d['db_database'],
            'DB_USERNAME='.$d['db_username'],
            'DB_PASSWORD='.(str_contains($d['db_password'] ?? '', ' ') ? '"'.addslashes($d['db_password']).'"' : ($d['db_password'] ?? '')),
            '',
            'BROADCAST_DRIVER=log',
            'CACHE_DRIVER=file',
            'FILESYSTEM_DISK=public',
            'QUEUE_CONNECTION=sync',
            'SESSION_DRIVER=file',
            'SESSION_LIFETIME=120',
            '',
            'MEMCACHED_HOST=127.0.0.1',
            '',
            'MAIL_MAILER='.$mail['mailer'],
            'MAIL_HOST='.$mail['host'],
            'MAIL_PORT='.$mail['port'],
            'MAIL_USERNAME='.$mail['username'],
            'MAIL_PASSWORD='.(str_contains($mail['password'], ' ') ? '"'.addslashes($mail['password']).'"' : $mail['password']),
            'MAIL_ENCRYPTION='.$mail['encryption'],
            'MAIL_FROM_ADDRESS='.$mail['from_address'],
            'MAIL_FROM_NAME="'.$mail['from_name'].'"',
            '',
            'VITE_APP_NAME="'.addslashes(config('app.name', 'Laravel')).'"',
        ])."\n";
    }

    public function success()
    {
        if (!File::exists($this->lockPath())) {
            return redirect()->route('install.index');
        }
        try {
            return view('install.success');
        } catch (\Throwable $e) {
            $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Installed</title><style>body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,sans-serif;background:#f3f4f6;margin:0;padding:2rem}.card{max-width:640px;margin:2rem auto;background:#fff;border-radius:0.5rem;box-shadow:0 1px 2px rgba(0,0,0,.06),0 1px 3px rgba(0,0,0,.1);padding:1.5rem;text-align:center}.btn{display:inline-block;background:#4f46e5;color:#fff;text-decoration:none;font-weight:600;padding:.5rem 1rem;border-radius:.375rem}</style></head><body><div class="card"><h1 style="font-size:1.5rem;margin-bottom:.5rem">Installation Complete</h1><p style="color:#374151;margin-bottom:1rem">The application has been installed successfully.</p><a class="btn" href="' . e(url('/')) . '">Go to App</a></div></body></html>';
            return response($html)->header('Content-Type','text/html');
        }
    }
}
