@extends('admin.layout')

@section('content')
<h1>Cập nhật trạng thái rút tiền</h1>

<form action="{{ route('admin.bank-accounts.update', $withdrawRequest->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="status" class="form-label">Trạng thái</label>
        <select name="status" id="status" class="form-control" required>
            <option value="pending" {{ $withdrawRequest->status == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
            <option value="approved" {{ $withdrawRequest->status == 'approved' ? 'selected' : '' }}>Đã chuyển</option>
            <option value="rejected" {{ $withdrawRequest->status == 'rejected' ? 'selected' : '' }}>Từ chối</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">Ảnh chứng từ chuyển tiền (nếu có)</label>
        <input type="file" name="image" id="image" class="form-control">
        @if ($withdrawRequest->image)
            <p class="mt-2">Đã có ảnh: <br><img src="{{ asset('storage/' . $withdrawRequest->image) }}" width="200"></p>
        @endif
    </div>

    <button type="submit" class="btn btn-success">Cập nhật</button>
</form>
@endsection
