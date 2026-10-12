<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // เพิ่มบรรทัดนี้

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('admin-only', function ($user) {
            return (int) $user->role === 1; // ใช้ (int) เพื่อความชัวร์ว่าเป็นตัวเลข
        });
    }
}