@extends('backend.partials.master')
@section('title')
    Edit Collection Period
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
                            <li class="breadcrumb-item"><a href="{{route('collection-periods.index')}}" class="breadcrumb-link">Collection Periods</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.edit') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-9">
                            <h5 class="mb-0">Edit Collection Period: {{ $period->name }}</h5>
                        </div>
                        <div class="col-3">
                            <a href="{{route('collection-periods.index')}}" class="btn btn-sm btn-primary float-right">
                                <i class="fa fa-arrow-left"></i> {{ __('levels.back') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{route('collection-periods.update', $period->id)}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Period Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $period->name) }}" placeholder="e.g., Morning Collection" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                    <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" 
                                           value="{{ old('start_time', date('H:i', strtotime($period->start_time))) }}" required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_time">End Time <span class="text-danger">*</span></label>
                                    <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" 
                                           value="{{ old('end_time', date('H:i', strtotime($period->end_time))) }}" required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                              rows="3" placeholder="Optional description of this collection period">{{ old('description', $period->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                                               value="1" {{ old('is_active', $period->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active (Period is available for use)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> {{ __('levels.update') }}
                                    </button>
                                    <a href="{{route('collection-periods.index')}}" class="btn btn-secondary">
                                        <i class="fa fa-times"></i> {{ __('levels.cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Validate that end time is after start time
    $('#start_time, #end_time').on('change', function() {
        var startTime = $('#start_time').val();
        var endTime = $('#end_time').val();
        
        if (startTime && endTime && startTime >= endTime) {
            alert('End time must be after start time.');
            $('#end_time').val('');
        }
    });
});
</script>
@endpush
@endsection
