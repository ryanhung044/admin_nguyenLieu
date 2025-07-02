<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role','!=', 'admin')->get();
        return view('admin.user.index', compact('users'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Xóa tài khoản thành công');
    }

    public function create()
    {
        return view('admin.user.create');
    }

    public function store(Request $request)
    {
        // return $request;
        $request->validate([
            'name' => 'required|unique:users,name|max:255',
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:6',
            'zalo_id' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric|min:0',
            'role' => 'required|in:user,admin',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->zalo_id = $request->zalo_id;
        $user->phone = $request->phone;
        $user->gender = $request->gender;
        $user->birthday = $request->birthday;
        $user->address = $request->address;
        $user->balance = $request->balance ?? 0;
        $user->role = $request->role;

        // Xử lý ảnh đại diện
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $path = $file->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Thêm người dùng thành công!');
    }

    public function edit(User $user)
    {
        return view('admin.user.edit', compact('user'));
    }

    public function editUser()
    {
        $user = Auth::user();
        return view('change_info', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|unique:users,name,' . $user->id,
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'phone' => 'required|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric|min:0',
            'role' => 'required|in:user,admin',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công');
    }

    public function updateUser(Request $request)
    {
        // $user = Auth::user();
        $user = User::where('id', Auth::user()->id)->first();

        $validated = $request->validate([
            'name' => 'required|unique:users,name,' . $user->id,
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'phone' => 'required|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()->route('account.index')->with('success', 'Cập nhật người dùng thành công');
    }
    public function member(Request $request)
    {
        $query = $request->input('q'); // Lấy giá trị tìm kiếm từ URL ?q=xxx

        $users = User::query();

        if ($query) {
            $users->where(function ($q) use ($query) {
                $q->where('full_name', 'like', '%' . $query . '%')
                    ->orWhere('phone', 'like', '%' . $query . '%')
                    ->orWhere('id', $query); // Nếu nhập đúng ID thì tìm theo ID
            });
        }

        $users = $users->get(); // <<< thêm get() vào đây

        $members = $users->map(function ($user) {
            // Tính doanh số cá nhân
            $userSaleCompleted = Order::where('referrer_id', $user->id)
                ->where('status', 'completed') // Chỉ tính đơn đã hoàn thành
                ->sum('total');
            $userSale = Order::where('referrer_id', $user->id)
                ->sum('total');

            // Tính hoa hồng đã hoàn thành
            $commission_completed = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('order_items.referrer_id', $user->id)
                ->where('orders.status', 'completed')
                ->sum('order_items.commission_amount');

            return (object) [
                'id' => $user->id,
                'name' => $user->full_name ?? $user->name,
                'phone' => $user->phone,
                'avatar' => $user->avatar ?? null,
                'joined_at' => $user->created_at,
                'personal_sales_completed' => $userSaleCompleted,
                'personal_sales' => $userSale,
                'commission' => $commission_completed,
                'total_members' => 0,
                'branch_count' => 0,
            ];
        });

        return view('member', compact('members', 'query')); // Truyền query ra view để hiện trong ô tìm kiếm
    }

    public function ambassador()
    {
        return view('ambassador'); // Truyền query ra view để hiện trong ô tìm kiếm

    }

    public function referrer(Request $request)
    {
        $referrerId = $request->query('ref');
        if (!Auth::check()) {
            return redirect()->route('signup', ['ref' => $referrerId]);
        }
        $user = Auth::user();
        $referrers = User::find($referrerId);
        session(['referrer_id' => $referrerId]);
        return view('referrer', compact('referrers', 'user'));
    }
}
