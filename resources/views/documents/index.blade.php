@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                Quản lý Công Văn
                <button class="btn btn-primary btn-sm" type="button" onclick="openCreateModal()">Thêm Công Văn</button>
            </h5>

            <div class="table-responsive">
                <table class="table table-bordered mt-3" id="documentsTable">
                    <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Người tạo</th>
                        <th>Type</th>
                        <th>Files</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($documents as $doc)
                        <tr id="docRow{{ $doc->id }}">
                            <td>{{ $doc->id }}</td>
                            <td>{{ $doc->title }}</td>
                            <td>{{ $doc->users->name ?? $doc->user_id }}</td>
                            <td>{{ $doc->type }}</td>
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
                                        <a href="{{ $url }}" class="btn btn-secondary btn-sm mb-1" download="{{ $file->name }}">
                                            {{ $file->name }}
                                        </a>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                <button class="btn btn-danger btn-sm deleteDocBtn" data-id="{{ $doc->id }}">Xóa</button>
                            </td>
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
                            <input type="text" name="type" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Files (PDF, DOC, DOCX, XLS, XLSX, JPG, PNG)</label>
                            <input type="file" name="files[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple class="form-control" required>
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
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const uploadUrl = "{{ route('documents.store') }}";
        const deleteDocUrlTemplate = "/documents/ID_PLACEHOLDER";
        const csrfToken = "{{ csrf_token() }}";

        // Mở modal tạo công văn
        function openCreateModal(){
            $('#documentForm')[0].reset();
            $('#documentModal').modal('show');
        }

        // Xem file PDF/Ảnh
        function viewFile(url, name){
            $('#viewFileTitle').text(name);
            $('#fileFrame').attr('src', url);
            $('#viewFileModal').modal('show');
        }

        // Upload công văn + files
        $('#documentForm').submit(function(e){
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: uploadUrl,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res){
                    if(res.status === 'success'){
                        location.reload(); // Reload để cập nhật table
                    } else {
                        alert(res.message || 'Upload thất bại!');
                    }
                },
                error: function(){ alert('Lỗi server!'); }
            });
        });

        // Xóa công văn
        $(document).on('click', '.deleteDocBtn', function(){
            if(!confirm('Bạn có chắc muốn xóa?')) return;
            const id = $(this).data('id');
            const deleteUrl = deleteDocUrlTemplate.replace('ID_PLACEHOLDER', id);

            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                data: {_token: csrfToken},
                success: function(res){
                    if(res.status === 'success'){
                        location.reload();
                    } else {
                        alert(res.message || 'Xóa thất bại!');
                    }
                },
                error: function(){ alert('Lỗi server!'); }
            });
        });
    </script>
@endsection
