<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Install - {{ config('app.name') }}</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/tailwindcss@2.2.19/dist/tailwind.min.css" />
</head>
<body class="font-sans bg-gray-100">
  <div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Install {{ config('app.name') }}</h1>
    <div class="bg-white shadow rounded p-6">
      <form method="POST" action="{{ route('install.run') }}" class="space-y-4">
        <div>
          <label class="block text-sm font-medium">App URL</label>
          <input type="url" name="app_url" value="{{ old('app_url', url('/')) }}" class="mt-1 w-full border rounded p-2" required />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">DB Host</label>
            <input type="text" name="db_host" value="{{ old('db_host', 'localhost') }}" class="mt-1 w-full border rounded p-2" required />
          </div>
          <div>
            <label class="block text-sm font-medium">DB Port</label>
            <input type="number" name="db_port" value="{{ old('db_port', 3306) }}" class="mt-1 w-full border rounded p-2" />
          </div>
          <div>
            <label class="block text-sm font-medium">DB Name</label>
            <input type="text" name="db_database" value="{{ old('db_database') }}" class="mt-1 w-full border rounded p-2" required />
          </div>
          <div>
            <label class="block text-sm font-medium">DB Username</label>
            <input type="text" name="db_username" value="{{ old('db_username') }}" class="mt-1 w-full border rounded p-2" required />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium">DB Password</label>
            <input type="text" name="db_password" value="{{ old('db_password') }}" class="mt-1 w-full border rounded p-2" />
          </div>
        </div>

        <h2 class="text-lg font-semibold mt-4">Mail (optional)</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">Mailer</label>
            <input type="text" name="mail_mailer" value="{{ old('mail_mailer', 'smtp') }}" class="mt-1 w-full border rounded p-2" />
          </div>
          <div>
            <label class="block text-sm font-medium">Host</label>
            <input type="text" name="mail_host" value="{{ old('mail_host') }}" class="mt-1 w-full border rounded p-2" />
          </div>
          <div>
            <label class="block text-sm font-medium">Port</label>
            <input type="number" name="mail_port" value="{{ old('mail_port', 587) }}" class="mt-1 w-full border rounded p-2" />
          </div>
          <div>
            <label class="block text-sm font-medium">Username</label>
            <input type="text" name="mail_username" value="{{ old('mail_username') }}" class="mt-1 w-full border rounded p-2" />
          </div>
          <div>
            <label class="block text-sm font-medium">Password</label>
            <input type="text" name="mail_password" value="{{ old('mail_password') }}" class="mt-1 w-full border rounded p-2" />
          </div>
          <div>
            <label class="block text-sm font-medium">Encryption</label>
            <input type="text" name="mail_encryption" value="{{ old('mail_encryption', 'tls') }}" class="mt-1 w-full border rounded p-2" />
          </div>
          <div>
            <label class="block text-sm font-medium">From Address</label>
            <input type="email" name="mail_from_address" value="{{ old('mail_from_address') }}" class="mt-1 w-full border rounded p-2" />
          </div>
          <div>
            <label class="block text-sm font-medium">From Name</label>
            <input type="text" name="mail_from_name" value="{{ old('mail_from_name', config('app.name')) }}" class="mt-1 w-full border rounded p-2" />
          </div>
        </div>

        @if ($errors->any())
          <div class="bg-red-100 text-red-800 p-3 rounded">
            <ul class="list-disc pl-5">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="mt-4">
          <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2 rounded">Install</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
