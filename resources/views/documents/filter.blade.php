<form action="{{ route('documents.index') }}">
    <div class="row mt-2 mb-4 mx-4">
        @if(auth()->user()->hasRole('admin|teacher'))
        <div class="form-wrap col-3">
            <select class="form-control" name="type">
                <option value="-1" {{( null === request()->get('type')) ? 'selected' : '' }}>Chọn role</option>
                <option {{( "1" === request()->get('type')) ? 'selected' : '' }} value="1">Giáo Viên</option>
                <option {{( "2" === request()->get('type')) ? 'selected' : '' }} value="2">Học Sinh</option>
            </select>
        </div>
        @endif
        <div class="form-wrap col-3">
            <input class="form-control" type="text" name="files_name" value="{{ request('files_name') }}" placeholder="Tên file">
        </div>
        <div class="form-wrap col-3">
            <input class="form-control" type="text" name="name" value="{{ request('name') }}" placeholder="Tên công văn">
        </div>
        <div class="form-wrap col-3">
            <input class="form-control" type="text" name="users_name" value="{{ request('users_name') }}" placeholder="Người tạo">
        </div>
    </div>
        <div class="row mt-2 mb-4 mx-4">
            <div class="btn-wrap col-1">
                <button type="submit" class="btn btn-round btn-primary">
                    <em class="icon ni ni-search"></em><span>Search</span>
                </button>
            </div>
            @if(auth()->user()->hasRole('admin|teacher'))
            <div class="btn-wrap col-2">
                <button class="btn btn-round btn-success" type="button" onclick="openCreateModal()">Thêm Công Văn</button>
            </div>
            @endif
        </div>
</form>

