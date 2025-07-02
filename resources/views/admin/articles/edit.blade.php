@extends('admin.layout')

@section('content')
    <h1 class="mb-4">Chỉnh sửa bài viết</h1>

    <form action="{{ route('admin.articles.update', $article->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') <!-- This is important for the PUT request when updating -->

        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="title" class="form-label">Tiêu đề bài viết</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                        name="title" value="{{ old('title', $article->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="summary" class="form-label">Tóm tắt nội dung</label>
                    <textarea class="form-control @error('summary') is-invalid @enderror" id="summary" name="summary">{{ old('summary', $article->summary) }}</textarea>
                    @error('summary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Nội dung chi tiết</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content">{{ old('content', $article->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label for="slug" class="form-label">Đường dẫn (slug)</label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                        name="slug" value="{{ old('slug', $article->slug) }}">
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Chuyên mục</label>
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                        name="category_id" required>
                        <option value="">-- Chọn chuyên mục --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $article->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="published_at" class="form-label">Thời gian đăng</label>
                    <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror"
                        id="published_at" name="published_at" value="{{ old('published_at', \Carbon\Carbon::parse($article->published_at)->format('Y-m-d\TH:i')) }}">
                    @error('published_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Ảnh đại diện</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                        name="image">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if ($article->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $article->image) }}" alt="Article Image" width="150">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Cập nhật bài viết</button>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor.create(document.querySelector('#content'), {
            ckfinder: {
                uploadUrl: '{{ route('upload') . '?_token=' . csrf_token() }}'
            }
        }).then(editor => {
            editor.editing.view.change(writer => {
                writer.setStyle('min-height', '300px', editor.editing.view.document.getRoot());
            });
        }).catch(error => {
            console.error(error);
        });
    </script>
@endsection
