@extends('backend.partials.master')
@section('title')
    Collection Sessions {{ __('levels.list') }}
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
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">Collection Sessions</a></li>
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
                <div class="card-body">
                    <form method="GET" action="{{ route('collection-sessions.index') }}" class="row align-items-end">
                        <div class="col-md-3">
                            <label for="date_from">From Date</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" 
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to">To Date</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" 
                                   value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('collection-sessions.index') }}" class="btn btn-secondary">
                                <i class="fa fa-refresh"></i> Reset
                            </a>
                        </div>
                        <div class="col-md-3 text-right">
                            <a href="{{ route('collection-sessions.current') }}" class="btn btn-info">
                                <i class="fa fa-clock-o"></i> Current Sessions
                            </a>
                            <a href="{{ route('collection-sessions.history') }}" class="btn btn-warning">
                                <i class="fa fa-history"></i> History
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- data table  -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="row pl-4 pr-4 pt-4">
                    <div class="col-9">
                        <p class="h3">Collection Sessions</p>
                    </div>
                    <div class="col-3">
                        @if (hasPermission('collection_sessions_create'))
                        <button type="button" class="btn btn-success float-right mr-2" data-toggle="modal" data-target="#startSessionModal">
                            <i class="fa fa-plus-circle"></i> Create New Session
                        </button>
                        @endif
                        @if (hasPermission('collection_periods_create'))
                        <a href="{{ route('collection-periods.create') }}" class="btn btn-primary float-right mr-2">
                            <i class="fa fa-clock"></i> New Period
                        </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>Collection Period</th>
                                    <th>Date</th>
                                    <th>Time Range</th>
                                    <th>{{ __('levels.status') }}</th>
                                    <th>Total Parcels</th>
                                    <th>Assigned</th>
                                    <th>Unassigned</th>
                                    <th>Progress</th>
                                    @if (hasPermission('collection_sessions_update') || hasPermission('collection_sessions_read'))
                                    <th width="150px">{{ __('levels.action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessions as $key => $session)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $session->collectionPeriod->name }}</td>
                                    <td>{{ $session->collection_date->format('M d, Y') }}</td>
                                    <td>{{ $session->collectionPeriod->time_range }}</td>
                                    <td>{!! $session->status_badge !!}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $session->total_parcels }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">{{ $session->assigned_parcels }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">{{ $session->unassigned_parcels }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ $session->progress_percentage }}%" 
                                                 aria-valuenow="{{ $session->progress_percentage }}" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                                {{ $session->progress_percentage }}%
                                            </div>
                                        </div>
                                    </td>
                                    @if (hasPermission('collection_sessions_update') || hasPermission('collection_sessions_read'))
                                    <td>
                                        <div class="btn-group">
                                            @if (hasPermission('collection_sessions_read'))
                                            <a href="{{route('collection-sessions.show', $session->id)}}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            @endif
                                            @if (hasPermission('collection_sessions_update') && $session->status == 'active')
                                            <form action="{{route('collection-sessions.complete', $session->id)}}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" 
                                                        onclick="return confirm('Are you sure you want to complete this session?')">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Start Session Modal -->
@if (hasPermission('collection_sessions_create'))
<div class="modal fade" id="startSessionModal" tabindex="-1" role="dialog" aria-labelledby="startSessionModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="startSessionModalLabel">Start Collection Session</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('collection-sessions.start') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="collection_period_id">Collection Period <span class="text-danger">*</span></label>
                        <select name="collection_period_id" id="collection_period_id" class="form-control" required>
                            <option value="">Select a period...</option>
                            @foreach($periods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }} ({{ $period->time_range }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="collection_date">Collection Date</label>
                        <input type="date" name="collection_date" id="collection_date" class="form-control" 
                               value="{{ date('Y-m-d') }}">
                        <small class="form-text text-muted">Leave empty to use today's date</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-play"></i> Start Session
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
