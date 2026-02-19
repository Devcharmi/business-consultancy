@extends('admin.layouts.app')
@section('content')
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        {{ isset($userData) ? 'Edit User' : 'Create User' }}
                    </div>
                    <a href="{{ route('user-manager.index') }}" class="btn btn-primary mt-10 d-block text-center">Back</a>
                </div>
                <div class="card-body">
                    <form method="POST" id="user_form" class="mt-6 space-y-6" enctype="multipart/form-data">
                        @if (!empty($userData))
                            @method('PUT')
                        @endif
                        @csrf
                        <!-- Start::row-1 -->
                        @include('admin.user_manager.partials.user-information-form')
                        @include('admin.user_manager.partials.user-password-form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var csrf_token = '{{ csrf_token() }}';
        var index_path = "{{ route('user-manager.index') }}";
    </script>
    <script src="{{ asset('admin/assets/js/custom/user_manager.js') }}"></script>
    <script src="{{ asset('admin/assets/js/custom/user_permission.js') }}"></script>
@endsection
