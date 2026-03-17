<?php

namespace App\Console\Commands;

use App\Models\Club;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NormalizeClubLogos extends Command
{
    protected $signature = 'club:normalize-logos';
    protected $description = 'Normalize club logo filenames to stable naming convention';

    public function handle()
    {
        $clubs = Club::whereNotNull('logo_url')->get();
        
        foreach ($clubs as $club) {
            $oldPath = $club->logo_url;
            
            if (!Storage::disk('public')->exists($oldPath)) {
                $this->warn("Club {$club->id}: Logo file not found at {$oldPath}");
                continue;
            }
            
            $ext = pathinfo($oldPath, PATHINFO_EXTENSION);
            $base = Str::slug($club->slug ?: $club->name ?: 'club');
            $stableBase = $base . '-' . $club->id;
            $newFilename = $stableBase . '.' . $ext;
            $newPath = 'uploads/club_logos/' . $newFilename;
            
            if ($oldPath === $newPath) {
                $this->info("Club {$club->id}: Already using stable naming");
                continue;
            }
            
            // Copy old file to new stable name
            $oldFullPath = storage_path('app/public/' . $oldPath);
            $newFullPath = storage_path('app/public/' . $newPath);
            
            if (!copy($oldFullPath, $newFullPath)) {
                $this->error("Club {$club->id}: Failed to copy {$oldPath} to {$newPath}");
                continue;
            }
            
            // Update database
            $club->logo_url = $newPath;
            $club->save();
            
            // Delete old file
            Storage::disk('public')->delete($oldPath);
            
            $this->info("Club {$club->id}: Normalized {$oldPath} → {$newPath}");
        }
        
        $this->info('Logo normalization complete!');
        return 0;
    }
}
