<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::id());
        return view('profile', compact('user'));
    }

    public function update(Request $request)
    {
        try {
            $user = User::findOrFail(Auth::id());

            // Validate first
            $validator = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'required|string|max:20',
                'address' => 'nullable|string|max:255',
                'birthday' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                try {
                    // Delete old avatar if exists
                    if ($user->avatar) {
                        Storage::disk('public')->delete($user->avatar);
                    }

                    // Store new avatar
                    $avatarPath = $request->file('avatar')->store('avatars', 'public');
                    if (!$avatarPath) {
                        throw new \Exception('Không thể lưu ảnh đại diện');
                    }
                    $user->avatar = $avatarPath;
                    // dd($user->avatar);
                } catch (\Exception $e) {
                    return redirect()->back()
                        ->with('error', 'Lỗi khi tải lên ảnh đại diện: ' . $e->getMessage())
                        ->withInput();
                }
            }

            // Update user information
            $user->full_name = $request->full_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->birthday = $request->birthday;
            $user->gender = $request->gender;

            // Save changes
            $user->save();

            return redirect()->route('user.profile.index')->with('success', 'Thông tin đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }
}
