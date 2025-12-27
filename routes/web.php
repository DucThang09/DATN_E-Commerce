<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashBoardController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\Category_ManageController;
use App\Http\Controllers\Admin\UserAccountController;
use App\Http\Controllers\Admin\OrderStatisticsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\HomeController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\CartController;
use App\Http\Controllers\Auth\CheckoutController;
use App\Http\Controllers\Auth\OrderUserController;
use App\Http\Controllers\Auth\SearchController;
use App\Http\Controllers\Auth\ShopController;
use App\Http\Controllers\Auth\QuickViewController;
use App\Http\Controllers\Auth\ContactController;
use App\Http\Controllers\Auth\OrderHistoryController;
use App\Http\Controllers\Admin\RevenueStatistics;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\VariantController;
use App\Models\Product;
use App\Models\User;
use App\Http\Controllers\AiChatController;

Route::post('/ai/chat', [AiChatController::class, 'chat'])->name('ai.chat');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

Route::get('/register', function () {
    return redirect()->route('login', ['mode' => 'register']);
})->name('register');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::post('/register', [RegisterController::class, 'register']);

Route::post('/contact', [ContactController::class, 'sendMessage'])->name('contact.send');
Route::get('/products/category/{slug}', [HomeController::class, 'showByCategory'])->name('products.category');
Route::get('/products/{company}', [HomeController::class, 'showCompanyProducts'])->name('products.company');

Route::get('/search', [SearchController::class, 'search'])->name('search');

Route::get('/shop', [ShopController::class, 'ShowShop'])->name('shop');
Route::get('/category', [ShopController::class, 'ShowCategory'])->name('category');
Route::get('/quick-view/{pid}', [QuickViewController::class, 'quickView'])->name('quick.view');
Route::get('/contact', [ContactController::class, 'showForm'])->name('contact.form');

Route::get('/verify-account/{email} ', [RegisterController::class, 'verify'])->name('verify');

Route::get('/forgot-password', [RegisterController::class, 'forgot_password'])->name('forgot');
Route::post('/forgot-password', [RegisterController::class, 'check_forgot_password']);

Route::get('/reset-password/{token}', [RegisterController::class, 'reset_password'])->name('reset');
Route::post('/reset-password/{token}', [RegisterController::class, 'check_reset_password']);
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update-qty', [CartController::class, 'updateQty'])->name('cart.update');

    Route::post('/remove-selected', [CartController::class, 'removeSelected'])->name('remove.selected');
    Route::post('/checkout-selected', [CheckoutController::class, 'checkoutSelected'])->name('checkout.selected');
    Route::get('/get-districts', [CheckoutController::class, 'getDistricts'])->name('get.districts');
    Route::get('/get-wards', [CheckoutController::class, 'getWards'])->name('get.wards');
    Route::get('/checkout/info', [CheckoutController::class, 'checkoutInfo'])->name('checkout.info');

    // GET: mở trang payment
    Route::get('/checkout/payment', [CheckoutController::class, 'paymentInfo'])
        ->name('checkout.payment');

    // POST: submit từ form checkout để lưu session
    Route::post('/checkout/payment', [CheckoutController::class, 'paymentPage'])
        ->name('checkout.payment.post');

    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.placeOrder');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])
        ->name('checkout.success');

    Route::get('/profile/update', [ProfileController::class, 'showUpdateForm'])->name('profile.update.form');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/orders/history', [OrderHistoryController::class, 'index'])->name('orders.history');
    Route::post('/orders/{order}/cancel', [OrderHistoryController::class, 'cancel'])->name('orders.cancel');
});


