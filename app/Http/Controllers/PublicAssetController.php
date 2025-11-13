<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicAssetController extends Controller
{
    public function clubLogo(string $filename)
    {
        $relative = 'uploads/club_logos/' . $filename;
        if (!Storage::disk('public')->exists($relative)) {
            abort(404);
        }
        $absolute = storage_path('app/public/' . $relative);
        return response()->file($absolute);
    }

    public function storage(string $path)
    {
        // Serve any file under the public disk
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }
        $absolute = storage_path('app/public/' . $path);
        return response()->file($absolute);
    }
}
