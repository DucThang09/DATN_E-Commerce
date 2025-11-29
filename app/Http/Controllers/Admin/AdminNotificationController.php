<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function latest(Request $request)
    {
        $afterId = (int) $request->query('after_id', 0);

        $notifications = AdminNotification::where('id', '>', $afterId)
            ->orderBy('id', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id'        => $n->id,
                    'type'      => $n->type,             // 'user_registered', 'order_created', ...
                    'title'     => $n->title,
                    'message'   => $n->message,
                    'is_read'   => (bool)$n->is_read,
                    'time_text' => $n->created_at->diffForHumans(), // đã set locale vi trong AppServiceProvider
                    'link_url'  => $n->link_url,
                ];
            });

        $unreadCount = AdminNotification::where('is_read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');

        $query = AdminNotification::query()->orderByDesc('created_at');

        switch ($tab) {
            case 'unread':
                $query->where('is_read', false);
                break;
            case 'read':
                $query->where('is_read', true);
                break;
            case 'order':
                $query->where('type', 'order_created');
                break;
            case 'stock':
                $query->where('type', 'stock_warning');
                break;
            case 'user':
                $query->where('type', 'user_registered');
                break;
        }

        $notifications = $query->paginate(10);
        $unreadCount   = AdminNotification::where('is_read', false)->count();

        if ($request->ajax()) {
            // trả lại partial để JS nhét vào #notifyListWrapper
            return view('admin.notifications._list', compact('notifications', 'tab'))->render();
        }

        return view('admin.notifications.index', compact('notifications', 'unreadCount', 'tab'));
    }



    public function markAllRead(Request $request)
    {
        AdminNotification::where('is_read', false)->update([
            'is_read' => true,
        ]);

        // Nếu là AJAX thì trả JSON, không redirect
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        // Fallback: nếu ai đó truy cập trực tiếp bằng form thường
        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }


    public function markRead(Request $request, $id)
    {
        $noti = AdminNotification::findOrFail($id);

        if (!$noti->is_read) {
            $noti->is_read = true;      // hoặc = 1
            $noti->save();              // NHỚ gọi save()
        }

        // Nếu request là AJAX (fetch)
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
            ]);
        }

        // Nếu ai đó submit form bình thường thì vẫn redirect như cũ
        return back()->with('success', 'Đã đánh dấu đã đọc.');
    }

    public function destroy(Request $request, $id)
    {
        $noti = AdminNotification::findOrFail($id);
        $noti->delete();   // xóa thật trong DB (hoặc soft delete nếu dùng SoftDeletes)

        // Nếu là AJAX (fetch) thì trả JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'id'      => $id,
            ]);
        }

        // Nếu là submit form bình thường thì redirect cũ
        return back()->with('success', 'Đã xóa thông báo.');
    }
}
