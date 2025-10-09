<?php

namespace App\Http\Controllers;

use App\Models\appSetting;
use App\Http\Requests\StoreappSettingRequest;
use App\Http\Requests\UpdateappSettingRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\ZaloToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $setting = AppSetting::first(); // Lấy thông tin cài đặt
        if (!$setting) {
            $setting = new AppSetting();
        }
        return view('admin.app_setting.edit', compact('setting'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $request;
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'default_color' => 'nullable|string|regex:/^#([A-Fa-f0-9]{3}){1,2}$/',
            'description' => 'nullable|string',
            'logo_path' => 'nullable|image|max:2048',
            'banner_path' => 'nullable|image|max:4096',
            'favicon_path' => 'nullable|image|max:1024',
            'donated' => 'nullable',
        ]);

        $setting = AppSetting::first() ?? new AppSetting();

        $setting->fill($validated);

        // Xử lý upload file nếu có
        if ($request->hasFile('logo_path')) {
            $setting->logo_path = $request->file('logo_path')->store('uploads/logo', 'public');
        }
        if ($request->hasFile('banner_path')) {
            $setting->banner_path = $request->file('banner_path')->store('uploads/banner', 'public');
        }
        if ($request->hasFile('favicon_path')) {
            $setting->favicon_path = $request->file('favicon_path')->store('uploads/favicon', 'public');
        }

        $setting->save();

        return redirect()->back()->with('success', 'Cập nhật thông tin ứng dụng thành công!');
    }


    /**
     * Display the specified resource.
     */
    public function show(appSetting $appSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(appSetting $appSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // return $request;
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'default_color' => 'nullable|string|regex:/^#([A-Fa-f0-9]{3}){1,2}$/',
            'description' => 'nullable|string',
            'logo_path' => 'nullable|image|max:2048',
            'banner_path' => 'nullable|image|max:4096',
            'favicon_path' => 'nullable|image|max:1024',
            'donated' => 'nullable',
        ]);

        $setting = AppSetting::first() ?? new AppSetting();

        $setting->fill($validated);

        // Xử lý upload file nếu có
        if ($request->hasFile('logo_path')) {
            $setting->logo_path = $request->file('logo_path')->store('uploads/logo', 'public');
        }
        if ($request->hasFile('banner_path')) {
            $setting->banner_path = $request->file('banner_path')->store('uploads/banner', 'public');
        }
        if ($request->hasFile('favicon_path')) {
            $setting->favicon_path = $request->file('favicon_path')->store('uploads/favicon', 'public');
        }

        $setting->save();

        return redirect()->back()->with('success', 'Cập nhật thông tin ứng dụng thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(appSetting $appSetting)
    {
        //
    }


    public function openApp()
    {
        $totalOrders = Order::count();

        // Tính tổng doanh thu từ cột `total`
        $totalRevenue = Order::sum('total');

        $totalUsers = User::count();
        $todayRevenue = Order::whereDate('created_at', Carbon::today())->sum('total');

        // Lấy tổng số sản phẩm
        $totalSuccessfulOrders = Order::where('status', 'completed')->count();

        $days = 7; // 7 ngày gần nhất

        $revenueData = Order::selectRaw('DATE(created_at) as date, SUM(total) as total')
            // ->where('status', 'completed') // hoặc 'paid' tuỳ hệ thống
            ->whereDate('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Tạo mảng dữ liệu: labels (ngày), values (doanh thu)
        $labels = [];
        $values = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();
            $labels[] = $date;

            $dayData = $revenueData->firstWhere('date', $date);
            $values[] = $dayData ? (int) $dayData->total : 0;
        }
        $topProducts = DB::table('order_items')
            ->select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();
        $topUsers = DB::table('users')->where('role', 'user')->orderByDesc('balance')
            ->orderByDesc('created_at')
            ->limit(7)
            ->get();
        $orders = Order::with('items.product', 'referrer')->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")->orderby('status')->paginate(7);
        
        return view('admin.index', compact(
            'totalOrders',
            'totalRevenue',
            'totalUsers',
            'totalSuccessfulOrders',
            'labels',
            'values',
            'todayRevenue',
            'topProducts',
            'topUsers',
            'orders'
        ));
    }
}
