@extends('admin.layouts.app')
@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        {{-- <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">User</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User</li>
                </ol>
            </nav>
            <h1 class="page-title fw-medium fs-18 mb-0">User Manager</h1>
        </div> --}}
    </div>
    <!-- Page Header Close -->
    <form method="POST" id="user_form" class="mt-6 space-y-6" enctype="multipart/form-data">
        @if (!empty($userData))
            @method('PUT')
        @endif
        @csrf
        <!-- Start::row-1 -->
        @include('admin.user_manager.partials.user-information-form')
        @include('admin.user_manager.partials.user-password-form')
    </form>
@endsection
@section('script')
    <script>
        var csrf_token = '{{ csrf_token() }}';
        var index_path = "{{ route('user-manager.index') }}";
    </script>
    <script src="{{ asset('admin/assets/js/custom/user_manager.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom/user_permission.js') }}"></script>
@endsection
