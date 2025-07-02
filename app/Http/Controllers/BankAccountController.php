<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $withdrawRequests = WithdrawRequest::with('user', 'bankAccount')->orderByDesc('created_at')->paginate(15);
        return view('admin.account_payment.index', compact('withdrawRequests'));
    }


    public function edit($id)
    {
        $withdrawRequest = WithdrawRequest::findOrFail($id);
        return view('admin.account_payment.edit', compact('withdrawRequest'));
    }

    public function update(Request $request, $id)
    {
        $withdrawRequest = WithdrawRequest::findOrFail($id);
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $withdrawRequest->image = $path;
        }

        $withdrawRequest->status = $request->status;
        $withdrawRequest->save();

        return redirect()->route('admin.bank-accounts.index')->with('success', 'Cập nhật thành công');
    }
}
