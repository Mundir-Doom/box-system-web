@extends('backend.partials.master')
@section('title')
    {{ __('delivery_assignments.title') }}
@endsection
@section('maincontent')
<!-- wrapper  -->
<div class="container-fluid dashboard-content">
    <!-- pageheader -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{ __('delivery_assignments.collection_management') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('delivery_assignments.title') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->

    <!-- Statistics Cards -->
    @push('styles')
    <style>
        .stat-card { background:#fff; border-radius:12px; box-shadow:0 4px 16px rgba(0,0,0,.06); padding:16px 18px; height:100%; display:flex; align-items:center; justify-content:space-between; border-left:4px solid var(--stat-color); }
        .stat-card .stat-content { display:flex; flex-direction:column; }
        .stat-card .stat-value { font-size:32px; font-weight:800; color:#1f2937; line-height:1; letter-spacing:.2px; }
        .stat-card .stat-label { font-size:13px; color:#6b7280; margin-top:6px; font-weight:700; }
        .stat-card .stat-icon { width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,.04); color: var(--stat-color); }
        .stat-card.stat-info { --stat-color:#17a2b8; }
        .stat-card.stat-success { --stat-color:#28a745; }
        .stat-card.stat-warning { --stat-color:#ffc107; }
        .stat-card.stat-primary { --stat-color: var(--bs-primary, #007bff); }
        .stat-card:hover { transform: translateY(-2px); transition: transform .15s ease-out, box-shadow .15s ease-out; box-shadow:0 8px 24px rgba(0,0,0,.09); }
    </style>
    @endpush

    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card stat-info">
                <div class="stat-content">
                    <div class="stat-value">{{ $assignmentStats['total_collected_today'] }}</div>
                    <div class="stat-label">{{ __('delivery_assignments.collected_today') }}</div>
                </div>
                <div class="stat-icon"><i class="fa fa-inbox fa-lg"></i></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card stat-success">
                <div class="stat-content">
                    <div class="stat-value">{{ $assignmentStats['total_assigned_today'] }}</div>
                    <div class="stat-label">{{ __('delivery_assignments.assigned_today') }}</div>
                </div>
                <div class="stat-icon"><i class="fa fa-truck fa-lg"></i></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card stat-warning">
                <div class="stat-content">
                    <div class="stat-value">{{ $assignmentStats['unassigned_parcels'] }}</div>
                    <div class="stat-label">{{ __('delivery_assignments.unassigned') }}</div>
                </div>
                <div class="stat-icon"><i class="fa fa-exclamation-triangle fa-lg"></i></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card stat-primary">
                <div class="stat-content">
                    <div class="stat-value">{{ $assignmentStats['active_sessions'] }}</div>
                    <div class="stat-label">{{ __('delivery_assignments.active_sessions') }}</div>
                </div>
                <div class="stat-icon"><i class="fa fa-clock-o fa-lg"></i></div>
            </div>
        </div>
    </div>

    <!-- Active Collection Sessions -->
    @if($activeSessions->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('delivery_assignments.active_collection_sessions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($activeSessions as $session)
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $session->collectionPeriod->name ?? 'Unknown Period' }}</h6>
                                    <p class="card-text">
                                        <small class="text-muted">{{ optional($session->collection_date)->format('M d, Y') ?? '-' }}</small><br>
                                        <strong>Total:</strong> {{ $session->total_parcels }} | 
                                        <strong>Unassigned:</strong> <span class="text-warning">{{ $session->unassigned_parcels }}</span>
                                    </p>
                                    <div class="progress mb-2" style="height: 5px;">
                                        <div class="progress-bar" style="width: {{ $session->progress_percentage }}%"></div>
                                    </div>
                                    <a href="{{ route('collection-sessions.show', $session->id) }}" class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Unassigned Parcels -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-9">
                            <h5 class="mb-0">{{ __('delivery_assignments.unassigned_parcels') }}</h5>
                        </div>
                        <div class="col-3">
                            @if($unassignedParcels->count() > 0 && hasPermission('delivery_assignments_create'))
                            <button type="button" class="btn btn-sm btn-success float-right" id="bulkAssignBtn">
                                <i class="fa fa-truck"></i> Bulk Assign
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($unassignedParcels->count() > 0)
                    <form id="bulkAssignForm">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th width="30px">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Tracking ID</th>
                                        <th>Merchant</th>
                                        <th>Customer</th>
                                        <th>Collection Period</th>
                                        <th>Collected At</th>
                                        @if(hasPermission('delivery_assignments_create'))
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unassignedParcels as $assignment)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="assignment_ids[]" value="{{ $assignment->id }}" class="form-check-input parcel-checkbox">
                                        </td>
                                        <td>
                                            <a href="{{ route('parcel.details', $assignment->parcel->id) }}" target="_blank">
                                                {{ $assignment->parcel->tracking_id }}
                                            </a>
                                        </td>
                                        <td>{{ $assignment->parcel->merchant->user->name ?? 'Unknown' }}</td>
                                        <td>{{ $assignment->parcel->customer_name }}</td>
                                        <td>{{ $assignment->collectionSession->collectionPeriod->name }}</td>
                                        <td>{{ optional($assignment->collected_at)->format('h:i A') ?? '-' }}</td>
                                        @if(hasPermission('delivery_assignments_create'))
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary assign-single-btn" 
                                                    data-assignment-id="{{ $assignment->id }}"
                                                    data-tracking-id="{{ $assignment->parcel->tracking_id }}">
                                                <i class="fa fa-truck"></i> Assign
                                            </button>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                    @else
                    <div class="alert alert-info text-center">
                        <i class="fa fa-check-circle fa-3x mb-3"></i>
                        <h5>All Parcels Assigned!</h5>
                        <p>There are no unassigned parcels at the moment. All collected parcels have been assigned to delivery personnel.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Available Delivery Men -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('delivery_assignments.available_delivery_personnel') }}</h5>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    @if($availableDeliveryMen->count() > 0)
                    <div id="deliveryMenList">
                        @foreach($availableDeliveryMen as $deliveryMan)
                        <div class="delivery-man-card" data-delivery-man-id="{{ $deliveryMan->id }}">
                            <div class="card mb-2 delivery-man-item" style="cursor: pointer;">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $deliveryMan->user->name ?? 'Unknown' }}</h6>
                                            <small class="text-muted">{{ $deliveryMan->user->phone ?? '' }}</small><br>
                                            <small class="text-muted">Hub: {{ $deliveryMan->hub->name ?? 'No Hub' }}</small>
                                        </div>
                                        <div class="text-center">
                                            <span class="badge badge-info" id="workload-{{ $deliveryMan->id }}">Loading...</span>
                                            <br><small class="text-muted">Current Load</small>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar" id="progress-{{ $deliveryMan->id }}" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-warning text-center">
                        <i class="fa fa-exclamation-triangle"></i>
                        <p class="mb-0">No delivery personnel available.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Modal -->
<div class="modal fade" id="assignmentModal" tabindex="-1" role="dialog" aria-labelledby="assignmentModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignmentModalLabel">Assign to Delivery Person</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="assignmentForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="selected_delivery_man">Delivery Person <span class="text-danger">*</span></label>
                        <select name="delivery_man_id" id="selected_delivery_man" class="form-control" required>
                            <option value="">Select delivery person...</option>
                            @foreach($availableDeliveryMen as $deliveryMan)
                            <option value="{{ $deliveryMan->id }}">{{ $deliveryMan->user->name ?? 'Unknown' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="priority">Priority</label>
                        <select name="priority" id="priority" class="form-control">
                            <option value="0">Normal</option>
                            <option value="1">High</option>
                            <option value="-1">Low</option>
                        </select>
                    </div>
                    <div id="selectedParcels"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-truck"></i> Assign Parcels
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let selectedDeliveryManId = null;
    let isAssigning = false;

    // Load delivery men workload
    loadDeliveryMenWorkload();

    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.parcel-checkbox').prop('checked', this.checked);
    });

    // Update select all when individual checkboxes change
    $('.parcel-checkbox').on('change', function() {
        updateSelectAllCheckbox();
    });

    // Delivery man selection
    $('.delivery-man-item').on('click', function() {
        $('.delivery-man-item').removeClass('border-primary');
        $(this).addClass('border-primary');
        selectedDeliveryManId = $(this).closest('.delivery-man-card').data('delivery-man-id');
        $('#selected_delivery_man').val(selectedDeliveryManId);
    });

    // Bulk assign button
    $('#bulkAssignBtn').on('click', function() {
        let selectedParcels = $('.parcel-checkbox:checked');
        if (selectedParcels.length === 0) {
            alert('Please select at least one parcel to assign.');
            return;
        }
        
        let parcelsList = '';
        selectedParcels.each(function() {
            let row = $(this).closest('tr');
            let trackingId = row.find('td:nth-child(2) a').text();
            parcelsList += '<li>' + trackingId + '</li>';
        });
        
        $('#selectedParcels').html('<p><strong>Selected Parcels:</strong></p><ul>' + parcelsList + '</ul>');
        var el = document.getElementById('assignmentModal');
        var inst = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
        inst.show();
    });

    // Single assign button
    $('.assign-single-btn').on('click', function() {
        let assignmentId = $(this).data('assignment-id');
        let trackingId = $(this).data('tracking-id');
        
        // Uncheck all and check only this one
        $('.parcel-checkbox').prop('checked', false);
        $('input[value="' + assignmentId + '"]').prop('checked', true);
        
        $('#selectedParcels').html('<p><strong>Selected Parcel:</strong> ' + trackingId + '</p>');
        var el = document.getElementById('assignmentModal');
        var inst = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
        inst.show();
    });

    // Assignment form submission
    $('#assignmentForm').on('submit', function(e) {
        e.preventDefault();
        
        if (isAssigning) return;
        isAssigning = true;

        let selectedParcels = $('.parcel-checkbox:checked');
        let assignmentIds = [];
        selectedParcels.each(function() {
            assignmentIds.push($(this).val());
        });

        let formData = {
            assignment_ids: assignmentIds,
            delivery_man_id: $('#selected_delivery_man').val(),
            priority: $('#priority').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: '{{ route("delivery-assignments.assign") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#assignmentModal').modal('hide');
                
                // Show success message
                if (response.success) {
                    // Create success alert
                    let alertHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        '<i class="fa fa-check-circle"></i> ' + response.message +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span></button></div>';
                    
                    $('.container-fluid').prepend(alertHtml);
                }
                
                // Reload page to show updated assignments
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                let errorMessage = 'Error assigning parcels';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                // Show error alert
                let alertHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    '<i class="fa fa-exclamation-circle"></i> ' + errorMessage +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span></button></div>';
                
                $('.container-fluid').prepend(alertHtml);
            },
            complete: function() {
                isAssigning = false;
            }
        });
    });

    function updateSelectAllCheckbox() {
        let totalCheckboxes = $('.parcel-checkbox').length;
        let checkedCheckboxes = $('.parcel-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
    }

    function loadDeliveryMenWorkload() {
        $.ajax({
            url: '{{ route("delivery-assignments.available-delivery-men") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    response.data.forEach(function(deliveryMan) {
                        let capacityClass = getCapacityClass(deliveryMan.capacity_status);
                        
                        // Update workload badge with active assignments
                        $('#workload-' + deliveryMan.id)
                            .text(deliveryMan.active_assignments)
                            .removeClass()
                            .addClass('badge ' + capacityClass)
                            .attr('title', 'Active: ' + deliveryMan.active_assignments + 
                                         ' | Today: ' + deliveryMan.today_assignments + 
                                         ' | Delivered: ' + deliveryMan.delivered_today);
                        
                        // Update progress bar (based on 20 parcel maximum capacity)
                        let progressPercentage = Math.min((deliveryMan.active_assignments / 20) * 100, 100);
                        $('#progress-' + deliveryMan.id)
                            .css('width', progressPercentage + '%')
                            .removeClass()
                            .addClass('progress-bar ' + getProgressClass(deliveryMan.capacity_status));
                        
                        // Update the card to show additional stats
                        let cardBody = $('#workload-' + deliveryMan.id).closest('.card-body');
                        let statsHtml = '<div class="mt-1">' +
                            '<small class="text-muted d-block">Today: ' + deliveryMan.today_assignments + ' assigned</small>' +
                            '<small class="text-muted d-block">Delivered: ' + deliveryMan.delivered_today + '</small>' +
                            '</div>';
                        
                        // Remove existing stats and add new ones
                        cardBody.find('.delivery-stats').remove();
                        cardBody.append('<div class="delivery-stats">' + statsHtml + '</div>');
                    });
                }
            },
            error: function(xhr) {
                console.error('Failed to load delivery men workload:', xhr.responseText);
            }
        });
    }

    function getCapacityClass(status) {
        switch(status) {
            case 'full': return 'badge-danger';
            case 'high': return 'badge-warning';
            case 'medium': return 'badge-info';
            case 'low': return 'badge-success';
            case 'available': return 'badge-primary';
            default: return 'badge-secondary';
        }
    }

    function getProgressClass(status) {
        switch(status) {
            case 'full': return 'bg-danger';
            case 'high': return 'bg-warning';
            case 'medium': return 'bg-info';
            case 'low': return 'bg-success';
            case 'available': return 'bg-primary';
            default: return 'bg-secondary';
        }
    }

    // Refresh workload every 30 seconds
    setInterval(loadDeliveryMenWorkload, 30000);
});
</script>
@endpush

@endsection
