@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <h5 class="card-header">User Management</h5>
            @include('admin.filter')
            <div class="table text-nowrap">
                <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th width="150">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    @foreach($users as $i => $user)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <label class="switch">
                                    <input
                                        type="checkbox"
                                        class="toggle-status"
                                        data-id="{{ $user->id }}"
                                        {{ !empty($user->status) ? 'checked' : '' }}>
                                    <div class="slider">
                                        <div class="circle">
                                            <svg class="cross" xml:space="preserve"
                                                 style="enable-background:new 0 0 512 512"
                                                 viewBox="0 0 365.696 365.696" y="0" x="0" height="6" width="6"
                                                 xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                 xmlns="http://www.w3.org/2000/svg"><g>
                                                    <path data-original="#000000" fill="currentColor"
                                                          d="M243.188 182.86 356.32 69.726c12.5-12.5 12.5-32.766 0-45.247L341.238 9.398c-12.504-12.503-32.77-12.503-45.25 0L182.86 122.528 69.727 9.374c-12.5-12.5-32.766-12.5-45.247 0L9.375 24.457c-12.5 12.504-12.5 32.77 0 45.25l113.152 113.152L9.398 295.99c-12.503 12.503-12.503 32.769 0 45.25L24.48 356.32c12.5 12.5 32.766 12.5 45.247 0l113.132-113.132L295.99 356.32c12.503 12.5 32.769 12.5 45.25 0l15.081-15.082c12.5-12.504 12.5-32.77 0-45.25zm0 0"></path>
                                                </g>
                                            </svg>
                                            <svg class="checkmark" xml:space="preserve"
                                                 style="enable-background:new 0 0 512 512" viewBox="0 0 24 24" y="0"
                                                 x="0" height="10" width="10"
                                                 xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                 xmlns="http://www.w3.org/2000/svg"><g>
                                                    <path class="" data-original="#000000" fill="currentColor"
                                                          d="M9.707 19.121a.997.997 0 0 1-1.414 0l-5.646-5.647a1.5 1.5 0 0 1 0-2.121l.707-.707a1.5 1.5 0 0 1 2.121 0L9 14.171l9.525-9.525a1.5 1.5 0 0 1 2.121 0l.707.707a1.5 1.5 0 0 1 0 2.121z"></path>
                                                </g></svg>
                                        </div>
                                    </div>
                                </label>
                            </td>
                            <td>{{ $user->roles->pluck('name')->implode(', ') ?: '-' }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                        <i class="icon-base bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        {{-- Truyền user sang JS qua JSON --}}
                                        <a class="dropdown-item" href="javascript:void(0)"
                                           onclick='openEditModal(@json($user))'>
                                            <i class="icon-base bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0)"
                                           onclick="deleteUser({{ $user->id }})">
                                            <i class="icon-base bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="userForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="user_id" name="id">

                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3 password-field">
                            <label>Password</label>
                            <input type="password" id="password" name="password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Role</label>
                            <select name="role_id" id="role_id" class="form-select" required>
                                <option value="">-- Select Role --</option>
                                @foreach($roles as $id => $role)
                                    <option value="{{ $id }}">{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let actionType = 'create';

        function openCreateModal() {
            actionType = 'create';
            $('#modalTitle').text('Add User');
            $('#userForm')[0].reset();
            $('#user_id').val('');
            $('.password-field').show();
            $('#userModal').modal('show');
        }

        function openEditModal(user) {
            actionType = 'edit';
            $('#modalTitle').text('Edit User');
            $('#user_id').val(user.id);
            $('#name').val(user.name);
            $('#email').val(user.email);
            $('#password').val('');
            $('.password-field').show();

            const roleId = user.roles.length > 0 ? user.roles[0].id : '';
            $('#role_id').val(roleId);

            $('#userModal').modal('show');
        }

        $('#saveBtn').off('click').on('click', function (e) {
            e.preventDefault();

            const id = $('#user_id').val();
            const formData = $('#userForm').serialize();
            const url = (actionType === 'create') ? '{{ route('users.store') }}' : `/admin/users/${id}`
            const method = (actionType === 'create') ? 'POST' : 'PUT';
            $.ajax({
                url: url,
                type: method,
                data: formData,
                success: function (res) {
                    // toastr.success('User created successfully!');
                    location.reload()
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON?.errors || {};
                    let msg = Object.values(errors).flat().join('<br>') || 'Validation failed';
                    toastr.error(msg);
                }
            });
        });

        function deleteUser(id) {
            if (!confirm('Are you sure you want to delete this user?')) return;

            $.ajax({
                url: `/users/${id}`,
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function (res) {
                    if (res.status) {
                        $(`#user-row-${id}`).remove();
                        toastr.success(res.message);
                    } else {
                        toastr.error(res.message || 'Delete failed');
                    }
                },
                error: function () {
                    toastr.error('Cannot delete user');
                }
            });
        }

        $(document).on('change', '.toggle-status', function (e) {
            e.preventDefault();

            const checkbox = $(this);
            const userId = checkbox.data('id');
            const newStatus = checkbox.is(':checked') ? 1 : 0;

            const oldStatus = !newStatus;

            Swal.fire({
                title: 'Xác nhận thay đổi?',
                text: newStatus ? "Kích hoạt người dùng này?" : "Vô hiệu hóa người dùng này?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/users/${userId}/toggle-status`,
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: newStatus
                        },
                        success: function (res) {
                            if (res.status) {
                                toastr.success(res.message);
                            } else {
                                toastr.error(res.message || 'Cập nhật thất bại');
                                checkbox.prop('checked', oldStatus);
                            }
                        },
                        error: function () {
                            toastr.error('Lỗi kết nối server');
                            checkbox.prop('checked', oldStatus);
                        }
                    });
                } else {
                    checkbox.prop('checked', oldStatus);
                }
            });
        });
    </script>
@endsection
