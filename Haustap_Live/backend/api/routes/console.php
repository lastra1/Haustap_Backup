<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use App\Support\FileJsonStore;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('store:sync', function () {
    $this->info('Syncing JSON files into MySQL json_store...');
    if (env('STORE_DRIVER') !== 'mysql') {
        $this->warn('STORE_DRIVER is not mysql; set STORE_DRIVER=mysql in .env to use DB-backed store.');
    }

    $paths = [
        base_path('storage/data'),
        storage_path('data'),
    ];
    $count = 0;
    foreach ($paths as $dir) {
        if (!is_dir($dir)) { continue; }
        $files = File::files($dir);
        foreach ($files as $f) {
            if (strtolower($f->getExtension()) !== 'json') { continue; }
            $path = $f->getPathname();
            $raw = @file_get_contents($path);
            if ($raw === false) { continue; }
            $data = json_decode($raw, true);
            $store = new FileJsonStore($path, $data ?? []);
            $store->write($data ?? []);
            $this->line("Synced: {$path}");
            $count++;
        }
    }
    $this->info("Completed. Synced {$count} file(s).");
})->purpose('Copy existing storage/data/*.json into the database json_store table');
