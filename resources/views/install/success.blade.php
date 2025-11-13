<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Installed - {{ config('app.name') }}</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/tailwindcss@2.2.19/dist/tailwind.min.css" />
</head>
<body class="font-sans bg-gray-100">
  <div class="max-w-xl mx-auto p-6 text-center">
    <div class="bg-white shadow rounded p-6">
      <h1 class="text-2xl font-semibold mb-2">Installation Complete</h1>
      <p class="text-gray-700 mb-4">{{ config('app.name') }} has been installed.</p>
      <a href="{{ url('/') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2 rounded">Go to App</a>
    </div>
  </div>
</body>
</html>
