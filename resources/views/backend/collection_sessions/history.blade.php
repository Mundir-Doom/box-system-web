@extends('backend.partials.master')
@section('title')
    Collection Sessions History
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
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">History</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->
    
    <!-- Filter Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Collection Sessions History</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('collection-sessions.history') }}" class="row align-items-end">
                        <div class="col-md-3">
                            <label for="start_date">From Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" 
                                   value="{{ $startDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date">To Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" 
                                   value="{{ $endDate }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('collection-sessions.history') }}" class="btn btn-secondary">
                                <i class="fa fa-refresh"></i> Reset
                            </a>
                        </div>
                        <div class="col-md-3 text-right">
                            <a href="{{ route('collection-sessions.current') }}" class="btn btn-info">
                                <i class="fa fa-clock-o"></i> Current Sessions
                            </a>
                            <a href="{{ route('collection-sessions.index') }}" class="btn btn-warning">
                                <i class="fa fa-list"></i> All Sessions
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    @if($sessions->count() > 0)
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $sessions->count() }}</h3>
                            <small>Total Sessions</small>
                        </div>
                        <i class="fa fa-calendar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $sessions->where('status', 'completed')->count() }}</h3>
                            <small>Completed</small>
                        </div>
                        <i class="fa fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $sessions->where('status', 'active')->count() }}</h3>
                            <small>Still Active</small>
                        </div>
                        <i class="fa fa-clock-o fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $sessions->sum('total_parcels') }}</h3>
                            <small>Total Parcels</small>
                        </div>
                        <i class="fa fa-boxes fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Historical Sessions Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-9">
                            <h5 class="mb-0">Sessions from {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</h5>
                        </div>
                        <div class="col-3">
                            @if($sessions->count() > 0)
                            <button type="button" class="btn btn-sm btn-success float-right" onclick="exportData()">
                                <i class="fa fa-download"></i> Export
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($sessions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped" id="historyTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Collection Period</th>
                                    <th>Time Range</th>
                                    <th>Status</th>
                                    <th>Duration</th>
                                    <th>Total Parcels</th>
                                    <th>Assigned</th>
                                    <th>Progress</th>
                                    <th>Performance</th>
                                    @if (hasPermission('collection_sessions_read'))
                                    <th>Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessions as $session)
                                <tr>
                                    <td>
                                        <strong>{{ $session->collection_date->format('M d, Y') }}</strong><br>
                                        <small class="text-muted">{{ $session->collection_date->format('l') }}</small>
                                    </td>
                                    <td>{{ $session->collectionPeriod->name ?? 'Unknown Period' }}</td>
                                    <td>{{ $session->collectionPeriod->time_range ?? '-' }}</td>
                                    <td>{!! $session->status_badge !!}</td>
                                    <td>
                                        @if($session->started_at && $session->completed_at)
                                            {{ $session->started_at->diffForHumans($session->completed_at, true) }}
                                        @elseif($session->started_at)
                                            <span class="text-warning">Still running</span>
                                        @else
                                            <span class="text-muted">Not started</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $session->total_parcels }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">{{ $session->assigned_parcels }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px; min-width: 100px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ $session->progress_percentage }}%" 
                                                 aria-valuenow="{{ $session->progress_percentage }}" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                                {{ $session->progress_percentage }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $performance = $session->progress_percentage;
                                            if ($performance >= 90) $class = 'success';
                                            elseif ($performance >= 70) $class = 'warning';  
                                            else $class = 'danger';
                                        @endphp
                                        <span class="badge badge-{{ $class }}">
                                            @if($performance >= 90) Excellent
                                            @elseif($performance >= 70) Good
                                            @else Needs Improvement
                                            @endif
                                        </span>
                                    </td>
                                    @if (hasPermission('collection_sessions_read'))
                                    <td>
                                        <a href="{{route('collection-sessions.show', $session->id)}}" class="btn btn-sm btn-info">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Performance Summary -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Performance Summary</h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $avgProgress = $sessions->avg('progress_percentage');
                                        $totalParcels = $sessions->sum('total_parcels');
                                        $totalAssigned = $sessions->sum('assigned_parcels');
                                        $completedSessions = $sessions->where('status', 'completed')->count();
                                    @endphp
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <h4 class="text-info">{{ number_format($avgProgress, 1) }}%</h4>
                                            <small>Average Assignment Rate</small>
                                        </div>
                                        <div class="col-md-3">
                                            <h4 class="text-success">{{ $totalParcels }}</h4>
                                            <small>Total Parcels Collected</small>
                                        </div>
                                        <div class="col-md-3">
                                            <h4 class="text-primary">{{ $totalAssigned }}</h4>
                                            <small>Total Parcels Assigned</small>
                                        </div>
                                        <div class="col-md-3">
                                            <h4 class="text-warning">{{ $completedSessions }}</h4>
                                            <small>Completed Sessions</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle fa-3x mb-3"></i>
                        <h5>No Historical Data Found</h5>
                        <p>There are no collection sessions in the selected date range. Try adjusting the date filters or check if any collection sessions have been created.</p>
                        <a href="{{ route('collection-sessions.current') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Start New Session
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportData() {
    // Simple CSV export functionality
    let table = document.getElementById('historyTable');
    let csv = [];
    
    // Get headers
    let headers = [];
    let headerRow = table.querySelector('thead tr');
    for (let i = 0; i < headerRow.cells.length - 1; i++) { // Exclude last column (Actions)
        headers.push(headerRow.cells[i].textContent.trim());
    }
    csv.push(headers.join(','));
    
    // Get data rows
    let rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        let rowData = [];
        for (let i = 0; i < row.cells.length - 1; i++) { // Exclude last column (Actions)
            let cellText = row.cells[i].textContent.trim().replace(/\n/g, ' ').replace(/,/g, ';');
            rowData.push('"' + cellText + '"');
        }
        csv.push(rowData.join(','));
    });
    
    // Download CSV
    let csvContent = csv.join('\n');
    let blob = new Blob([csvContent], { type: 'text/csv' });
    let url = window.URL.createObjectURL(blob);
    let a = document.createElement('a');
    a.href = url;
    a.download = 'collection_sessions_history_{{ $startDate }}_to_{{ $endDate }}.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

$(document).ready(function() {
    // Set default dates if not set
    if (!$('#start_date').val()) {
        let thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
        $('#start_date').val(thirtyDaysAgo.toISOString().split('T')[0]);
    }
    
    if (!$('#end_date').val()) {
        let today = new Date();
        $('#end_date').val(today.toISOString().split('T')[0]);
    }
});
</script>
@endpush

@endsection
