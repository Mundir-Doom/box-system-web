@extends('backend.partials.master')
@section('title')
    Current Collection Sessions
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
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">Current Sessions</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->

    <!-- Active Periods Info -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Active Collection Periods Today</h5>
                </div>
                <div class="card-body">
                    @if($activePeriods->count() > 0)
                        <div class="row">
                            @foreach($activePeriods as $period)
                            <div class="col-md-4">
                                <div class="card border-info">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">{{ $period->name }}</h6>
                                        <p class="card-text">{{ $period->time_range }}</p>
                                        @if($period->isCurrentlyActive())
                                            <span class="badge badge-success">Currently Active</span>
                                        @else
                                            <span class="badge badge-secondary">Scheduled</span>
                                        @endif
                        @if (hasPermission('collection_sessions_create'))
                        <br><br>
                        @if(!$period->getCurrentSession())
                        <form action="{{ route('collection-sessions.start') }}" method="POST" style="display: inline;">
                            @csrf
                            <input type="hidden" name="collection_period_id" value="{{ $period->id }}">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-plus-circle"></i> Start New Session
                            </button>
                        </form>
                                        @else
                                        <a href="{{ route('collection-sessions.show', $period->getCurrentSession()->id) }}" class="btn btn-sm btn-info">
                                            <i class="fa fa-eye"></i> View Session
                                        </a>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fa fa-info-circle"></i> No active collection periods for today.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- current sessions table  -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-9">
                            <h5 class="mb-0">Current Active Sessions</h5>
                        </div>
                        <div class="col-3">
                            <a href="{{route('collection-sessions.index')}}" class="btn btn-sm btn-primary float-right">
                                <i class="fa fa-list"></i> All Sessions
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if($currentSessions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Collection Period</th>
                                    <th>Started At</th>
                                    <th>Total Parcels</th>
                                    <th>Assigned</th>
                                    <th>Unassigned</th>
                                    <th>Progress</th>
                                    <th>{{ __('levels.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currentSessions as $session)
                                <tr>
                                    <td>
                                        <strong>{{ $session->collectionPeriod->name }}</strong><br>
                                        <small class="text-muted">{{ $session->collectionPeriod->time_range }}</small>
                                    </td>
                                    <td>{{ $session->started_at ? $session->started_at->format('h:i A') : '-' }}</td>
                                    <td>
                                        <span class="badge badge-info badge-lg">{{ $session->total_parcels }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success badge-lg">{{ $session->assigned_parcels }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning badge-lg">{{ $session->unassigned_parcels }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                                                 style="width: {{ $session->progress_percentage }}%" 
                                                 aria-valuenow="{{ $session->progress_percentage }}" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                                {{ $session->progress_percentage }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if (hasPermission('collection_sessions_read'))
                                            <a href="{{route('collection-sessions.show', $session->id)}}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                            @endif
                                            @if (hasPermission('delivery_assignments_read'))
                                            <a href="{{route('delivery-assignments.index')}}?session={{ $session->id }}" class="btn btn-sm btn-primary">
                                                <i class="fa fa-truck"></i> Assign
                                            </a>
                                            @endif
                                            @if (hasPermission('collection_sessions_update'))
                                            <form action="{{route('collection-sessions.complete', $session->id)}}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" 
                                                        onclick="return confirm('Are you sure you want to complete this session?')">
                                                    <i class="fa fa-check"></i> Complete
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle fa-3x mb-3"></i>
                        <h5>No Active Collection Sessions</h5>
                        <p>There are no active collection sessions running at the moment. Start a new session from an active collection period above.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-refresh every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
});
</script>
@endpush

@endsection
