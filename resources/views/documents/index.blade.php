@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span>Document Management</span>
                <button class="btn btn-sm btn-outline-secondary" id="btnSettings" type="button">
                    <i class="bx bx-cog me-1"></i> Settings Config Files
                </button>
            </h5>
            @include('documents.filter')
            <div class="table table-responsive text-nowrap">
                <table class="table table-bordered">
                    <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên công văn</th>
                        <th>Tiêu đề</th>
                        <th>Người tạo</th>
                        <th>Type</th>
                        <th>Files</th>
                        @if(auth()->user()->hasRole('admin|document'))
                            <th>Actions</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($documents as $doc)
                        <tr id="docRow{{ $doc->id }}">
                            <td>{{ $doc->id }}</td>
                            <td>{{ $doc->name }}</td>
                            <td>{{ $doc->title }}</td>
                            <td>{{ $doc->users->name ?? $doc->user_id }}</td>
                            <td>{{ (!empty($doc->type) && $doc->type == 1 ) ? "Giáo viên" : (( $doc->type == 2)  ?"Học sinh" : "" )}}</td>
                            <td>
                                @foreach($doc->files as $file)
                                    @php
                                        $url = asset('storage/'.$file->path);
                                    @endphp
                                    @if(in_array($file->type, ['pdf','jpg','jpeg','png']))
                                        <button class="btn btn-info btn-sm mb-1" type="button"
                                                onclick="viewFile('{{ $url }}','{{ $file->name }}')">
                                            {{ $file->name }}
                                        </button>
                                    @else
                                        <a href="{{ $url }}" class="btn btn-secondary btn-sm mb-1"
                                           download="{{ $file->name }}">
                                            {{ $file->name }}
                                        </a>
                                    @endif
                                @endforeach
                            </td>
                            @if(auth()->user()->hasRole('admin|document'))
                                <td>
                                    <button class="btn btn-warning btn-sm editDocBtn" data-id="{{ $doc->id }}">Sửa
                                    </button>
                                    <button class="btn btn-danger btn-sm deleteDocBtn" data-id="{{ $doc->id }}">Xóa
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal tạo công văn --}}
    <div class="modal fade" id="documentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="documentForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Thêm Công Văn</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Tên Công văn</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Nội dung</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Type</label>
                            <select name="type" class="form-control">
                                <option value="2">Học Sinh</option>
                                <option value="1">Giáo viên</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Files (PDF, DOC, DOCX, XLS, XLSX, JPG, PNG)</label>
                            <input type="file" name="files[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                   multiple class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal xem file --}}
    <div class="modal fade" id="viewFileModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewFileTitle">Xem File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <iframe id="fileFrame" src="" frameborder="0" width="100%" height="600px"></iframe>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal cấu hình dung lượng upload --}}
    <div class="modal fade" id="uploadSettingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="uploadSettingForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Cấu hình Upload File</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="max_file_size_mb" class="form-label">Dung lượng tối đa (MB)</label>
                            <input type="number" name="max_file_size_mb" id="max_file_size_mb"
                                   class="form-control" min="1" max="500" required>
                        </div>
                        <div class="mb-3">
                            <label for="allowed_types" class="form-label">Loại file cho phép (phân cách bằng dấu phẩy)</label>
                            <input type="text" name="allowed_types" id="allowed_types" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu cấu hình</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const uploadUrl = "{{ route('documents.store') }}";
        const updateUrlTemplate = "admin/documents/ID_PLACEHOLDER";
        const deleteDocUrlTemplate = "admin/documents/ID_PLACEHOLDER";
        const csrfToken = "{{ csrf_token() }}";

        function viewFile(url, name) {
            $('#viewFileTitle').text(name);
            $('#fileFrame').attr('src', url);
            $('#viewFileModal').modal('show');
        }

        $('#documentForm').submit(function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            const id = $(this).attr('data-id'); // có id => đang sửa

            let ajaxUrl = uploadUrl;
            let ajaxMethod = 'POST';

            if (id) {
                ajaxUrl = updateUrlTemplate.replace('ID_PLACEHOLDER', id);
                ajaxMethod = 'POST';
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: ajaxUrl,
                type: ajaxMethod,
                data: formData,
                contentType: false,
                processData: false,
                success: function (res) {
                    location.reload();
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    location.reload()
                }

            });
        });

        function openCreateModal() {
            $('#documentForm')[0].reset();
            $('#documentForm').removeAttr('data-id');
            $('[name="files[]"]').attr('required', true);
            $('#modalTitle').text('Thêm Công Văn');
            $('#documentModal').modal('show');
        }


        // Xóa công văn
        $(document).on('click', '.deleteDocBtn', function () {
            if (!confirm('Bạn có chắc muốn xóa?')) return;
            const id = $(this).data('id');
            const deleteUrl = deleteDocUrlTemplate.replace('ID_PLACEHOLDER', id);

            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                data: {_token: csrfToken},
                success: function (res) {
                    location.reload();
                },
                error: function () {
                    alert('Lỗi server!');
                }
            });
        });

        $(document).on('click', '.editDocBtn', function () {
            const id = $(this).data('id');
            const editUrl = `/admin/documents/${id}/edit`;

            $.get(editUrl, function (res) {
                if (res.status === 'success') {
                    $('#modalTitle').text('Sửa Công Văn');
                    $('[name="name"]').val(res.data.name);
                    $('[name="title"]').val(res.data.title);
                    $('[name="type"]').val(res.data.type);

                    $('[name="files[]"]').removeAttr('required');

                    $('#documentForm').attr('data-id', id);

                    $('#documentModal').modal('show');
                } else {
                    alert(res.message || 'Không thể tải dữ liệu công văn!');
                }
            }).fail(function () {
                alert('Lỗi server khi tải dữ liệu!');
            });
        });

        const getSettingUrl = "{{ route('upload.settings.get') }}";
        const updateSettingUrl = "{{ route('upload.settings.update') }}";

        $('button:contains("Settings Config Files")').on('click', function() {
            $.get(getSettingUrl, function(res) {
                if (res.status === 'success') {
                    $('#max_file_size_mb').val(res.data.max_file_size_mb);
                    $('#allowed_types').val(res.data.allowed_types);
                    $('#uploadSettingModal').modal('show');
                }
            });
        });

        $('#uploadSettingForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: updateSettingUrl,
                type: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire('Thành công', res.message, 'success');
                        $('#uploadSettingModal').modal('hide');
                    }
                },
                error: function(err) {
                    Swal.fire('Lỗi', 'Không thể cập nhật cấu hình!', 'error');
                    console.error(err.responseText);
                }
            });
        });
    </script>
@endsection
