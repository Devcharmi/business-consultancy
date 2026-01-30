<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light"
    data-menu-styles="light" data-toggled="close">

<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->

<head>
    @include('admin.layouts.head')
    @yield('styles')
</head>

<body class="">

    <!-- Loader -->
    <div id="loader">
        <img src="https://php.spruko.com/zeno/zeno/assets/images/media/loader.svg" alt="">
    </div>
    <!-- Loader -->

    <div class="page">

        <!-- Start::main-header -->
        @include('admin.layouts.header')
        <!-- End::main-header -->

        <!-- Start::main-sidebar -->
        @include('admin.layouts.sidebar')
        <!-- End::main-sidebar -->

        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
        <!-- End::app-content -->

        <!-- Start::main-modal -->
        <div id="modal_show_html"></div>
        <div id="sub_modal_show_html"></div>
        <!-- End::main-modal -->

        <!-- Start::main-footer -->
        @include('admin.layouts.footer')
        <!-- End::main-footer -->

    </div>

    <!-- Start::main-scripts -->
    @include('admin.layouts.footerscript')
    @yield('script')
</body>


<!-- Mirrored from php.spruko.com/zeno/zeno/pages/index3.php by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 18 Jun 2025 11:04:30 GMT -->

</html><!-- This code use for render base file -->
