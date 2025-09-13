@extends('backend.partials.master')
@section('title')
    Collection Periods {{ __('levels.list') }}
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
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">Collection Periods</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->
    <div class="row">
        <!-- data table  -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="row pl-4 pr-4 pt-4">
                    <div class="col-9">
                        <p class="h3">Collection Periods</p>
                    </div>
                    @if (hasPermission('collection_periods_create'))
                    <div class="col-3">
                        <a href="{{route('collection-periods.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="top" title="{{ __('levels.add') }}">
                            <i class="fa fa-plus"></i> Add Period
                        </a>
                    </div>
                    @endif
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>Name</th>
                                    <th>Time Range</th>
                                    <th>{{ __('levels.status') }}</th>
                                    <th>Description</th>
                                    <th>Created</th>
                                    @if (hasPermission('collection_periods_update') || hasPermission('collection_periods_delete'))
                                    <th width="100px">{{ __('levels.action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($periods as $key => $period)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $period->name }}</td>
                                    <td>{{ $period->time_range }}</td>
                                    <td>{!! $period->status_badge !!}</td>
                                    <td>{{ Str::limit($period->description, 50) }}</td>
                                    <td>{{ $period->created_at->format('M d, Y') }}</td>
                                    @if (hasPermission('collection_periods_update') || hasPermission('collection_periods_delete'))
                                    <td>
                                        <div class="btn-group">
                                            @if (hasPermission('collection_periods_update'))
                                            <a href="{{route('collection-periods.edit', $period->id)}}" class="btn btn-sm btn-primary">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{route('collection-periods.toggle', $period->id)}}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $period->is_active ? 'btn-warning' : 'btn-success' }}" 
                                                        onclick="return confirm('Are you sure you want to {{ $period->is_active ? 'deactivate' : 'activate' }} this period?')">
                                                    <i class="fa {{ $period->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @if (hasPermission('collection_periods_delete'))
                                            <form action="{{route('collection-periods.destroy', $period->id)}}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this period?')">
                                                    <i class="fa fa-trash"></i>
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
@endsection
