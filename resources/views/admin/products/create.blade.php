@extends('admin.layout')

@section('content')
    <h1 class="mb-4">Thêm mới sản phẩm</h1>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- Cột bên trái - Thông tin cơ bản -->
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="name" class="form-label">Tên sản phẩm</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="summary" class="form-label">Nội dung tóm tắt</label>
                    <textarea class="form-control @error('summary') is-invalid @enderror" id="summary" name="summary">{{ old('summary') }}</textarea>
                    @error('summary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Nội dung chi tiết</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content">{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="sku" class="form-label">Mã sản phẩm</label>
                    <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku"
                        name="sku" value="{{ old('sku') }}">
                    @error('sku')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label">Đường dẫn đến sản phẩm:</label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                        name="slug" value="{{ old('slug') }}">
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="commission_rate" class="form-label">Hoa hồng cho cộng tác viên:</label>
                    <input type="text" class="form-control @error('commission_rate') is-invalid @enderror"
                        id="commission_rate" name="commission_rate" value="{{ old('commission_rate') }}">
                    @error('commission_rate')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="category_id" class="form-label">Danh mục sản phẩm</label>
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                        name="category_id" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="stock" class="form-label">Số lượng:</label>
                    <input type="number" min="0" class="form-control @error('stock') is-invalid @enderror"
                        id="stock" name="stock" value="{{ old('stock') }}">
                    @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- <div class="mb-3">
                    <label for="group_id" class="form-label">Nhóm sản phẩm</label>
                    <select class="form-select @error('group_id') is-invalid @enderror" id="group_id" name="group_id">
                        <option value="">-- Chọn nhóm --</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('group_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="price" class="form-label">Giá niêm yết</label>
                        <input type="number" class="form-control @error('price') is-invalid @enderror" id="price"
                            name="price" value="{{ old('price') }}">
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="sale_price" class="form-label">Giá bán</label>
                        <input type="number" class="form-control @error('sale_price') is-invalid @enderror" id="sale_price"
                            name="sale_price" value="{{ old('sale_price') }}" required>
                        @error('sale_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="thumbnail" class="form-label">Ảnh đại diện</label>
                    <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" id="thumbnail"
                        name="thumbnail">
                    @error('thumbnail')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="images" class="form-label">Ảnh sản phẩm (nhiều ảnh)</label>
                    <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="images"
                        name="images[]" multiple>
                    @error('images.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


            </div>
        </div>
        <!-- Template ẩn -->
        <div id="attribute-template" class="d-none">
            <div class="attribute-row">
                <div class="row mb-3">
                    <div class="col-4">
                        <select id="attributeSelect" class="attribute-name form-select" style="height: 100%">
                            <option value="">--Vui Lòng chọn thuộc tính--</option>
                            @foreach ($attributes as $attribute)
                                <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                            @endforeach
                            <option value="addAttibute">Thuộc tính khác</option>
                        </select>
                    </div>
                    <div class="col-8 d-flex gap-3">
                        <input type="text" name="attribute_value" class="attribute-values form-control"
                            placeholder="Nhập giá trị và enter (vd: S, M, L)">
                        <button type="button" class="btn btn-danger btn-sm remove-attribute">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id="attributes-wrapper"></div>
        <button type="button" class="btn btn-success my-2" id="add-attribute">+ Thêm thuộc tính</button>
        <button type="button" class="btn btn-primary my-2" id="generate-combinations">Sinh tổ hợp</button>
        <table class="table table-bordered mt-3" id="variants-table">
            <thead>
                <tr>
                    <th>Thuộc tính</th>
                    <th>Giá niêm yết</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th>Hình ảnh</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <button type="submit" class="btn btn-success">Lưu sản phẩm</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Quay lại</a>



    </form>
    <div class="modal fade" id="addAttributeModal" tabindex="-1" aria-labelledby="addAttributeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="add-attribute-form" action="{{ route('admin.attributes.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm thuộc tính mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" class="form-control" name="attribute_name"
                            placeholder="Nhập tên thuộc tính">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Thêm</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById("attributeSelect").addEventListener("change", function() {
                const selectedValue = this.value;
                if (selectedValue === "addAttibute") {
                    // Mở modal khi chọn "Thuộc tính khác"
                    var myModal = new bootstrap.Modal(document.getElementById('addAttributeModal'));
                    myModal.show();
                }
            });

            // Thêm thuộc tính mới khi nhấn nút Thêm thuộc tính
            document.getElementById('add-attribute').addEventListener('click', function() {
                const template = document.getElementById('attribute-template').firstElementChild;
                const wrapper = document.getElementById('attributes-wrapper');
                const clone = template.cloneNode(true);

                wrapper.appendChild(clone);

                // Cập nhật sự kiện change cho thuộc tính mới được thêm
                const selectElement = clone.querySelector(".attribute-name");
                selectElement.addEventListener("change", function() {
                    const selectedValue = this.value;
                    if (selectedValue === "addAttibute") {
                        // Mở modal khi chọn "Thuộc tính khác"
                        var myModal = new bootstrap.Modal(document.getElementById(
                            'addAttributeModal'));
                        myModal.show();
                    }
                });
            });

            // Xử lý xóa thuộc tính khi nhấn nút xóa
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-attribute')) {
                    const row = e.target.closest('.attribute-row');
                    if (row) {
                        row.remove();
                    }
                }
            });


            // Sự kiện khi đóng modal mà không thêm thuộc tính mới
            document.querySelector('.btn-close').addEventListener('click', function() {
                // Reset dropdown về "Vui lòng chọn thuộc tính"
                const selects = document.querySelectorAll('.attribute-name');
                selects.forEach(function(select) {
                    select.value = '';
                });
            });
        });

        document.getElementById('generate-combinations').addEventListener('click', function() {
            const attributes = [];

            document.querySelectorAll('.attribute-row').forEach(row => {
                const id = row.querySelector('.attribute-name').value; // attribute ID
                const values = row.querySelector('.attribute-values').value.split(',').map(v => v.trim())
                    .filter(v => v);
                if (id && values.length) {
                    attributes.push({
                        id, // Lưu attribute_id
                        values
                    });
                }
            });

            const combinations = generateCombinations(attributes.map(a => a.values)); // tổ hợp giá trị
            const attributeIds = attributes.map(a => a.id); // giữ thứ tự id theo values

            const tableBody = document.querySelector('#variants-table tbody');
            tableBody.innerHTML = ''; // clear old rows

            combinations.forEach((combo, index) => {
                let hiddenInputs = '';
                combo.forEach((val, i) => {
                    hiddenInputs +=
                        `<input type="hidden" name="variants[${index}][attributes][${i}][attribute_id]" value="${attributeIds[i]}">`;
                    hiddenInputs +=
                        `<input type="hidden" name="variants[${index}][attributes][${i}][value]" value="${val}">`;
                });

                const row = document.createElement('tr');
                row.innerHTML = `
                                <td>${combo.join(', ')}${hiddenInputs}</td>
                                <td><input type="number" name="variants[${index}][price]" class="form-control"></td>
                                <td><input type="number" name="variants[${index}][sale_price]" class="form-control"></td>
                                <td><input type="number" name="variants[${index}][stock]" class="form-control"></td>
                                <td><input type="file" name="variants[${index}][image]" class="form-control"></td>
                            `;
                tableBody.appendChild(row);
            });
        });


        function generateCombinations(arrays) {
            return arrays.reduce((acc, curr) => {
                const result = [];
                acc.forEach(a => {
                    curr.forEach(b => {
                        result.push(a.concat([b]));
                    });
                });
                return result;
            }, [
                []
            ]);
        }
    </script>
    <script>
        ClassicEditor.create(document.querySelector('#content'), {
                ckfinder: {
                    uploadUrl: '{{ route('upload') . '?_token=' . csrf_token() }}'
                }
            })
            .then(editor => {
                editor.editing.view.change(writer => {
                    writer.setStyle('min-height', '300px', editor.editing.view.document.getRoot());
                });
            })
            .catch(error => {
                console.error(error);
            });
    </script>
@endsection
