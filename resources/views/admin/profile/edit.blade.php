@extends('admin.layouts.app')
@section('content')
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
