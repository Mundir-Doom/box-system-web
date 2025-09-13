@extends('backend.partials.master')
@section('title')
    Collection Session Details
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
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Collection Management</a></li>
                            <li class="breadcrumb-item"><a href="{{route('collection-sessions.index')}}" class="breadcrumb-link">Collection Sessions</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">Session Details</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->

    <!-- Session Info -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ $session->collectionPeriod->name }} - {{ $session->collection_date->format('M d, Y') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Collection Period:</strong> {{ $session->collectionPeriod->name }}</p>
                            <p><strong>Time Range:</strong> {{ $session->collectionPeriod->time_range }}</p>
                            <p><strong>Collection Date:</strong> {{ $session->collection_date->format('l, M d, Y') }}</p>
                            <p><strong>Status:</strong> {!! $session->status_badge !!}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Started At:</strong> {{ $session->started_at ? $session->started_at->format('h:i A') : 'Not started' }}</p>
                            <p><strong>Completed At:</strong> {{ $session->completed_at ? $session->completed_at->format('h:i A') : 'Not completed' }}</p>
                            <p><strong>Duration:</strong> 
                                @if($session->started_at && $session->completed_at)
                                    {{ $session->started_at->diffForHumans($session->completed_at, true) }}
                                @elseif($session->started_at)
                                    {{ $session->started_at->diffForHumans() }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($session->notes)
                    <hr>
                    <p><strong>Notes:</strong> {{ $session->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="row">
                            <div class="col-4">
                                <h3 class="text-info">{{ $session->total_parcels }}</h3>
                                <small>Total</small>
                            </div>
                            <div class="col-4">
                                <h3 class="text-success">{{ $session->assigned_parcels }}</h3>
                                <small>Assigned</small>
                            </div>
                            <div class="col-4">
                                <h3 class="text-warning">{{ $session->unassigned_parcels }}</h3>
                                <small>Pending</small>
                            </div>
                        </div>
                        <hr>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $session->progress_percentage }}%" 
                                 aria-valuenow="{{ $session->progress_percentage }}" 
                                 aria-valuemin="0" aria-valuemax="100">
                                {{ $session->progress_percentage }}%
                            </div>
                        </div>
                        <small class="text-muted">Assignment Progress</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    @if($session->status == 'active')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    @if (hasPermission('delivery_assignments_read'))
                    <a href="{{route('delivery-assignments.index')}}?session={{ $session->id }}" class="btn btn-primary">
                        <i class="fa fa-truck"></i> Assign to Delivery Personnel
                    </a>
                    @endif
                    @if (hasPermission('collection_sessions_update'))
                    <form action="{{route('collection-sessions.complete', $session->id)}}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success" 
                                onclick="return confirm('Are you sure you want to complete this session?')">
                            <i class="fa fa-check"></i> Complete Session
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Parcels in Session -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-9">
                            <h5 class="mb-0">Parcels in this Session</h5>
                        </div>
                        <div class="col-3">
                            @if($session->status == 'active' && hasPermission('collection_sessions_update'))
                            <button type="button" class="btn btn-sm btn-success float-right" data-toggle="modal" data-target="#addParcelModal">
                                <i class="fa fa-plus"></i> Add Parcel
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($session->parcelAssignments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tracking ID</th>
                                    <th>Merchant</th>
                                    <th>Customer</th>
                                    <th>Collection Status</th>
                                    <th>Delivery Man</th>
                                    <th>Priority</th>
                                    <th>Collected At</th>
                                    @if($session->status == 'active' && hasPermission('collection_sessions_update'))
                                    <th>Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($session->parcelAssignments as $assignment)
                                <tr>
                                    <td>
                                        <a href="{{ route('parcel.details', $assignment->parcel->id) }}" target="_blank">
                                            {{ $assignment->parcel->tracking_id }}
                                        </a>
                                    </td>
                                    <td>{{ $assignment->parcel->merchant->user->name ?? 'Unknown' }}</td>
                                    <td>{{ $assignment->parcel->customer_name }}</td>
                                    <td>{!! $assignment->status_badge !!}</td>
                                    <td>
                                        @if($assignment->deliveryMan)
                                            {{ $assignment->deliveryMan->user->name ?? 'Unknown' }}
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>{!! $assignment->priority_badge !!}</td>
                                    <td>{{ $assignment->collected_at->format('h:i A') }}</td>
                                    @if($session->status == 'active' && hasPermission('collection_sessions_update'))
                                    <td>
                                        <form action="{{route('collection-sessions.remove-parcel', [$session->id, $assignment->parcel->id])}}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Remove this parcel from the collection session?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info text-center">
                        <i class="fa fa-inbox fa-3x mb-3"></i>
                        <h5>No Parcels in Session</h5>
                        <p>This collection session doesn't have any parcels yet. Add parcels to get started.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Parcel Modal -->
@if($session->status == 'active' && hasPermission('collection_sessions_update'))
<div class="modal fade" id="addParcelModal" tabindex="-1" role="dialog" aria-labelledby="addParcelModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addParcelModalLabel">Add Parcel to Session</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('collection-sessions.add-parcel', $session->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="parcel_id">Parcel (Tracking ID) <span class="text-danger">*</span></label>
                        <select name="parcel_id" id="parcel_id" class="form-control" required>
                            <option value="">Search and select parcel...</option>
                        </select>
                        <small class="form-text text-muted">Only parcels with status "RECEIVED_WAREHOUSE" can be added.</small>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" 
                                  placeholder="Optional notes about this collection"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-plus"></i> Add Parcel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize select2 for parcel selection
    $('#parcel_id').select2({
        ajax: {
            url: '{{ route("parcel.search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    status: 5, // RECEIVED_WAREHOUSE
                    not_in_collection: true
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function(parcel) {
                        return {
                            id: parcel.id,
                            text: parcel.tracking_id + ' - ' + parcel.merchant_name + ' (' + parcel.customer_name + ')'
                        };
                    })
                };
            },
            cache: true
        },
        placeholder: 'Search by tracking ID...',
        minimumInputLength: 3,
        dropdownParent: $('#addParcelModal')
    });
});
</script>
@endpush

@endsection
