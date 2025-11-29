<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Status;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Kiểm tra xem người dùng đã đăng nhập
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        // Lấy danh sách đơn hàng + phân trang
        $orders = Order::withSum('items as total_quantity', 'quantity') // 👈 thêm dòng này
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $status = Status::all();

        return view('admin.placed_orders', compact('orders', 'status'));
    }
    public function detailJson(Order $order)
    {
        $order->load('items'); // hasMany OrderItem

        return response()->json([
            'id'          => $order->id,
            'name'        => $order->name,
            'email'       => $order->email,
            'number'      => $order->number,
            'address'     => $order->address,
            'method'      => $order->method,
            'placed_on'   => optional($order->placed_on)->format('H:i d/m/Y'),
            'status'      => $order->payment_status,
            'statusLabel' => $order->payment_status === 'pending' ? 'Chờ xác nhận' : 'Hoàn tất',
            'total'       => number_format($order->total_price, 0, ',', '.') . '₫',

            'items'       => $order->items->map(function ($item) {
                return [
                    'product_name'  => $item->product_name,
                    'quantity'      => $item->quantity,
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
        $names = preg_split('/\s*-\s*/', $request->total_products);
        $productDetails = [];

        foreach ($names as $product) {
            // Sử dụng preg_match để lấy tên sản phẩm và số lượng
            if (preg_match('/(.*)\s*\((\d+)\s*x\s*(\d+)\)$/', $product, $matches)) {
                // Lưu tên sản phẩm và số lượng vào mảng
                $productDetails[] = [
                    'name' => trim($matches[1]),   // Tên sản phẩm
                    'price' => $matches[2],        // Giá
                    'quantity' => $matches[3]      // Số lượng
                ];
            }
        }


        if ($request->payment_status == 'completed') {
            foreach ($productDetails as $item) {
                // Tìm sản phẩm trong bảng `products` theo tên
                $product = Product::where('name', $item['name'])->first();

                // Kiểm tra nếu sản phẩm tồn tại
                if ($product) {
                    // Kiểm tra nếu inventory đủ để trừ
                    if ($product->inventory >= $item['quantity']) {
                        // Trừ số lượng từ inventory
                        $product->inventory -= $item['quantity'];
                        $product->qty_sold += $item['quantity'];
                        $product->revenue += ($item['price'] * $item['quantity']);
                        $product->save(); // Lưu thay đổi vào cơ sở dữ liệu
                        echo "Đã trừ " . $item['quantity'] . " từ kho của sản phẩm: " . $item['name'] . "\n";
                    } else {
                        echo "Sản phẩm " . $item['name'] . " không đủ hàng trong kho.\n";
                    }
                } else {
                    echo "Không tìm thấy sản phẩm " . $item['name'] . " trong cơ sở dữ liệu.\n";
                }
            }
        }


        $order = Order::findOrFail($request->order_id);
        $order->payment_status = $request->payment_status;
        $order->save();


        return redirect()->route('admin.placed_orders')->with('message', 'Payment status updated!');
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
            ->withSum('items as total_quantity', 'quantity') // nếu bạn đang dùng
            ->orderByDesc('id')                              // 👈 thêm dòng này
            ->paginate(10);

        $status = Status::all();

        return view('admin.placed_orders', compact('orders', 'status'));
    }
}
