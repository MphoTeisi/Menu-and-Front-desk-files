@extends('layouts.admin')

@section('content')
    <div class="main">
        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container-fluid">
                <h3 class="page-title">Front Desk</h3>

                <div class="row">
                    <div class="col-md-12">

                        <!-- PANEL -->
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Enquiries Management <b></b></h3>
                            </div>

                            <div class="panel-body">

                                <div class="custom-tabs-line tabs-line-bottom left-aligned">
                                    <ul class="nav" role="tablist">
                                        <li class="active">
                                            <a href="#tab-enquiries" role="tab" data-toggle="tab">Enquiries</a>
                                        </li>
                                        <li>
                                            <a href="#tab-followups" role="tab" data-toggle="tab">Follow-Ups</a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="tab-content">

                                    <!-- ✅ ENQUIRIES TAB -->
                                    <div class="tab-pane fade in active" id="tab-enquiries">
                                        <button type="button" class="btn btn-primary" id="createEnquiryBtn">
                                            <i class="fa fa-plus"></i> Add New Enquiry
                                        </button>

                                        <table id="enquiriesTable" class="table table-bordered table-striped w-100">
                                            <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th>Name</th>
                                                    <th>Phone</th>
                                                    <th>Email</th>
                                                    <th>Address</th>
                                                    <th>Description</th>
                                                    <th>Enquiry Date</th>
                                                    <th>Next Follow-up</th>
                                                    <th width="10%">Status</th>
                                                    <th width="15%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>

                                    <!-- ✅ FOLLOW-UPS TAB -->
                                    <div class="tab-pane fade" id="tab-followups">

                                        <div class="col-md-12" with="100%">
                                            <table id="followupsTable" class="table table-bordered table-striped w-100" style="width: 100%;">
                                                <thead>
                                                    <tr with="100%">
                                                        <th width="5%">ID</th>
                                                        <th>Enquiry</th>
                                                        <th>Phone</th>
                                                        <th>Follow-up Date</th>
                                                        <th>Notes</th>
                                                        <th>Next Follow-up</th>
                                                        <th width="10%">Status</th>
                                                        <th width="15%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- END PANEL -->

                    </div>
                </div>

            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </div>

    <!-- ✅ SIMPLE ENQUIRY MODAL -->
    <div class="modal fade" id="enquiryModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Form will be loaded here via AJAX -->
            </div>
        </div>
    </div>

    <!-- ✅ FOLLOW-UP MODAL -->
    <div class="modal fade" id="followupModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title" id="followupModalLabel">Add Follow-up</h3>
                </div>
                <div class="modal-body" id="followupModalBody">
                    <!-- Follow-up form via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ LOADING MODAL -->
    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <span class="glyphicon glyphicon-refresh glyphicon-spin"></span>
                    <p class="mt-2 mb-0">Processing...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap.min.css">

    <style>
        .status-badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .status-active {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-closed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-converted {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-lost {
            background-color: #f8d7da;
            color: #721c24;
        }

        .table th {
            background-color: #f8f9fa;
        }

        #enquiriesTable_wrapper .dataTables_filter {
            float: right;
        }

        #enquiriesTable_wrapper .dataTables_length {
            float: left;
        }
    </style>
@endpush

