<!-- Menu Permissions Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="permissionModalTitle">Menu Permissions</h3>
            </div>
            <div class="modal-body">
                <!-- Assign Permission Form -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Assign New Permission</h4>
                    </div>
                    <div class="panel-body">
                        <form id="permissionForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="permission_id">Permission <span class="text-danger">*</span></label>
                                        <select class="form-control" id="permission_id" name="permission_id">
                                            <option value="">Select Permission</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="role_id">Role <span class="text-danger">*</span></label>
                                        <select class="form-control" id="role_id" name="role_id">
                                            <option value="">Select Role</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fa fa-plus"></i> Assign Permission
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <hr>

                <!-- Assigned Permissions Table -->
                <div class="panel panel-default" style="width: 100%; margin: 0; padding: 0;">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            Assigned Permissions 
                            <span class="badge" id="permissionCount">0</span>
                        </h4>
                    </div>
                    <div class="panel-body" style="padding: 0;">
                        <div class="table-responsive" style="width: 100%;">
                            <table class="table table-bordered table-hover table-striped mb-0" id="permissionsTable" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40%;">Permission</th>
                                        <th style="width: 40%;">Role</th>
                                        <th style="width: 20%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Populated by DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let currentMenuId = null;
    let permissionsTable = null;
    
    // Open Permissions Modal (delegated for tree view)
    $(document).on('click', '.permissions-btn', function(e) {
        e.preventDefault();
        let url = $(this).data('url');
        let menuName = $(this).data('name');
        
        $('#permissionModalTitle').text(`Permissions for: ${menuName}`);
        
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    currentMenuId = response.menu.id;
                    
                    // Populate roles dropdown
                    let roleOptions = '<option value="">Select Role</option>';
                    $.each(response.roles, function(id, name) {
                        roleOptions += `<option value="${id}">${name}</option>`;
                    });
                    $('#role_id').html(roleOptions);
                    
                    // Populate permissions dropdown
                    let permissionOptions = '<option value="">Select Permission</option>';
                    $.each(response.permissions, function(id, name) {
                        permissionOptions += `<option value="${id}">${name}</option>`;
                    });
                    $('#permission_id').html(permissionOptions);
                    
                    // Initialize/Reinitialize permissions table
                    if (permissionsTable) {
                        permissionsTable.destroy();
                    }
                    
                    permissionsTable = $('#permissionsTable').DataTable({
                        data: response.assigned_permissions,
                        columns: [
                            {
                                data: 'permission.name',
                                defaultContent: '-'
                            },
                            {
                                data: 'role.name',
                                defaultContent: '-'
                            },
                            {
                                data: 'id',
                                orderable: false,
                                searchable: false,
                                render: function(data) {
                                    return `<button class="btn btn-danger btn-sm delete-permission-btn" 
                                                data-id="${data}"
                                                data-url="{{ url('admin/menu-permissions') }}/${data}">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>`;
                                }
                            }
                        ],
                        drawCallback: function() {
                            updatePermissionCount();
                        },
                        pageLength: 10,
                        order: [[0, 'asc']]
                    });
                    
                    $('#permissionModal').modal('show');
                }
            },
            error: function(xhr) {
                toastr.error('Error loading permissions');
            }
        });
    });

    // Assign Permission
    $('#permissionForm').submit(function(e) {
        e.preventDefault();
        
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        $.ajax({
            url: '{{ route("admin.menu-permissions.store") }}',
            type: 'POST',
            data: {
                menu_id: currentMenuId,
                permission_id: $('#permission_id').val(),
                role_id: $('#role_id').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#permissionForm')[0].reset();
                    
                    if (permissionsTable) {
                        permissionsTable.row.add(response.permission).draw();
                        updatePermissionCount();
                    }
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $(`#${key}`).addClass('is-invalid');
                        $(`#${key}`).siblings('.invalid-feedback').text(value[0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || 'Error assigning permission');
                }
            }
        });
    });

    // Delete Permission (delegated)
    $(document).on('click', '.delete-permission-btn', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to remove this permission?')) {
            return;
        }
        
        let url = $(this).data('url');
        let row = $(this).closest('tr');
        
        $.ajax({
            url: url,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    if (permissionsTable) {
                        permissionsTable.row(row).remove().draw();
                        updatePermissionCount();
                    }
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error removing permission');
            }
        });
    });

    // Update permission count badge
    function updatePermissionCount() {
        if (permissionsTable) {
            const count = permissionsTable.rows().count();
            $('#permissionCount').text(count);
        }
    }

    // Reset form when modal closes
    $('#permissionModal').on('hidden.bs.modal', function() {
        $('#permissionForm')[0].reset();
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });
});
</script>
@endpush