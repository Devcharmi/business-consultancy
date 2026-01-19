@extends('admin.layouts.app')
@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        {{-- <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Role</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Role</li>
                </ol>
            </nav>
            <h1 class="page-title fw-medium fs-18 mb-0">Role</h1>
        </div> --}}
    </div>
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18"><a href="{{ route('roles.index') }}"> Roles </a>/ Permissions for
                    Role : {{ $roleData->name }}</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        Role : {{ $roleData->name }}
                    </div>
                    <a href="#" data-url="{{ route('role.show', ['role' => 'new']) }}"
                        class="btn btn-success mt-10 d-block text-center open-modal">+ Add Role</a>
                </div>
                <div class="card-body">
                    <form id="permission_from" method="put">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-3">
                                <h5>Permissions</h5>
                                <div class="form-check pt-3">
                                    <label class="form-check-label" for="formCheck2">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        Select all
                                    </label>
                                </div>
                            </div>
                            <div class="col-9 text-end">
                                <button type="submit" class="btn btn-primary" id="update_permission_button"
                                    data-url="{{ route('give-role-permission', ['role' => $roleData->id]) }}">Update</button>
                            </div>
                        </div>
                        <hr />
                        <div class="row mb-2">
                            @foreach ($permissionData as $permission)
                                <div class="col-sm-3">
                                    <div class="form-check">
                                        <label class="form-check-label" for="formCheck2">
                                            <input class="form-check-input checkbox" type="checkbox" id="formCheck"
                                                name="permission[]" value="{{ $permission->name }}"
                                                {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).on('click', '#selectAll', function() {
            if ($('#selectAll').prop('checked') == true) {
                // alert("hii");
                $('.checkbox').each(function() {
                    $(this).prop('checked', true);
                });
            } else {
                $('.checkbox').each(function() {
                    $(this).prop('checked', false);
                });
            }
        });

        $(document).on('click', '#update_permission_button', function() {
            // var check_validation = $("#permission_from").parsley().validate();
            // if (check_validation) {
            $("#permission_from").ajaxSubmit({
                url: $("#update_permission_button").attr("data-url"),
                type: "PUT",
                dataType: "json",
                header: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                beforeSubmit: function() {},
                success: function(result) {
                    showToastr("success", result.message);
                    setInterval(function() {
                        window.location.href = "{{ route('roles.index') }}";
                    }, 3000);
                    // 
                },
                error: function(result) {
                    $("[id$='_error']").empty();
                    showToastr("error", result.responseJSON.message);
                    $.each(result.responseJSON.errors, function(k, v) {
                        var id_arr = k.split(".");
                        $("body")
                            .find("#permission_from")
                            .find("#" + id_arr[0] + "_error")
                            .text(v);
                    });
                },
            });
            // }
            return false;
        });
    </script>
@endsection