@push('scripts')
    <!-- jQuery MUST be loaded first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap 3 JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            // Show loading modal
            function showLoading() {
                $('#loadingModal').modal('show');
            }

            // Hide loading modal
            function hideLoading() {
                $('#loadingModal').modal('hide');
            }

            // Show notification
            function showNotification(message, type = 'success') {
                alert(message);
            }

            // Initialize DataTable
            const table = $('#enquiriesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.enquiries.index') }}",
                    data: function(d) {
                        // Additional filters if needed
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        className: 'text-center'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'email',
                        name: 'email',
                        render: function(data) {
                            return data ? '<a href="mailto:' + data + '">' + data + '</a>' :
                                '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'address',
                        name: 'address',
                        render: function(data) {
                            return data ? data : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'description',
                        name: 'description',
                        render: function(data) {
                            if (!data) return '<span class="text-muted">-</span>';
                            let text = data.length > 40 ? data.substring(0, 40) + '...' : data;
                            return '<span title="' + data + '">' + text + '</span>';
                        }
                    },
                    {
                        data: 'enquiry_date',
                        name: 'enquiry_date',
                        render: function(data) {
                            return data ? new Date(data).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            }) : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'next_follow_up_date',
                        name: 'next_follow_up_date',
                        render: function(data) {
                            if (!data) return '<span class="text-muted">-</span>';

                            const date = new Date(data);
                            const today = new Date();
                            today.setHours(0, 0, 0, 0);

                            let className = '';
                            if (date < today) {
                                className = 'text-danger';
                            } else if (date.getTime() === today.getTime()) {
                                className = 'text-warning';
                            }

                            return '<span class="' + className + '">' +
                                date.toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric'
                                }) + '</span>';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            const statusClass = 'status-' + data;
                            return '<span class="status-badge ' + statusClass + '">' +
                                data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });

            // Initialize Follow-ups DataTable (using same style as enquiries)
            const followupsTable = $('#followupsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.enquiries.followups.data') }}",
                    data: function(d) {
                        // Additional filters if needed
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        className: 'text-center'
                    },
                    {
                        data: 'enquiry_name',
                        name: 'enquiry.name',
                        render: function(data) {
                            return data ? data : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'phone',
                        name: 'enquiry.phone',
                        render: function(data) {
                            return data ? data : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'followup_date',
                        name: 'followup_date',
                        render: function(data) {
                            return data ? new Date(data).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            }) : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'description',
                        name: 'description',
                        render: function(data) {
                            if (!data) return '<span class="text-muted">-</span>';
                            let text = data.length > 50 ? data.substring(0, 50) + '...' : data;
                            return '<span title="' + data + '">' + text + '</span>';
                        }
                    },
                    {
                        data: 'next_follow_up_date',
                        name: 'enquiry.next_follow_up_date',
                        render: function(data) {
                            if (!data) return '<span class="text-muted">-</span>';

                            const date = new Date(data);
                            const today = new Date();
                            today.setHours(0, 0, 0, 0);

                            let className = '';
                            if (date < today) {
                                className = 'text-danger';
                            } else if (date.getTime() === today.getTime()) {
                                className = 'text-warning';
                            }

                            return '<span class="' + className + '">' +
                                date.toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric'
                                }) + '</span>';
                        }
                    },
                    {
                        data: 'status',
                        name: 'enquiry.status',
                        render: function(data) {
                            const statusClass = 'status-' + data;
                            return '<span class="status-badge ' + statusClass + '">' +
                                data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                    <button class="btn btn-sm btn-info followup-btn"
                            data-url="{{ route('admin.enquiries.followup', '') }}/${row.enquiry_id}"
                            title="Follow Up">
                        <i class="fa fa-phone"></i>
                    </button>
                    <button class="btn btn-sm btn-primary edit-btn"
                            data-url="{{ route('admin.enquiries.edit', '') }}/${row.enquiry_id}"
                            title="Edit">
                        <i class="fa fa-edit"></i>
                    </button>
                `;
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ], // Sort by ID descending (or use [3, 'desc'] for followup date)
                language: {
                    emptyTable: "No follow-ups found",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    lengthMenu: "Show _MENU_ entries",
                    loadingRecords: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Processing...</span></div>',
                    search: "Search:",
                    zeroRecords: "No matching records found",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
            // Create Enquiry Button
            $('#createEnquiryBtn').click(function() {
                $.ajax({
                    url: "{{ route('admin.enquiries.create') }}",
                    type: 'GET',
                    beforeSend: function() {
                        showLoading();
                    },
                    success: function(response) {
                        $('#enquiryModal .modal-content').html(response);
                        $('#enquiryModal').modal('show');
                    },
                    error: function(xhr) {
                        showNotification('Error loading form', 'error');
                        console.error(xhr.responseText);
                    },
                    complete: function() {
                        hideLoading();
                    }
                });
            });

            //: View Enquiry Button
            $(document).on('click', '.view-btn', function() {
                const url = $(this).data('url');

                showLoading();
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#enquiryModal .modal-content').html(response);
                        $('#enquiryModal').modal('show');
                    },
                    error: function(xhr) {
                        showNotification('Error loading enquiry details', 'error');
                        console.error('Error loading enquiry details:', xhr.responseText);
                    },
                    complete: function() {
                        hideLoading();
                    }
                });
            });

            // Edit Enquiry Button
            $(document).on('click', '.edit-btn', function() {
                const url = $(this).data('url');

                showLoading();
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#enquiryModal .modal-content').html(response);
                        $('#enquiryModal').modal('show');
                    },
                    error: function(xhr) {
                        showNotification('Error loading enquiry', 'error');
                        console.error('Error loading enquiry:', xhr.responseText);
                    },
                    complete: function() {
                        hideLoading();
                    }
                });
            });

            // Follow-up Button
            $(document).on('click', '.followup-btn', function() {
                const url = $(this).data('url');
                const $btn = $(this);
                const originalHtml = $btn.html();

                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

                showLoading();
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#followupModalBody').html(response);
                        $('#followupModal').modal('show');
                    },
                    error: function(xhr) {
                        showNotification('Error loading follow-up form', 'error');
                        console.error('Error loading follow-up form:', xhr.responseText);
                    },
                    complete: function() {
                        hideLoading();
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // Delete Enquiry Button
            $(document).on("click", ".delete-btn", function() {
                const url = $(this).data('url');
                const enquiryName = $(this).closest('tr').find('td:eq(1)').text();

                if (confirm(
                        `Are you sure you want to delete the enquiry for "${enquiryName}"? This action cannot be undone.`
                    )) {
                    showLoading();
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            table.ajax.reload();
                            showNotification('Enquiry deleted successfully!');
                        },
                        error: function(xhr) {
                            console.error('Error deleting enquiry:', xhr.responseText);
                            showNotification('Error deleting enquiry', 'error');
                        },
                        complete: function() {
                            hideLoading();
                        }
                    });
                }
            });

            // Handle Enquiry Form Submission
            $(document).on('submit', '#enquiryForm', function(e) {
                e.preventDefault();

                const form = $(this);
                const formData = new FormData(this);
                const url = form.attr('action');

                // Show loading state
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

                showLoading();

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#followupModal').modal('hide');
                        table.ajax.reload(null, false); // Refresh enquiries table
                        followupsTable.ajax.reload(null, false); // Refresh follow-ups table
                        showNotification(response.message || 'Follow-up saved successfully!');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessage = 'Please fix the following errors:\n\n';

                            for (const field in errors) {
                                errorMessage += '• ' + errors[field][0] + '\n';
                            }
                            showNotification(errorMessage, 'error');
                        } else {
                            showNotification('Error saving enquiry', 'error');
                            console.error('Error saving enquiry:', xhr.responseText);
                        }
                    },
                    complete: function() {
                        hideLoading();
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Handle Follow-up Form Submission
            $(document).on('submit', '#followupForm', function(e) {
                e.preventDefault();

                const form = $(this);
                const formData = new FormData(this);
                const url = form.attr('action');

                // Show loading state
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

                showLoading();

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#followupModal').modal('hide');
                        table.ajax.reload(null, false); // Refresh enquiries table
                        followupsTable.ajax.reload(null, false); // Refresh follow-ups table
                        showNotification(response.message || 'Follow-up saved successfully!');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessage = 'Please fix the following errors:\n\n';

                            for (const field in errors) {
                                errorMessage += '• ' + errors[field][0] + '\n';
                            }
                            showNotification(errorMessage, 'error');
                        } else {
                            showNotification('Error saving follow-up', 'error');
                            console.error('Error saving follow-up:', xhr.responseText);
                        }
                    },
                    complete: function() {
                        hideLoading();
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Close modals and reset content
            $('#enquiryModal, #followupModal').on('hidden.bs.modal', function() {
                $(this).find('.modal-content').html('');
            });
        });
    </script>
@endpush
