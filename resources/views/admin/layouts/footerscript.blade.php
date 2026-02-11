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

    <!-- DataTables CORE -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <!-- Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- Export deps -->
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


        //  🔥 Global stacked modal handler (SweetAlert-like behavior) 
        $(document).on("show.bs.modal", ".modal", function() {
            const zIndex = 1050 + 10 * $(".modal.show").length;
            $(this).css("z-index", zIndex);
            setTimeout(() => {
                    $(".modal-backdrop").not(".modal-stack").first().css("z-index", zIndex - 1).addClass(
                        "modal-stack");
                },
                0);
        });

        $(document).on("hide.bs.modal", ".modal",
            function() {
                const $modal = $(this);

                if ($modal.find(":focus").length) {
                    $(document.activeElement).blur();
                }

                $modal.find(".select2-hidden-accessible").each(function() {
                    $(this).select2("destroy");
                });
            });
    </script>

    {{-- daterange setting common --}}
    <script>
        // Initialize date range picker (FUTURE ORIENTED)
        $('.date-range').daterangepicker({
            autoUpdateInput: false,
            opens: 'left',
            autoApply: false,
            minDate: moment(), // 🔥 prevent past dates
            ranges: {
                'Today': [moment(), moment()],
                'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
                'Next 7 Days': [moment(), moment().add(6, 'days')],
                'Next 30 Days': [moment(), moment().add(29, 'days')],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Next Month': [
                    moment().add(1, 'month').startOf('month'),
                    moment().add(1, 'month').endOf('month')
                ]
            }
        });

        // Apply date range
        $('.date-range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(
                picker.startDate.format('DD-MM-YYYY') +
                ' - ' +
                picker.endDate.format('DD-MM-YYYY')
            );
            $(this).trigger('change'); // for filters
        });

        // Cancel date range
        $('.date-range').on('cancel.daterangepicker', function() {
            $(this).val('');
            $(this).trigger('change');
        });

        // Set Today programmatically
        function setDateRangeToday() {
            const today = moment();
            const picker = $('.date-range').data('daterangepicker');
            picker.setStartDate(today);
            picker.setEndDate(today);
            $('.date-range').val(today.format('DD-MM-YYYY') + ' - ' + today.format('DD-MM-YYYY'));
        }
    </script>

    {{-- select2 initialization common --}}
    <script>
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
    </script>

    {{-- datatable buttons common --}}
    <script>
        // const REPORT_TABLE_DOM =
        //     "<'row align-items-center mb-2'" +
        //     "<'col-md-3'l>" +
        //     "<'col-md-6 text-center'B>" +
        //     "<'col-md-3'f>" +
        //     ">" +
        //     "<'row'<'col-12'tr>>" +
        //     "<'row mt-2'<'col-md-5'i><'col-md-7'p>>";
        const REPORT_TABLE_DOM =
            "<'row align-items-center mb-2'" +
            "<'col-md-6 d-flex align-items-center'lB>" +
            "<'col-md-6 text-end'f>" +
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
                cell.alignment = 'center'; // center header
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
                extend: "collection",
                className: "btn btn-secondary btn-sm",
                text: '<i class="fas fa-ellipsis-v"></i>',
                titleAttr: "Export Options",
                buttons: [{
                        extend: "excel",
                        text: '<i class="fas fa-file-excel me-2 text-success"></i> Excel',
                        title: () => getReportFileName(reportName),
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
                        text: '<i class="fas fa-file-csv me-2 text-info"></i> CSV',
                        title: () => getReportFileName(reportName),
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
                        text: '<i class="fas fa-file-pdf me-2 text-danger"></i> PDF',
                        orientation: "landscape",
                        pageSize: "A4",
                        title: () => getReportFileName(reportName),
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
                        text: '<i class="fas fa-print me-2 text-warning"></i> Print',
                        title: () => getReportFileName(reportName),
                        exportOptions: {
                            columns: ":not(.no-export)",
                            orthogonal: "export",
                            format: {
                                body: exportFormatBody
                            }
                        }
                    }
                ]
            }];
        }

        // function getReportButtons(reportName) {
        //     return [{
        //             extend: "excel",
        //             className: "btn btn-success btn-sm mx-1",
        //             text: '<i class="fas fa-file-excel me-1"></i> Excel',
        //             title: () => getReportFileName(reportName),
        //             exportOptions: {
        //                 columns: ":not(.no-export)",
        //                 orthogonal: "export",
        //                 format: {
        //                     body: exportFormatBody
        //                 }
        //             }
        //         },
        //         {
        //             extend: "csv",
        //             className: "btn btn-info btn-sm mx-1",
        //             text: '<i class="fas fa-file-csv me-1"></i> CSV',
        //             title: () => getReportFileName(reportName),
        //             exportOptions: {
        //                 columns: ":not(.no-export)",
        //                 orthogonal: "export",
        //                 format: {
        //                     body: exportFormatBody
        //                 }
        //             }
        //         },
        //         {
        //             extend: "pdf",
        //             className: "btn btn-danger btn-sm mx-1",
        //             text: '<i class="fas fa-file-pdf me-1"></i> PDF',
        //             orientation: "landscape",
        //             pageSize: "A4",
        //             title: () => getReportFileName(reportName),
        //             exportOptions: {
        //                 columns: ":not(.no-export)",
        //                 orthogonal: "export",
        //                 format: {
        //                     body: exportFormatBody
        //                 }
        //             },
        //             customize: function(doc) {
        //                 pdfWithBorders(doc);
        //             }
        //         },
        //         {
        //             extend: "print",
        //             className: "btn btn-warning btn-sm mx-1",
        //             text: '<i class="fas fa-print me-1"></i> Print',
        //             title: () => getReportFileName(reportName),
        //             exportOptions: {
        //                 columns: ":not(.no-export)",
        //                 orthogonal: "export",
        //                 format: {
        //                     body: exportFormatBody
        //                 }
        //             }
        //         }
        //     ]

        // }
    </script>

    <script>
        var task_edit_path = "{{ route('task.show', ['task' => ':task']) }}";
        var task_delete_path = "{{ route('task.destroy', ['task' => ':task']) }}";
        var task_pdf_path = "{{ route('task.pdf', ['task' => ':task']) }}";

        window.canEditMeeting = @json(canAccess('task.edit'));
        window.canDeleteMeeting = @json(canAccess('task.delete'));

        $(document).on("click", ".open-meeting-modal", function(e) {
            e.preventDefault();

            const consultingId = $(this).data("consulting-id") || null;
            const clientObjectiveId = $(this).data("client-objective-id") || null;

            const clientName = $(this).data("client-name");
            const objectiveName = $(this).data("objective-name");

            $("#taskModalTitle").html(`
        Meetings
        <span class="text-muted fw-normal">
            — ${clientName}${objectiveName ? " / " + objectiveName : ""}
        </span>
    `);

            const $addBtn = $("#addMeetingBtn");

            if (!$addBtn.data("base-url")) {
                $addBtn.data("base-url", $addBtn.attr("href"));
            }

            let url = $addBtn.data("base-url");

            if (consultingId) {
                url += "?consulting_id=" + consultingId;
            } else if (clientObjectiveId) {
                url += "?client_objective_id=" + clientObjectiveId;
            }

            $addBtn.attr("href", url);

            const modal = new bootstrap.Modal("#taskModal", {
                backdrop: "static",
                keyboard: false,
            });

            modal.show();

            loadTaskModalTable(consultingId, clientObjectiveId);
        });


        let taskModalTable = null;
        let currentConsultingId = null;
        let currentClientObjectiveId = null;

        function loadTaskModalTable(consultingId = null, clientObjectiveId = null) {

            currentConsultingId = consultingId;
            currentClientObjectiveId = clientObjectiveId;

            // ✅ If already initialized → just reload with new params
            if ($.fn.DataTable.isDataTable('#task_modal_table')) {
                taskModalTable.ajax.reload(null, true);
                return;
            }

            taskModalTable = $("#task_modal_table").DataTable({
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                processing: true,
                serverSide: true,
                serverMethod: "GET",
                pageLength: 25,
                lengthMenu: [
                    [25, 100, 200, 250],
                    [25, 100, 200, 250],
                ],

                ajax: {
                    url: $("#task_modal_table").attr("data-url"),
                    data: function(d) {
                        d.consulting_id = currentConsultingId;
                        d.client_objective_id = currentClientObjectiveId;
                    }
                },

                columns: [

                    // 1️⃣ Sr No
                    {
                        data: "id",
                        orderable: true,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },

                    // 2️⃣ Title
                    {
                        data: "title",
                        defaultContent: "-"
                    },

                    // 3️⃣ Expertise
                    {
                        data: "expertise_manager",
                        orderable: false,
                        render: function(data) {
                            if (!data) return "-";

                            return `
                        <span class="badge"
                            style="
                                background-color: ${data.color ?? data.color_name ?? "#6c757d"};
                                color:#fff;
                                font-size:11px;
                                padding:4px 8px;
                                border-radius:4px;">
                            ${data.name}
                        </span>
                    `;
                        }
                    },

                    // 4️⃣ Due Date
                    {
                        data: "task_due_date",
                        render: function(data) {
                            return data ? moment(data).format("DD-MM-YYYY HH:mm") : "-";
                        }
                    },

                    // 5️⃣ Status
                    {
                        data: "status_manager",
                        orderable: false,
                        render: function(data) {
                            if (!data) return "N/A";

                            return `
                        <span style="
                            background:${data.color_name || "gray"};
                            color:#fff;
                            padding:2px 6px;
                            border-radius:4px;
                            font-size:11px;">
                            ${data.name}
                        </span>
                    `;
                        }
                    },

                    // 6️⃣ Action
                    {
                        data: "id",
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                        render: function(id) {

                            let pdf_path_set = task_pdf_path.replace(":task", id);
                            let edit_path_set = task_edit_path.replace(":task", id);
                            let delete_path_set = task_delete_path.replace(":task", id);

                            let editDisabled = window.canEditMeeting ? "" :
                                "style='pointer-events:none;opacity:0.4;'";

                            let deleteDisabled = window.canDeleteMeeting ? "" :
                                "style='pointer-events:none;opacity:0.4;'";

                            return `
                        <a href="${pdf_path_set}" target="_blank" title="PDF">
                            <i class="fas fa-file-pdf p-1 text-secondary"></i>
                        </a>

                        <a href="${edit_path_set}"
                           title="Edit"
                           ${editDisabled}>
                            <i class="fas fa-pen p-1 text-primary"></i>
                        </a>

                        <a href="javascript:void(0);"
                           class="task-delete-data"
                           data-url="${delete_path_set}"
                           title="Delete"
                           ${deleteDisabled}>
                            <i class="fas fa-trash p-1 text-danger"></i>
                        </a>
                    `;
                        }
                    }
                ],

                language: {
                    searchPlaceholder: "Search...",
                    sSearch: "",
                    lengthMenu: "_MENU_ items/page",
                }
            });
        }


        // function loadTaskModalTable(consultingId) {
        //     consultingId = consultingId;

        //     if (taskModalTable) {
        //         taskModalTable.ajax.reload(null, true);
        //         return;
        //     }

        //     taskModalTable = $("#task_modal_table").DataTable({
        //         order: [
        //             [0, "desc"]
        //         ],
        //         autoWidth: false,
        //         processing: true,
        //         serverSide: true,
        //         serverMethod: "GET",
        //         pageLength: 25,
        //         lengthMenu: [
        //             [25, 100, 200, 250],
        //             [25, 100, 200, 250],
        //         ],

        //         ajax: {
        //             url: $("#task_modal_table").attr("data-url"),
        //             data: function(d) {
        //                 d.consulting_id = consultingId;
        //             },
        //         },

        //         columns: [
        //             // 1️⃣ Sr No
        //             {
        //                 data: "id",
        //                 render: function(data, type, row, meta) {
        //                     return meta.row + meta.settings._iDisplayStart + 1;
        //                 },
        //             },

        //             // 2️⃣ title
        //             {
        //                 data: "title",
        //                 render: function(data) {
        //                     return data ? data : "-";
        //                 },
        //             },

        //             // 4️⃣ Expertise
        //             {
        //                 data: "expertise_manager",
        //                 render: function(data) {
        //                     if (!data) return "-";

        //                     return `
    //                         <span
    //                             class="badge"
    //                             style="
    //                                 background-color: ${data.color ?? data.color_name ?? "#6c757d"};
    //                                 color: #fff;
    //                                 font-size: 11px;
    //                                 padding: 4px 8px;
    //                                 border-radius: 4px;
    //                             "
    //                         >
    //                             ${data.name}
    //                         </span>
    //                     `;
        //                 },
        //             },

        //             // 3️⃣ Due Date
        //             {
        //                 data: "task_due_date",
        //                 render: function(data) {
        //                     return data ? moment(data).format("DD-MM-YYYY HH:mm") : "-";
        //                 },
        //             },

        //             // 4️⃣ Status
        //             {
        //                 data: "status_manager",
        //                 render: function(v, t, o) {
        //                     if (o.status_manager) {
        //                         let name = o.status_manager.name;
        //                         let color = o.status_manager.color_name || "gray";

        //                         return `
    //                     <span style="
    //                         background:${color};
    //                         color:#fff;
    //                         padding:2px 6px;
    //                         border-radius:4px;
    //                         font-size:11px;
    //                     ">
    //                         ${name}
    //                     </span>`;
        //                     }
        //                     return "N/A";
        //                 },
        //             },

        //             // 5️⃣ Action
        //             {
        //                 data: "id",
        //                 orderable: false,
        //                 searchable: false,
        //                 className: "text-center",
        //                 render: function(id) {
        //                     let pdf_path_set = task_pdf_path.replace(":task", id);
        //                     let edit_path_set = task_edit_path.replace(":task", id);
        //                     let delete_path_set = task_delete_path.replace(":task", id);

        //                     let editDisabled = window.canEditMeeting ?
        //                         "" :
        //                         "style='pointer-events:none;opacity:0.4;'";

        //                     let deleteDisabled = window.canDeleteMeeting ?
        //                         "" :
        //                         "style='pointer-events:none;opacity:0.4;'";

        //                     return `
    //                 <a href="${pdf_path_set}" target="_blank" title="PDF">
    //                     <i class="fas fa-file-pdf p-1 text-secondary"></i>
    //                 </a>

    //                 <a href="${edit_path_set}"
    //                    title="Edit"
    //                    ${editDisabled}>
    //                     <i class="fas fa-pen p-1 text-primary"></i>
    //                 </a>

    //                 <a href="javascript:void(0);"
    //                    class="task-delete-data"
    //                    data-url="${delete_path_set}"
    //                    title="Delete"
    //                    ${deleteDisabled}>
    //                     <i class="fas fa-trash p-1 text-danger"></i>
    //                 </a>
    //             `;
        //                 },
        //             },
        //         ],

        //         language: {
        //             searchPlaceholder: "Search...",
        //             sSearch: "",
        //             lengthMenu: "_MENU_&nbsp; items/page",
        //         },
        //     });
        // }


        $(document).on("click", ".task-delete-data", function() {
            event.preventDefault();
            swal.fire({
                title: "Are you sure to delete this record?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
            }).then((result) => {
                if (result.isConfirmed) {
                    var delete_url = $(this).attr("data-url");
                    $.ajax({
                        url: delete_url,
                        type: "DELETE",
                        data: {
                            _token: csrf_token,
                        },
                        success: function(result) {
                            $("[id$='_error']").empty();
                            taskModalTable.draw();
                            showToastr("success", result.message);
                        },
                        error: function(result) {
                            showToastr("error", result.responseJSON.message);
                        },
                    });
                }
            });
        });
    </script>
