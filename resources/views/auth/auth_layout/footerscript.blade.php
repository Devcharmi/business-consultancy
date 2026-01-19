<!-- Start::custom-scripts -->
<!-- jQuery (required by Toastr) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="{{ asset('admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Show Password JS -->
<script src="{{ asset('admin/assets/js/show-password.js') }}"></script>
<!-- End::custom-scripts -->

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "timeOut": "5000",
        "positionClass": "toast-top-right"
    };

    function showToastr(type, message) {
        if (type == "success") {
            toastr.success(message);
        }
        if (type == "info") {
            toastr.info(message);
        }
        if (type == "error") {
            toastr.error(message);
        }
    }
</script>
<script>
    @if (session('status'))
        showToastr("success", "{{ session('status') }}");
    @endif

    @if (session('info'))
        showToastr("info", "{{ session('info') }}");
    @endif

    @if (session('error'))
        showToastr("error", "{{ session('error') }}");
    @endif

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            showToastr("error", "{{ $error }}");
        @endforeach
    @endif
</script>
