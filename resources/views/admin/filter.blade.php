<form action="{{ route('users.index') }}">
    <div class="row mt-2 mb-4 mx-4">
        <div class="form-wrap col-2">
            <select class="form-control" name="status">
                <option value="-1" selected>Chọn trạng thái</option>
                <option {{( "1" === request()->get('status')) ? 'selected' : '' }} value="1">ACTIVE</option>
                <option {{( "0" === request()->get('status')) ? 'selected' : '' }} value="0">INACTIVE</option>
            </select>
        </div>
        <div class="form-wrap col-3">
            <input class="form-control" value="{{request()->get('email') ?? null}}" placeholder="email" name="email">
        </div>
        <div class="form-wrap col-3">
            <input class="form-control" type="text" name="name" value="{{ request('name') }}" placeholder="Tên">
        </div>
{{--        <div class="form-wrap col-4">--}}
{{--            <div class="form-control-wrap">--}}
{{--                <div class="input-daterange date-picker-range input-group">--}}
{{--                    <input name="start_time" type="text" placeholder="Ngày tạo từ" class="form-control"--}}
{{--                           value="{{request()->get('start_time')}}"/>--}}
{{--                    <div class="input-group-addon">TO</div>--}}
{{--                    <input name="end_time" type="text" placeholder="Đến" class="form-control"--}}
{{--                           value="{{request()->get('end_time')}}"/></div>--}}
{{--            </div>--}}
{{--        </div>--}}
        <div class="btn-wrap col-1">
            <button type="submit" class="btn btn-round btn-primary">
                <em class="icon ni ni-search"></em><span>Search</span>
            </button>
        </div>
        <div class="btn-wrap col-2">
            <button type="button" class="btn btn-round btn-success" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openCreateModal()">
                <em class="icon ni ni-search"></em><span>Add User</span>
            </button>
        </div>
    </div>
{{--    <div class="row mt-2 mb-4 mx-4">--}}
{{--        <div class="btn-wrap col-1">--}}
{{--            <button type="submit" class="btn btn-round btn-primary">--}}
{{--                <em class="icon ni ni-search"></em><span>Search</span>--}}
{{--            </button>--}}
{{--        </div>--}}
{{--        <div class="btn-wrap col-2">--}}
{{--            <button type="button" class="btn btn-round btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openCreateModal()">--}}
{{--                <em class="icon ni ni-search"></em><span>Add User</span>--}}
{{--            </button>--}}
{{--        </div>--}}
{{--    </div>--}}
</form>

