    <!-- Scroll To Top -->
    <div class="scrollToTop">
        <span class="arrow"><i class="ti ti-arrow-narrow-up fs-20"></i></span>
    </div>
    <div id="responsive-overlay"></div>
    <!-- Scroll To Top -->
    <!-- jQuery (required by Toastr) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery Form Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>

    <!-- Moment.js -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <!-- Popper JS -->
    <script src="{{ asset('admin/assets/libs/%40popperjs/core/umd/popper.min.js') }}"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Defaultmenu JS -->
    <script src="{{ asset('admin/assets/js/defaultmenu.min.js') }}"></script>

    <!-- Node Waves JS-->
    <script src="{{ asset('admin/assets/libs/node-waves/waves.min.js') }}"></script>

    <!-- Sticky JS -->
    <script src="{{ asset('admin/assets/js/sticky.js') }}"></script>

    <!-- Simplebar JS -->
    {{-- <script src="{{ asset('admin/assets/libs/simplebar/simplebar.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin/assets/js/simplebar.js') }}"></script> --}}

    <!-- Auto Complete JS -->
    {{-- <script src="{{ asset('admin/assets/libs/%40tarekraafat/autocomplete.js/autoComplete.min.js') }}"></script> --}}

    <!-- Color Picker JS -->
    <script src="{{ asset('admin/assets/libs/%40simonwep/pickr/pickr.es5.min.js') }}"></script>

    <!-- Date & Time Picker JS -->
    <script src="{{ asset('admin/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <!-- End::main-scripts -->

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

    <!-- Apex Charts JS -->
    {{-- <script src="{{ asset('admin/assets/libs/apexcharts/apexcharts.min.js') }}"></script> --}}

    <!-- Echarts JS -->
    {{-- <script src="{{ asset('admin/assets/libs/echarts/echarts.min.js') }}"></script> --}}

    <!-- Ecommerce Dashboard -->
    {{-- <script src="{{ asset('admin/assets/js/ecommerce-dashboard.js') }}"></script> --}}


    <!-- Custom JS -->
    <script src="{{ asset('admin/assets/js/custom.js') }}"></script>

    <!-- Custom-Switcher JS -->
    {{-- <script src="{{ asset('admin/assets/js/custom-switcher.min.js') }}"></script> --}}

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Daterangepicker CSS & JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-daterangepicker@3.0.3/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script
        src="{{ asset('admin/assets/js/ckeditor-init.js') }}?v={{ filemtime(public_path('admin/assets/js/ckeditor-init.js')) }}">
    </script>
    <script>
        var userRoles = @json(auth()->user()->getRoleNames()); // array of role names

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

        $(".select2").select2({
            placeholder: "Select...",
            width: "100%",
            minimumResultsForSearch: 0
        });

        $(document).on('select2:open', function() {
            setTimeout(function() {
                const searchInput = document.querySelector(
                    '.select2-container--open .select2-search__field');
                if (searchInput) searchInput.focus();
            }, 10);
        });

        // // Initialize date range picker
        $('.date-range').daterangepicker({
            autoUpdateInput: false, // keeps input blank
            opens: 'left',
            autoApply: false, // user must manually apply
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                    'month')]
            }
        }, function(start, end, label) {
            // Don't pre-select any range unless user chooses
        });

        // When user applies a date range
        $('.date-range').on('apply.daterangepicker', function(ev, picker) {
            // Match backend format: "YYYY-MM-DD - YYYY-MM-DD"
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
            $(this).trigger('change'); // trigger filter
        });

        // When user cancels
        $('.date-range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $(this).trigger('change');
        });

        // Function to set daterangepicker to today
        function setDateRangeToday() {
            let today = moment();
            let picker = $('.date-range').data('daterangepicker');
            picker.setStartDate(today);
            picker.setEndDate(today);
            $('.date-range').val(today.format('DD-MM-YYYY') + ' - ' + today.format('DD-MM-YYYY'));
        }

        function toggleTodayTab() {
            let dateRange = $('.date-range').val() || ""; // prevents undefined
            let todayTab = $('a[data-status="today"]').closest('li');
            let allTab = $('a[data-status="all"]');

            if (dateRange.trim() !== "") {
                // Hide Today tab
                todayTab.hide();

                // Activate ALL tab
                $('#taskTabs .nav-link').removeClass('active');
                allTab.addClass('active');
            } else {
                // Show Today tab
                todayTab.show();

                // Activate Today tab
                $('#taskTabs .nav-link').removeClass('active');
                $('a[data-status="today"]').addClass('active');
            }
        }
    </script>
    <script>
        // // Initialize date range picker
        $('.date-range').daterangepicker({
            autoUpdateInput: false, // keeps input blank
            opens: 'left',
            autoApply: false, // user must manually apply
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                    'month')]
            }
        }, function(start, end, label) {
            // Don't pre-select any range unless user chooses
        });

        // When user applies a date range
        $('.date-range').on('apply.daterangepicker', function(ev, picker) {
            // Match backend format: "YYYY-MM-DD - YYYY-MM-DD"
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
            $(this).trigger('change'); // trigger filter
        });

        // When user cancels
        $('.date-range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $(this).trigger('change');
        });

        // Function to set daterangepicker to today
        function setDateRangeToday() {
            let today = moment();
            let picker = $('.date-range').data('daterangepicker');
            picker.setStartDate(today);
            picker.setEndDate(today);
            $('.date-range').val(today.format('DD-MM-YYYY') + ' - ' + today.format('DD-MM-YYYY'));
        }

        function toggleTodayTab() {
            let dateRange = $('.date-range').val() || ""; // prevents undefined
            let todayTab = $('a[data-status="today"]').closest('li');
            let allTab = $('a[data-status="all"]');

            if (dateRange.trim() !== "") {
                // Hide Today tab
                todayTab.hide();

                // Activate ALL tab
                $('#taskTabs .nav-link').removeClass('active');
                allTab.addClass('active');
            } else {
                // Show Today tab
                todayTab.show();

                // Activate Today tab
                $('#taskTabs .nav-link').removeClass('active');
                $('a[data-status="today"]').addClass('active');
            }
        }
    </script>
    <script>
        // 🔥 Global stacked modal handler (SweetAlert-like behavior)
        $(document).on("show.bs.modal", ".modal", function() {
            const zIndex = 1050 + 10 * $(".modal.show").length;
            $(this).css("z-index", zIndex);

            setTimeout(() => {
                $(".modal-backdrop")
                    .not(".modal-stack")
                    .first()
                    .css("z-index", zIndex - 1)
                    .addClass("modal-stack");
            }, 0);
        });

    </script>
