<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Đừng quên thêm dòng này
use App\Models\Category;
use App\Models\AdminNotification;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.user_header', function ($view) {
            $categories = Category::all();
            $view->with('categories', $categories);
        });

        Carbon::setLocale('vi');

        View::composer('components.admin_header', function ($view) {
            // 5 thông báo mới nhất
            $notifications = AdminNotification::orderByDesc('created_at')
                ->limit(5)
                ->get();

            // Số chưa đọc
            $unreadCount = AdminNotification::where('is_read', false)->count();

            $view->with(compact('notifications', 'unreadCount'));
        });
    }
}
