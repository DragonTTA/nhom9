<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class SyncPermissionsFromRoutes extends Command
{
    /**
     * Tên command (dùng khi gọi Artisan)
     *
     * @var string
     */
    protected $signature = 'permissions:sync-routes';

    /**
     * Mô tả command
     *
     * @var string
     */
    protected $description = 'Scan Permission';

    /**
     * Thực thi command
     */
    public function handle()
    {
        $routes = Route::getRoutes();

        $created = 0;
        $skipped = 0;

        foreach ($routes as $route) {
            $name = $route->getName();
            $prefix = $route->getPrefix();

            if (!$name || ($prefix && str_starts_with($prefix, 'admin'))) {
                $skipped++;
                continue;
            }
            if (!Permission::where('name', $name)->exists()) {
                Permission::create(['name' => $name]);
                $this->info("✅ Created permission: {$name}");
                $created++;
            } else {
                $skipped++;
            }
        }

        $this->line("✨ Done! Created: {$created}, Skipped: {$skipped}");
        return 0;
    }
}
