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
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

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
    <script>
        const REPORT_TABLE_DOM =
            "<'row align-items-center mb-2'" +
            "<'col-md-3'l>" +
            "<'col-md-6 text-center'B>" +
            "<'col-md-3'f>" +
            ">" +
            "<'row'<'col-12'tr>>" +
            "<'row mt-2'<'col-md-5'i><'col-md-7'p>>";

        function exportFormatBody(data, row, column, node) {
            if (typeof node === "string") return node.trim();

            const $node = $(node);

            const fullName = $node.find(".export-full-name");
            if (fullName.length) return fullName.text().trim();

            const select = $node.find("select");
            if (select.length) return select.find("option:selected").text().trim();

            const input = $node.find("input");
            if (input.length) return input.val();

            const titledSpan = $node.find("[title]");
            if (titledSpan.length) return titledSpan.attr("title").trim();

            return $node.text().trim();
        }

        function pdfWithBorders(doc) {
            const table = doc.content.find(c => c.table);
            if (!table) return;

            table.layout = {
                hLineWidth: () => 0.8,
                vLineWidth: () => 0.8,
                hLineColor: () => '#555',
                vLineColor: () => '#555',
                paddingLeft: () => 6,
                paddingRight: () => 6,
                paddingTop: () => 5,
                paddingBottom: () => 5
            };

            // Header row styling
            table.table.body[0].forEach(cell => {
                cell.fillColor = '#343a40'; // dark bg
                cell.color = '#ffffff'; // white text
                cell.bold = true;
                cell.alignment = 'center';
            });

            // Optional: default font size
            doc.defaultStyle.fontSize = 9;
        }

        function getReportFileName(baseName) {
            let dateRange = $('#dateRange').val();

            if (dateRange) {
                dateRange = dateRange.replace(/\s+/g, '').replace('to', '_to_');
                return `${baseName}_${dateRange}`;
            }

            let today = moment().format('DD-MM-YYYY');
            return `${baseName}_${today}`;
        }


        function getReportButtons(reportName) {
            return [{
                    extend: "excel",
                    className: "btn btn-success btn-sm mx-1",
                    text: '<i class="fas fa-file-excel me-1"></i> Excel',
                    title: () => getReportFileName("User_Task_Report"),
                    exportOptions: {
                        columns: ":not(.no-export)",
                        orthogonal: "export",
                        format: {
                            body: exportFormatBody
                        }
                    }
                },
                {
                    extend: "csv",
                    className: "btn btn-info btn-sm mx-1",
                    text: '<i class="fas fa-file-csv me-1"></i> CSV',
                    title: () => getReportFileName("User_Task_Report"),
                    exportOptions: {
                        columns: ":not(.no-export)",
                        orthogonal: "export",
                        format: {
                            body: exportFormatBody
                        }
                    }
                },
                {
                    extend: "pdf",
                    className: "btn btn-danger btn-sm mx-1",
                    text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                    orientation: "landscape",
                    pageSize: "A4",
                    title: () => getReportFileName("User_Task_Report"),
                    exportOptions: {
                        columns: ":not(.no-export)",
                        orthogonal: "export",
                        format: {
                            body: exportFormatBody
                        }
                    },
                    customize: function(doc) {
                        pdfWithBorders(doc);
                    }
                },
                {
                    extend: "print",
                    className: "btn btn-warning btn-sm mx-1",
                    text: '<i class="fas fa-print me-1"></i> Print',
                    title: () => getReportFileName("User_Task_Report"),
                    exportOptions: {
                        columns: ":not(.no-export)",
                        orthogonal: "export",
                        format: {
                            body: exportFormatBody
                        }
                    }
                }
            ]

        }
    </script>
