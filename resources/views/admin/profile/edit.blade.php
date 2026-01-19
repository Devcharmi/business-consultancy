@extends('admin.layouts.app')
@section('content')
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        {{-- <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Profile</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </ol>
            </nav>
            <h1 class="page-title fw-medium fs-18 mb-0">Profile</h1>
        </div> --}}
    </div>
    <!-- Page Header Close -->

    <!-- Start::row-1 -->
    @include('admin.profile.partials.update-profile-information-form')
    {{-- @if (auth()->user()->hasRole(['Super Admin', 'Admin'])) --}}
    @include('admin.profile.partials.update-password-form')
    {{-- @endif --}}
    {{-- @include('profile.partials.delete-user-form') --}}
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#profileImageBtn').on('click', function() {
                $('#profileImage').click();
            });
            // Profile Image Preview
            $('#profileImage').change(function(e) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#profilePreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
                $('#removeProfileInput').val(0); // reset remove flag
            });

            // Remove Profile Image
            $('#removeProfile').click(function() {
                $('#profilePreview').attr('src', '');
                $('#profileImage').val('');
                $('#removeProfileInput').val(1); // set remove flag
            });

        });
    </script>
@endsection
