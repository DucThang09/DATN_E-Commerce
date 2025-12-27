<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Status;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }
        $orders = Order::withSum('items as total_quantity', 'quantity')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $status = Status::all();

        return view('admin.placed_orders', compact('orders', 'status'));
    }
    public function detailJson(Order $order)
    {
        $order->load('items', 'orderStatus');

        return response()->json([
            'id'          => $order->id,
            'order_code'  => $order->order_code,
            'name'        => $order->name,
            'email'       => $order->email,
            'number'      => $order->number,
            'address'     => $order->address,
            'method'      => $order->method,
            'placed_on'   => optional($order->placed_on)->format('H:i d/m/Y'),
            'status' => $order->status_id,
            'statusLabel' => optional($order->orderStatus)->status ?? 'pending',
            'total'       => number_format($order->total_price, 0, ',', '.') . '₫',

            'items' => $order->items->map(function ($item) {
                $colorName = null;

                if (!empty($item->colorProduct_id)) {
                    $colorName = DB::table('color')
                        ->where('colorProduct_id', $item->colorProduct_id)
                        ->value('colorProduct');
                }

                return [
                    'product_name'  => $item->product_name,
                    'quantity'      => $item->quantity,
                    'color'         => $colorName, // ✅ thêm
                    'unit_price'    => number_format($item->unit_price, 0, ',', '.') . '₫',
                    'total_price'   => number_format($item->total_price, 0, ',', '.') . '₫',
                ];
            }),
        ]);
    }

    public function updatePayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'payment_status' => 'required|string',
        ]);

        $orderId   = (int) $request->order_id;
        $newStatus = trim($request->payment_status);

        // ✅ CHỈNH Ở ĐÂY: status nào coi như "đã trừ kho"
        $deductedStatuses = ['pending', 'completed'];
        // nếu bạn trừ kho từ "paid" thì thêm 'paid'

        // ✅ CHỈNH Ở ĐÂY: status nào coi như "hủy"
        $cancelStatuses = ['cancelled', 'canceled', 'cancel']; // tùy UI gửi gì

        try {
            DB::transaction(function () use ($orderId, $newStatus, $deductedStatuses, $cancelStatuses) {

                // ✅ lock order row
                $order = Order::with('items')
                    ->where('id', $orderId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $oldStatus = (string) $order->payment_status;

                if ($oldStatus === $newStatus) {
                    return;
                }

                $oldDeducted = in_array($oldStatus, $deductedStatuses, true);
                $newDeducted = in_array($newStatus, $deductedStatuses, true);

                $isCancel = in_array($newStatus, $cancelStatuses, true);

                // ===== TRỪ KHO: khi chuyển từ chưa trừ -> đã trừ
                if ($newDeducted && !$oldDeducted) {
                    foreach ($order->items as $item) {
                        $variantId = $item->variant_id ?: null;

                        if (!$variantId && !empty($item->product_id) && !empty($item->colorProduct_id)) {
                            $variantId = DB::table('product_variants')
                                ->where('product_id', $item->product_id)
                                ->where('colorProduct_id', $item->colorProduct_id)
                                ->value('id');
                        }

                        if (!$variantId && !empty($item->product_id)) {
                            $cnt = DB::table('product_variants')->where('product_id', $item->product_id)->count();
                            if ($cnt === 1) {
                                $variantId = DB::table('product_variants')->where('product_id', $item->product_id)->value('id');
                            }
                        }

                        if (!$variantId) {
                            throw new \Exception("Item '{$item->product_name}' chưa có variant_id.");
                        }

                        $variant = DB::table('product_variants')
                            ->where('id', $variantId)
                            ->lockForUpdate()
                            ->first();

                        if (!$variant) {
                            throw new \Exception("Không tìm thấy variant_id={$variantId} cho '{$item->product_name}'.");
                        }

                        if ((int)$variant->inventory < (int)$item->quantity) {
                            throw new \Exception("Không đủ tồn kho cho '{$item->product_name}' (cần {$item->quantity}, còn {$variant->inventory}).");
                        }

                        DB::table('product_variants')
                            ->where('id', $variantId)
                            ->update([
                                'inventory' => (int)$variant->inventory - (int)$item->quantity,
                                'qty_sold'  => (int)$variant->qty_sold + (int)$item->quantity,
                            ]);
                    }
                }

                // ===== HOÀN KHO: khi đơn đã trừ kho và bây giờ bị hủy (hoặc chuyển về trạng thái chưa trừ)
                if ($oldDeducted && $isCancel && !in_array($oldStatus, $cancelStatuses, true)) {
                    foreach ($order->items as $item) {
                        $variantId = $item->variant_id ?: null;

                        if (!$variantId && !empty($item->product_id) && !empty($item->colorProduct_id)) {
                            $variantId = DB::table('product_variants')
                                ->where('product_id', $item->product_id)
                                ->where('colorProduct_id', $item->colorProduct_id)
                                ->value('id');
                        }

                        if (!$variantId && !empty($item->product_id)) {
                            $cnt = DB::table('product_variants')->where('product_id', $item->product_id)->count();
                            if ($cnt === 1) {
                                $variantId = DB::table('product_variants')->where('product_id', $item->product_id)->value('id');
                            }
                        }

                        if (!$variantId) {
                            throw new \Exception("Không xác định được variant để hoàn kho cho '{$item->product_name}'.");
                        }

                        $variant = DB::table('product_variants')
                            ->where('id', $variantId)
                            ->lockForUpdate()
                            ->first();

                        if (!$variant) {
                            throw new \Exception("Không tìm thấy variant_id={$variantId} để hoàn kho.");
                        }

                        $newInv  = (int)$variant->inventory + (int)$item->quantity;
                        $newSold = max(0, (int)$variant->qty_sold - (int)$item->quantity);

                        DB::table('product_variants')
                            ->where('id', $variantId)
                            ->update([
                                'inventory' => $newInv,
                                'qty_sold'  => $newSold,
                            ]);
                    }
                }

                // map payment_status -> status_id (theo bảng status của bạn)
                $mapStatusId = [
                    'pending'   => 4, // Chờ xác nhận
                    'completed' => 5, // Đã giao / hoàn thành
                    'canceled'  => 6, // Đã hủy
                    'cancelled' => 6,
                    'cancel'    => 6,
                ];

                $order->payment_status = $newStatus;

                // ✅ đồng bộ status_id để trang lịch sử đọc đúng
                if (isset($mapStatusId[$newStatus])) {
                    $order->status_id = $mapStatusId[$newStatus];
                }

                $order->save();
            });

            return redirect()->route('admin.placed_orders')->with('message', 'Payment status updated!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.placed_orders')->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->route('admin.placed_orders')->with('message', 'Order deleted successfully!');
    }

    public function search_order(Request $request)
    {
        $query = Order::query();

        if ($request->has('search_type') && $request->has('search_value')) {
            if ($request->search_type == 'name') {
                $query->whereHas('user', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search_value . '%');
                });
            } elseif ($request->search_type == 'id') {
                $query->where('id', $request->search_value);
            } elseif ($request->search_type == 'date') {
                $query->whereDate('created_at', $request->search_value);
            } elseif ($request->search_type == 'month') {
                $query->whereMonth('created_at', $request->search_value);
            } elseif ($request->search_type == 'year') {
                $query->whereYear('created_at', $request->search_value);
            }
        }

        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query
            ->withSum('items as total_quantity', 'quantity')
            ->orderByDesc('id')
            ->paginate(10);
        $status = Status::all();
        return view('admin.placed_orders', compact('orders', 'status'));
    }
}