Route::get('/admin', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin', [AdminController::class, 'login'])->name('admin.login_post');
Route::middleware(['admin'])->group(function () {
    Route::post('/admin/logout', [AdminController::class, 'signOut'])->name('admin.logout');

    Route::get('/admin/notifications/latest', [AdminNotificationController::class, 'latest'])
        ->name('admin.notifications.latest');

    Route::get('/admin/dashboard', [DashBoardController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/admin/notifications', [AdminNotificationController::class, 'index'])
        ->name('admin.notifications.index');

    Route::post('/admin/notifications/mark-all-read', [AdminNotificationController::class, 'markAllRead'])
        ->name('admin.notifications.mark_all_read');

    Route::post('/admin/notifications/{id}/mark-read', [AdminNotificationController::class, 'markRead'])
        ->name('admin.notifications.mark_read');

    Route::delete('/admin/notifications/{id}', [AdminNotificationController::class, 'destroy'])
        ->name('admin.notifications.destroy');

    Route::get('/admin/revenue-data', [DashboardController::class, 'revenueData'])
        ->name('admin.revenue_data');

    Route::get('/admin/user-stats', [DashboardController::class, 'userStats'])
        ->name('admin.user_stats');
    Route::get('/admin/online-count', function () {
        $count = User::where('last_seen_at', '>=', now()->subMinutes(1))->count();

        return response()->json([
            'online_users' => $count,
        ]);
    })->middleware('admin')->name('admin.online_count');

    Route::middleware(['super_admin'])->group(function () {

        Route::get('/admin/accounts/create', [AdminAccountController::class, 'create'])
            ->name('admin.accounts.create');

        Route::get('/admin/accounts', [AdminAccountController::class, 'index'])
            ->name('admin.accounts');

        Route::post('/admin/accounts', [AdminAccountController::class, 'store'])
            ->name('admin.register_submit');

        Route::put('/admin/accounts/{id}', [AdminAccountController::class, 'update'])
            ->name('admin.accounts.update');

        Route::get('/admin/accounts/delete/{id}', [AdminAccountController::class, 'destroy'])
            ->name('admin.accounts.delete');
    });

    Route::post('/admin/users/store', [UserAccountController::class, 'store'])
        ->name('admin.users_store');

    Route::get('/admin/users-accounts', [UserAccountController::class, 'index'])
        ->name('admin.users_accounts');

    Route::delete('/admin/users-accounts/delete/{id}', [UserAccountController::class, 'destroy'])
        ->name('admin.users_delete');

    Route::get('/admin/users/search', [UserAccountController::class, 'search'])
        ->name('admin.users_search');

    Route::put('/admin/users/{id}', [UserAccountController::class, 'update'])
        ->name('admin.users_update');

    Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products_store');
    Route::get('/admin/products/delete/{id}', [ProductController::class, 'destroy'])->name('admin.products_delete');
    Route::delete('/admin/products/delete-selected', [ProductController::class, 'deleteSelected'])
        ->name('admin.products_delete_selected');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.update_product');
    Route::post('/products/{id}/update', [ProductController::class, 'update'])->name('products.update');
    Route::get('/admin/products/search', [ProductController::class, 'product_search'])->name('admin.products.search');

    Route::get('/admin/placed-orders', [OrderController::class, 'index'])->name('admin.placed_orders');
    Route::post('/admin/update-payment', [OrderController::class, 'updatePayment'])->name('admin.update_payment');
    Route::get('/admin/delete-order/{id}', [OrderController::class, 'destroy'])->name('admin.delete_order');
    Route::get('/admin/placed-orders', [OrderController::class, 'search_order'])->name('admin.placed_orders');
    Route::get(
        '/admin/orders/{order}/detail-json',
        [\App\Http\Controllers\Admin\OrderController::class, 'detailJson']
    )->name('admin.orders.detail_json');

    Route::get('/admin/messages', [MessageController::class, 'index'])->name('admin.messages');
    Route::get('/admin/messages/delete/{id}', [MessageController::class, 'destroy'])->name('admin.messages.delete');

    Route::get('/admin/messages/sort', [MessageController::class, 'message_sort'])->name('admin.sort');
    Route::get('/admin/messages/search', [MessageController::class, 'message_search'])->name('admin.search');

    Route::get('/admin/profile', [AdminProfileController::class, 'edit'])->name('admin.profile_edit');
    Route::post('/admin/profile', [AdminProfileController::class, 'update'])->name('admin.profile_update');

    Route::get('/admin/revenue_statistics', [RevenueStatistics::class, 'index'])->name('admin.revenue_statistics');
    Route::get('/revenue-statistics/search', [RevenueStatistics::class, 'search'])->name('admin.revenue_statistics');
    Route::get('/revenue-statistics/sort', [RevenueStatistics::class, 'sort'])->name('admin.revenue_statistics');

    Route::get('/admin/category_manage', [Category_ManageController::class, 'index'])->name('admin.category_manage');
    Route::post('/categories/store', [Category_ManageController::class, 'store'])->name('admin.category.store');
    
    Route::get('/categories/delete/{category_id}', [Category_ManageController::class, 'delete'])->name('admin.category.delete');
    Route::post('/categories/store_brand', [Category_ManageController::class, 'store_brand'])->name('admin.brand.store_brand');
    Route::get('/categories/delete_brand/{brand_id}', [Category_ManageController::class, 'delete_brand'])->name('admin.brand.delete_brand');
    Route::post('/categories/store_color', [Category_ManageController::class, 'store_color'])->name('admin.color.store_color');
    Route::get('/categories/delete_color/{color_id}', [Category_ManageController::class, 'delete_color'])->name('admin.color.delete_color');
    // CATEGORY
    Route::get('/admin/category/edit/{category_id}', [Category_ManageController::class, 'edit'])
        ->name('admin.category.edit');
    Route::post('/admin/category/update/{category_id}', [Category_ManageController::class, 'update'])
        ->name('admin.category.update');

    // BRAND
    Route::get('/admin/brand/edit/{brand_id}', [Category_ManageController::class, 'edit_brand'])
        ->name('admin.brand.edit_brand');
    Route::post('/admin/brand/update/{brand_id}', [Category_ManageController::class, 'update_brand'])
        ->name('admin.brand.update_brand');

    // COLOR
    Route::get('/admin/color/edit/{color_id}', [Category_ManageController::class, 'edit_color'])
        ->name('admin.color.edit_color');
    Route::post('/admin/color/update/{color_id}', [Category_ManageController::class, 'update_color'])
        ->name('admin.color.update_color');


    Route::get('/admin/revenue/month', [OrderStatisticsController::class, 'index'])
        ->name('admin.revenue_month');

    Route::get('/admin/revenue/month/sort', [OrderStatisticsController::class, 'sort'])
        ->name('admin.revenue_month_sort');

    Route::get('/admin/revenue/month/export', [OrderStatisticsController::class, 'exportMonth'])
        ->name('admin.revenue_month_export');
    Route::get('products/{product}/variants', [VariantController::class, 'index_by_product'])
        ->name('variants.index_by_product');

    Route::post('variants', [VariantController::class, 'store'])
        ->name('variants.store');

    Route::delete('variants/{variant}', [VariantController::class, 'destroy'])
        ->name('variants.destroy');

    Route::put('variants/{variant}', [VariantController::class, 'update'])
        ->name('variants.update');
});
