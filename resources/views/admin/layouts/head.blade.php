<!-- Meta Data -->
<meta charset="UTF-8">
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
<meta name="Author" content="Spruko Technologies Private Limited">
<meta name="keywords"
    content="admin dashboard in php, admin panel bootstrap template, admin template php, best php framework, bootstrap and php, bootstrap php, bootstrap template, panel admin php, php admin, php admin panel template, php components, php templates, php ui, template admin php, template php">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Title -->
<title> {{ config('app.name', '') }} </title>

<!-- Favicon -->
<link rel="icon" href="{{ asset('admin/assets/images/brand-logos/logo-icon.png') }}" type="image/x-icon">

<!-- Start::Styles -->

<!-- Choices JS -->
<script src="{{ asset('admin/assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

<!-- Main Theme Js -->
<script src="{{ asset('admin/assets/js/main.js') }}"></script>

<!-- Bootstrap Css -->
<link id="style" href="{{ asset('admin/assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

<!-- Style Css -->
<link href="{{ asset('admin/assets/css/styles.css') }}" rel="stylesheet">

<!-- Icons Css -->
{{-- <link href="{{ asset('admin/assets/css/icons.css') }}" rel="stylesheet"> --}}

<!-- Node Waves Css -->
<link href="{{ asset('admin/assets/libs/node-waves/waves.min.css') }}" rel="stylesheet">

<!-- Simplebar Css -->
{{-- <link href="{{ asset('admin/assets/libs/simplebar/simplebar.min.css') }}" rel="stylesheet"> --}}

<!-- Color Picker Css -->
<link rel="stylesheet" href="{{ asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/libs/%40simonwep/pickr/themes/nano.min.css') }}">

<!-- Choices Css -->
<link rel="stylesheet" href="{{ asset('admin/assets/libs/choices.js/public/assets/styles/choices.min.css') }}">

<!-- FlatPickr CSS -->
<link rel="stylesheet" href="{{ asset('admin/assets/libs/flatpickr/flatpickr.min.css') }}">

<!-- Auto Complete CSS -->
<link rel="stylesheet" href="{{ asset('admin/assets/libs/%40tarekraafat/autocomplete.js/css/autoComplete.css') }}">
<!-- End::Styles -->

<!-- DataTables CSS -->
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
 --}}
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css"
    integrity="sha512-kJlvECunwXftkPwyvHbclArO8wszgBGisiLeuDFwNM8ws+wKIw0sv1os3ClWZOcrEB2eRXULYUsm8OVRGJKwGA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">


<link href="https://unpkg.com/@tabler/icons-webfont@2.11.0/tabler-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Daterangepicker CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-daterangepicker@3.0.3/daterangepicker.css" />
@section('styles')
    <style>
        #location_results a:hover {
            background: #f3f4f6;
        }
    </style>
@endsection
