@extends('backend.partials.master')
@section('title')
    {{ __('parcel.title') }}    {{ __('levels.list') }}
@endsection
@section('maincontent')
    <!-- wrapper  -->
    <div class="container-fluid  dashboard-content">
        <!-- page header -->
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('menus.dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a href="{{route('parcel.index') }}" class="breadcrumb-link">{{ __('parcel.title') }}</a></li>
                                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link active">{{ __('levels.list') }}</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page header -->
        <div class="row">
            <!-- data table  -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white border-bottom-0">
                        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between py-3">
                            <!-- Title and Search Section -->
                            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center mb-3 mb-lg-0">
                                <h4 class="mb-3 mb-lg-0 me-lg-4 fw-bold text-dark">{{ __('parcel.title') }}</h4>
                                <form action="{{route('parcel.specific.search') }}" method="get" class="search-form">
                                    <div class="input-group">
                                        <input id="Psearch" class="form-control" name="search" type="text" placeholder="{{ __('levels.search') }}..." value="{{ $request->search }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search me-1"></i>{{ __('levels.search') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Action Buttons Section -->
                            @if(hasPermission('parcel_create'))
                                <div class="d-flex flex-wrap gap-2 action-buttons">
                                    {{-- Filter Toggle Button --}}
                                    <button class="btn btn-outline-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                                        <i class="fas fa-filter me-1"></i>{{ __('levels.filter') }}
                                    </button>
                                    
                                    {{-- multiple parcel label print --}}
                                    <form action="{{ route('parcel.multiple.print-label') }}" method="get" target="_blank" id="print_label_form" class="d-inline">
                                        @csrf
                                        <div id="print_label_content"></div>
                                        <button type="submit" class="btn btn-primary btn-sm multiplelabelprint" data-parcels='' style="display: none">
                                            <i class="fas fa-print me-1"></i> {{ __('levels.print_label') }}
                                        </button>
                                    </form>
                                    {{-- end multiple parcel label print --}}
                                    
                                    @if($request->parcel_status == \App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN )
                                        <a href="{{route('parcel.parcel-bulkassign-print',$request->all())}}" class="btn btn-primary btn-sm" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="Print">
                                            <i class="fas fa-print me-1"></i> {{ __('parcel.print') }}
                                        </a>
                                    @endif
                                    
                                    <a href="{{route('parcel.parcelDeliveryMan')}}" target="_blank" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Parcel Map">
                                        <i class="fas fa-map-marked-alt me-1"></i> {{ __('parcel.map') }}
                                    </a>
                                    
                                    <a href="{{route('parcel.parcel-import')}}" class="btn btn-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Parcel Import">
                                        <i class="fas fa-file-import me-1"></i> {{ __('parcel.import_parcel') }}
                                    </a>
                                    
                                    <a href="{{route('parcel.create')}}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Add">
                                        <i class="fas fa-plus me-1"></i> {{ __('levels.add') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Filter Section -->
                        <div class="collapse" id="filterCollapse">
                            <div class="border-top pt-3 mt-3">
                                <form action="{{route('parcel.filter')}}" method="GET">
                                    <div class="row g-3">
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                            <div class="form-group">
                                                <label for="parcel_date" class="form-label fw-medium mb-2">{{ __('parcel.date') }}</label>
                                                <input type="text" autocomplete="off" id="date" name="parcel_date" placeholder="Enter Date" class="form-control" value="{{ old('parcel_date',$request->parcel_date) }}">
                                                @error('parcel_date')
                                                <small class="text-danger mt-1">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                            <div class="form-group">
                                                <label for="parcelStatus" class="form-label fw-medium mb-2">{{ __('parcel.status') }}</label>
                                                <select id="parcelStatus" name="parcel_status" class="form-select @error('parcel_status') is-invalid @enderror">
                                                    <option value="" selected> {{ __('menus.select') }} {{ __('levels.status') }}</option>
                                                    @foreach (trans('parcelStatusFilter') as $key => $status)
                                                        <option value="{{ $key}}" {{ (old('parcel_status',$request->parcel_status) == $key) ? 'selected' : '' }}>{{ $status }}</option>
                                                    @endforeach
                                                </select>
                                                @error('parcel_status')
                                                <small class="text-danger mt-1">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                            <div class="form-group">
                                                <label for="parcelMerchantid" class="form-label fw-medium mb-2">{{ __('parcel.merchant') }}</label>
                                                <select id="parcelMerchantid" name="parcel_merchant_id" class="form-select @error('parcel_merchant_id') is-invalid @enderror" data-url="{{ route('parcel.merchant.shops') }}">
                                                    <option value=""> {{ __('menus.select') }} {{ __('merchant.title') }}</option>
                                                </select>
                                                @error('parcel_merchant_id')
                                                <small class="text-danger mt-1">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                            <div class="form-group">
                                                <label for="parcelDeliveryManID" class="form-label fw-medium mb-2">{{ __('parcel.deliveryman') }}</label>
                                                <select id="parcelDeliveryManID" name="parcel_deliveryman_id" data-url="{{ route('parcel.deliveryman.search') }}" class="form-select @error('parcel_deliveryman_id') is-invalid @enderror">
                                                    <option value="">{{ __('menus.select') }} {{ __('deliveryman.title') }}</option>
                                                </select>
                                                @error('parcel_deliveryman_id')
                                                <small class="text-danger mt-1">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                            <div class="form-group">
                                                <label for="parcelPickupmanId" class="form-label fw-medium mb-2">{{ __('parcel.pickupman') }}</label>
                                                <select id="parcelPickupmanId" name="parcel_pickupman_id" data-url="{{ route('parcel.deliveryman.search') }}" class="form-select @error('parcel_pickupman_id') is-invalid @enderror">
                                                    <option value=""> {{ __('menus.select') }} {{ __('parcel.pickup_man') }}</option>
                                                </select>
                                                @error('parcel_pickupman_id')
                                                <small class="text-danger mt-1">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                            <div class="form-group">
                                                <label for="invoice_id" class="form-label fw-medium mb-2">{{ __('parcel.invoice_id') }}</label>
                                                <input id="invoice_id" type="text" name="invoice_id" placeholder="{{ __('parcel.invoice_id') }}" autocomplete="off" class="form-control" value="{{old('invoice_id',$request->invoice_id)}}">
                                                @error('parcel_customer_phone')
                                                <small class="text-danger mt-1">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-primary px-4 py-2">
                                                <i class="fa fa-filter me-2"></i>{{ __('levels.filter') }}
                                            </button>
                                            <a href="{{ route('parcel.index') }}" class="btn btn-outline-secondary px-4 py-2 ms-2">
                                                <i class="fa fa-eraser me-2"></i>{{ __('levels.clear') }}
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="table" class="table table-hover align-middle parcelTable border-bottom" style="width:100%">
                                <thead class="bg-light">
                                <tr>
                                    <th class="parcel-index permission-check-box ps-4 py-3">
                                        <div class="form-check">
                                            <input type="checkbox" id="tick-all" class="form-check-input"/>
                                        </div>
                                    </th>
                                    <th class="py-3">{{ __('###') }}</th>
                                    <th class="py-3">{{ __('parcel.tracking_id') }}</th>
                                    <th class="py-3">{{ __('parcel.delivery_man') }}</th>
                                    <th class="py-3">{{ __('parcel.recipient_info') }}</th>
                                    <th class="py-3">{{ __('parcel.merchant') }}</th>
                                    <th class="py-3">{{ __('parcel.amount')}}</th>
                                    <th class="py-3">{{ __('parcel.priority') }}</th>
                                    <th class="py-3">{{ __('parcel.status') }}</th>
                                    @if(hasPermission('parcel_status_update') == true)
                                        <th class="py-3">{{ __('parcel.status_update') }}</th>
                                    @endif
                                    <th class="py-3">{{ __('parcel.payment')}}</th>
                                    <th class="py-3">{{ __('View Proof of Delivery')}}</th>
                                </tr>
                                </thead>
                                <tbody class="border-top-0">
                                <!-- Bulk Actions Row -->
                                @if(hasPermission('parcel_create'))
                                <tr class="bg-light bulk-actions-row">
                                    <td colspan="12" class="p-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <span class="me-3 fw-medium text-muted">{{ __('levels.bulk_actions') }}:</span>
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" id="bulkActionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-tasks me-1"></i> {{ __('levels.select_action') }}
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="bulkActionDropdown">
                                                        <li><a class="dropdown-item bulk-action" data-value="assignpickupbulk" href="#">{{ __('levels.assign_pickup') }}</a></li>
                                                        <li><a class="dropdown-item bulk-action" data-value="transfer_to_hub_multiple_parcel" href="#">{{ __('levels.hub_transfer') }}</a></li>
                                                        <li><a class="dropdown-item bulk-action" data-value="received_by_hub_multiple_parcel" href="#">{{ __('levels.received_by_hub') }}</a></li>
                                                        <li><a class="dropdown-item bulk-action" data-value="delivery_man_assign_multiple_parcel" href="#">{{ __('levels.delivery_man_assign') }}</a></li>
                                                        <li><a class="dropdown-item bulk-action" data-value="assign_return_merchant" href="#">{{ __('levels.assign_return_merchant') }}</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fas fa-info-circle me-1"></i>
                                                {{ __('levels.select_parcels_to_perform_bulk_actions') }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @php
                                    $i=1;
                                @endphp
                                @foreach($parcels as $parcel)
                                    <tr>
                                        <td class="parcel-index permission-check-box ps-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="parcels[]" value="{{ $parcel->id }}" class="common-key form-check-input" />
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                                <ul class="dropdown-menu shadow-sm border-0">
                                                    <li><a href="{{ route('parcel.details',$parcel->id) }}" class="dropdown-item"><i class="fa fa-eye text-primary me-2"></i> {{__('levels.view')}}</a></li>
                                                    <li><a href="{{ route('parcel.logs',$parcel->id) }}" class="dropdown-item"><i class="fas fa-history text-info me-2"></i> {{__('levels.parcel_logs')}}</a></li>
                                                    <li><a href="{{ route('parcel.clone',$parcel->id) }}" class="dropdown-item"><i class="fas fa-clone text-secondary me-2"></i> {{__('levels.clone')}}</a></li>
                                                    <li><a href="{{ route('parcel.print',$parcel->id) }}" class="dropdown-item"><i class="fas fa-print text-dark me-2"></i> {{__('levels.print')}}</a></li>
                                                    <li><a href="{{ route('parcel.print-label',$parcel->id) }}" target="_blank" class="dropdown-item"><i class="fas fa-tag text-dark me-2"></i> {{__('levels.print_label')}}</a></li>
                                                    @if(\App\Enums\ParcelStatus::DELIVERED !== $parcel->status && \App\Enums\ParcelStatus::PARTIAL_DELIVERED !== $parcel->status )
                                                        @if(hasPermission('parcel_update') == true)
                                                            <li><a href="{{route('parcel.edit',$parcel->id)}}" class="dropdown-item"><i class="fas fa-edit text-warning me-2"></i> {{__('levels.edit')}}</a></li>
                                                        @endif
                                                        @if(hasPermission('parcel_delete'))
                                                            <li>
                                                                <form id="delete" value="Test" action="{{route('parcel.delete',$parcel->id)}}" method="POST" data-title="{{ __('delete.parcel') }}">
                                                                    @method('DELETE')
                                                                    @csrf
                                                                    <input type="hidden" name="" value="Parcel" id="deleteTitle">
                                                                    <button type="submit" class="dropdown-item"><i class="fa fa-trash text-danger me-2"></i> {{ __('levels.delete') }}</button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-light text-dark me-2 px-3 py-2 rounded-pill">{{ $parcel->tracking_id }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="delivery-man-info">
                                                @php
                                                    // If filtering by delivery man, show the filtered delivery man
                                                    if($request->parcel_deliveryman_id) {
                                                        $filteredDeliveryMan = $parcel->parcelEvent()->where('delivery_man_id', $request->parcel_deliveryman_id)->latest()->first();
                                                        $deliveryMan = $filteredDeliveryMan;
                                                    } else {
                                                        // Otherwise, show the latest delivery man assignment
                                                        $deliveryMan = $parcel->parcelEvent()->whereNotNull('delivery_man_id')->latest()->first();
                                                    }
                                                @endphp
                                                @if($deliveryMan && $deliveryMan->deliveryMan)
                                                    <div class="fw-medium">{{$deliveryMan->deliveryMan->user->name}}</div>
                                                    <div class="text-muted small">{{$deliveryMan->deliveryMan->user->mobile}}</div>
                                                @else
                                                    <span class="text-muted small">Not Assigned</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="recipient-info">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="icon-circle bg-light text-primary me-2"><i class="fa fa-user"></i></span>
                                                    <span class="fw-medium">{{$parcel->customer_name}}</span>
                                                </div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="icon-circle bg-light text-success me-2"><i class="fas fa-phone"></i></span>
                                                    <span>{{$parcel->customer_phone}}</span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="icon-circle bg-light text-danger me-2"><i class="fas fa-map-marker-alt"></i></span>
                                                    <span class="text-muted small">{{$parcel->customer_address}}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="merchant-info">
                                                <div class="fw-medium mb-2">{{$parcel->merchant->business_name}}</div>
                                                <div class="text-muted small mb-1"><i class="fas fa-phone-alt me-1"></i> {{$parcel->merchant->user->mobile}}</div>
                                                <div class="text-muted small"><i class="fas fa-map-pin me-1"></i> {{$parcel->merchant->address}}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="amount-info">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">{{__('levels.cod')}}:</span>
                                                    <span class="fw-medium">{{settings()->currency}}{{$parcel->cash_collection}}</span>
                                                </div>
                                                
                                                @if ($parcel->return_to_courier == App\Enums\BooleanStatus::YES) 
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted">{{__('levels.return_charges')}}:</span>
                                                        <span class="fw-medium">{{settings()->currency}}{{$parcel->return_charges}}</span>
                                                    </div>
                                                @else
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="text-muted">{{__('levels.total_delivery_amount')}}:</span>
                                                        <span class="fw-medium">{{settings()->currency}}{{$parcel->total_delivery_amount}}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span class="text-muted">{{__('levels.vat_amount')}}:</span>
                                                        <span class="fw-medium">{{settings()->currency}}{{$parcel->vat_amount}}</span>
                                                    </div>
                                                @endif
                                                
                                                <div class="d-flex justify-content-between pt-2 border-top">
                                                    <span class="text-dark fw-medium">{{__('levels.current_payable')}}:</span>
                                                    <span class="fw-bold text-primary">{{settings()->currency}}{{$parcel->current_payable}}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input switch-id" type="checkbox" data-url="{{ route('parcel.priority.status') }}" data-id="{{ $parcel->id }}" role="switch" value="{{ $parcel->priority_type_id }}" @if($parcel->priority_type_id == 1) checked @else @endif>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="status-info">
                                                <div class="mb-2">{!! $parcel->parcel_status !!}</div>
                                                
                                                @if($parcel->partial_delivered && $parcel->status != \App\Enums\ParcelStatus::PARTIAL_DELIVERED)
                                                    <div class="mb-2">
                                                        <span class="badge bg-success rounded-pill">{{trans("parcelStatus." . \App\Enums\ParcelStatus::PARTIAL_DELIVERED)}}</span>
                                                    </div>
                                                @endif
                                                
                                                <div class="text-muted small">
                                                    <i class="far fa-clock me-1"></i> {{\Carbon\Carbon::parse($parcel->updated_at)->format('Y-m-d h:i A')}}
                                                </div>
                                            </div>
                                        </td>
                                        @if(hasPermission('parcel_status_update') == true)
                                            <td>
                                                @if(\App\Enums\ParcelStatus::DELIVERED !== $parcel->status && \App\Enums\ParcelStatus::PARTIAL_DELIVERED !== $parcel->status && \App\Enums\ParcelStatus::RETURN_RECEIVED_BY_MERCHANT !== $parcel->status && \App\Enums\ParcelStatus::RETURNED_MERCHANT !== $parcel->status)
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-edit me-1"></i> Update
                                                        </button>
                                                        <ul class="dropdown-menu shadow-sm border-0">
                                                            {!! parcelStatus($parcel) !!}
                                                        </ul>
                                                    </div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                           
                                            <td>
                                                @if ($parcel->invoice)
                                                    <div class="payment-info">
                                                        <div class="mb-2">
                                                            @if ($parcel->invoice->status == App\Enums\InvoiceStatus::PAID)
                                                                <span class="badge bg-success rounded-pill">{{ __('invoice.'.@$parcel->invoice->status) }}</span>
                                                            @else
                                                                <span class="badge bg-warning text-dark rounded-pill">{{ __('invoice.'.@$parcel->invoice->status) }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="fw-medium mb-1">{{ @$parcel->invoice->invoice_id }}</div>
                                                        @if ($parcel->invoice->status == App\Enums\InvoiceStatus::PAID)
                                                            <div class="text-muted small">
                                                                <i class="far fa-calendar-check me-1"></i> {{ @dateFormat(@$parcel->invoice->updated_at) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>

                                        @endif
                                        <td>
                                            @if( $parcel->status == \App\Enums\ParcelStatus::DELIVERED)
                                                <a href="{{route('parcel.deliveredInfo',$parcel->id)}}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" data-bs-placement="top" title="View">
                                                    <i class="fas fa-file-alt me-1"></i> {{ __('View') }}
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                    <div class="card-footer bg-white p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <p class="mb-0 text-muted">
                                    {!! __('Showing') !!}
                                    <span class="fw-medium">{{ $parcels->firstItem() }}</span>
                                    {!! __('to') !!}
                                    <span class="fw-medium">{{ $parcels->lastItem() }}</span>
                                    {!! __('of') !!}
                                    <span class="fw-medium">{{ $parcels->total() }}</span>
                                    {!! __('results') !!}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <nav aria-label="Page navigation">
                                    <div class="pagination justify-content-md-end">
                                        {{ $parcels->appends($request->all())->links() }}
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end data table  -->
        </div>
    </div>
    <!-- end wrapper  -->
    
    {{-- Move modals outside of .table-responsive to avoid layout glitches --}}
    @include('backend.parcel.pickup_assign_modal')
    @include('backend.parcel.pickup_re_schedule')
    @include('backend.parcel.received_by_pickup')
    @include('backend.parcel.transfer_to_hub')
    @include('backend.parcel.received_by_hub')
    @include('backend.parcel.delivery_man_assign')
    @include('backend.parcel.delivery_reschedule')
    @include('backend.parcel.partial_delivered_modal')
    @include('backend.parcel.delivered_modal')
    @include('backend.parcel.received_warehouse')
    @include('backend.parcel.return_to_qourier')
    @include('backend.parcel.return_assign_to_merchant')
    @include('backend.parcel.re_schedule_return_assign_to_merchant')
    @include('backend.parcel.return_received_by_merchant')
    @include('backend.parcel.transfer_to_hub_multiple_parcel')
    @include('backend.parcel.received_by_hub_multiple_parcel')
    @include('backend.parcel.assign_pickup_bulk')
    @include('backend.parcel.delivery_man_assign_multiple_parcel')
    @include('backend.parcel.assign_return_to_merchant_bulk')
@endsection()

<!-- css  -->
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        /* General Styles */
        body {
            color: #333;
        }
        
        .card {
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            border: none;
            margin-bottom: 24px;
            overflow: hidden;
        }
        
        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background-color: #fff;
        }
        
        .search-form {
            min-width: 300px;
        }
        
        .search-form .input-group {
            max-width: 400px;
        }
        
        .search-form .input-group .form-control {
            border-radius: 6px 0 0 6px;
        }
        
        .search-form .input-group .btn {
            border-radius: 0 6px 6px 0;
        }
        
        .action-buttons {
            flex-wrap: wrap;
        }
        
        .action-buttons .btn {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
            white-space: nowrap;
        }
        
        .action-buttons .dropdown-toggle {
            min-width: 140px;
        }
        
        /* Bulk Actions Row Styling */
        .bg-light {
            background-color: #f8f9fa !important;
        }
        
        .bulk-actions-row {
            border-bottom: 1px solid #dee2e6;
        }
        
        .bulk-actions-row td {
            border-top: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
        }
        
        .bulk-actions-row .dropdown-toggle {
            min-width: 160px;
        }
        
        @media (max-width: 991px) {
            .search-form {
                min-width: 100%;
                margin-top: 1rem;
            }
            
            .action-buttons {
                margin-top: 1rem;
                justify-content: flex-start;
            }
        }
        
        @media (max-width: 576px) {
            .action-buttons .btn {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }
            
            .action-buttons .dropdown-toggle {
                min-width: 120px;
            }
        }
        
        /* Merged Card Filter Styles */
        .card-header .collapse {
            transition: all 0.3s ease;
        }
        
        .card-header .form-group {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .card-header .form-label {
            color: #555;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .card-header .form-control,
        .card-header .form-select {
            border: 1px solid #ced4da;
            border-radius: 6px;
            height: 38px;
            padding: 0.375rem 0.75rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .card-header .form-control:focus,
        .card-header .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            border-color: #86b7fe;
            outline: 0;
        }
        
        .card-header .form-control::placeholder {
            color: #6c757d;
            opacity: 1;
        }
        
        /* Form Controls */
        .form-control, .form-select {
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
        }
        
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
        }
        
        .input-group-text {
            border-radius: 6px 0 0 6px;
        }
        
        .form-label {
            font-weight: 500;
        }
        
        /* Buttons */
        .btn {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
        }
        
        .btn-outline-primary {
            color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: #fff;
        }
        
        /* Table Styles */
        .table {
            color: #333;
        }
        
        .table th {
            font-weight: 600;
            color: #555;
            white-space: nowrap;
        }
        
        .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }
        
        .table tr:hover {
            background-color: rgba(0, 0, 0, 0.01);
        }
        
        /* Custom Elements */
        .icon-circle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            font-size: 0.875rem;
        }
        
        .recipient-info, .merchant-info, .amount-info, .payment-info, .status-info {
            min-width: 180px;
        }
        
        .amount-info {
            min-width: 220px;
        }
        
        .delivery-man-info {
            min-width: 120px;
            max-width: 150px;
        }
        
        .delivery-man-info .fw-medium {
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .delivery-man-info .text-muted {
            font-size: 0.8rem;
        }
        
        /* Dropdown Menu */
        .dropdown-menu {
            padding: 0.5rem 0;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
        }
        
        .dropdown-item:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
        
        /* Badges */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
        
        /* Select2 Customization */
        .select2-container .select2-selection--single {
            height: 38px !important;
            border-radius: 6px;
            border-color: #ced4da;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 12px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        
        /* Gap utility */
        .gap-2 {
            gap: 0.5rem;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .collapse.show {
            animation: fadeIn 0.3s ease forwards;
        }
        
        /* Table Dropdown Styles */
        tbody .dropdown {
            position: relative;
        }
        
        tbody .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            min-width: 160px;
            padding: 0.5rem 0;
            margin: 0.125rem 0 0;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
            display: none;
        }
        
        tbody .dropdown-menu.show {
            display: block;
        }
        
        tbody .dropdown-item {
            display: block;
            width: 100%;
            padding: 0.25rem 1rem;
            clear: both;
            font-weight: 400;
            color: #212529;
            text-align: inherit;
            text-decoration: none;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
        }
        
        tbody .dropdown-item:hover,
        tbody .dropdown-item:focus {
            color: #1e2125;
            background-color: #e9ecef;
        }
    </style>
@endpush
<!-- js  -->
@push('scripts')
    <script src="{{ static_asset('js/onscan.js/onscan.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
    <script>
        var merchantUrl = '{{ route('parcel.merchant.get') }}';
        var merchantID = '{{ $request->parcel_merchant_id }}';
        var deliveryManID = '{{ $request->parcel_deliveryman_id }}';
        var pickupManID = '{{ $request->parcel_pickupman_id }}';
        var dateParcel = '{{ $request->parcel_date }}';
        
        // Handle the new bulk action dropdown
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap 5 dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'))
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl)
            });
            
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            
            // Handle filter toggle button
            const filterToggleBtn = document.querySelector('[data-bs-target="#filterCollapse"]');
            if (filterToggleBtn) {
                filterToggleBtn.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (this.getAttribute('aria-expanded') === 'true') {
                        icon.classList.remove('fa-filter');
                        icon.classList.add('fa-times');
                    } else {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-filter');
                    }
                });
            }
            
            // Handle table action dropdowns
            const tableActionDropdowns = document.querySelectorAll('tbody .dropdown-toggle');
            tableActionDropdowns.forEach(dropdown => {
                dropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Close other dropdowns
                    const otherDropdowns = document.querySelectorAll('tbody .dropdown-menu.show');
                    otherDropdowns.forEach(menu => {
                        menu.classList.remove('show');
                    });
                    
                    // Toggle current dropdown
                    const menu = this.nextElementSibling;
                    if (menu) {
                        menu.classList.toggle('show');
                    }
                });
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    const openDropdowns = document.querySelectorAll('tbody .dropdown-menu.show');
                    openDropdowns.forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });
            
            // Handle bulk action clicks
            const bulkActions = document.querySelectorAll('.bulk-action');
            bulkActions.forEach(action => {
                action.addEventListener('click', function(e) {
                    e.preventDefault();
                    const actionValue = this.getAttribute('data-value');
                    handleBulkAction(actionValue);
                });
            });
            
            function showBsModal(modalId) {
                var el = document.getElementById(modalId);
                if (!el) return;
                var inst = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
                inst.show();
            }

            function handleBulkAction(actionValue) {
                // This replicates the existing select change handler
                if (actionValue === 'assignpickupbulk') {
                    showBsModal('assignpickupbulk');
                } else if (actionValue === 'transfer_to_hub_multiple_parcel') {
                    showBsModal('transfer_to_hub_multiple_parcel');
                } else if (actionValue === 'received_by_hub_multiple_parcel') {
                    showBsModal('received_by_hub_multiple_parcel');
                } else if (actionValue === 'delivery_man_assign_multiple_parcel') {
                    showBsModal('delivery_man_assign_multiple_parcel');
                } else if (actionValue === 'assign_return_merchant') {
                    showBsModal('assign_return_to_merchant_bulk');
                }
            }
        });
    </script>
    <script src="{{ static_asset('backend/js/parcel/custom.js') }}"></script>
    <script src="{{ static_asset('backend/js/parcel/filter.js') }}"></script>
    <script src="{{ static_asset('backend/js/parcel/priorityChange.js') }}"></script>
 
@endpush
