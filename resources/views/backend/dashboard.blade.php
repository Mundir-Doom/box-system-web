@extends('backend.partials.master')
@section('title')
    {{ __('menus.dashboard') }}
@endsection
@section('maincontent')
    <div class="container-fluid dashboard-content ">

        <!-- pageheader  -->
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="page-header">
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}"
                                        class="breadcrumb-link">{{ __('menus.dashboard') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ settings()->name }}
                                    {{ __('menus.dashboard') }} </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- end pageheader  -->
        <div class="ecommerce-widget">

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card dashboard-header-card">
                        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="m-0 dashboard-title">{{ settings()->name }} {{ __('menus.dashboard') }}</h5>
                            <form action="{{ route('dashboard.index', ['test' => 'custom']) }}" method="get" class="d-flex align-items-center dashboard-filter-form">
                                <input type="hidden" name="days" value="custom" />
                                <input type="text" name="filter_date" placeholder="YYYY-MM-DD" autocomplete="off"
                                       class="form-control dashboard-filter-input date_range_picker group-input"
                                       value="{{ $request->filter_date }}" required />
                                <button type="submit" class="btn btn-primary group-btn ml-0">{{ __('levels.filter') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row header-summery">


                @if (hasPermission('total_parcel') == true)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <a href="{{ route('parcel.filter',['parcel_date' => $request->date]) }}" class="d-block">
                            <div class="card border-3 border-top border-top-primary total-card-color  ">
                                <div class="card-body total-card-body">
                                    <div class="text-center d-flex">
                                        <label class="icon p-10px">
                                            <i class="fa fa-box-open"></i>
                                        </label>
                                        <div class="box-content w-100 text-left">
                                            <h5 class="text-muted">{{ __('dashboard.total_parcel') }}</h5>
                                            <div class="metric-value d-inline-block">
                                                <h1 class="mb-1">{{ $data['total_parcel'] }}</h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                @if (hasPermission('total_user') == true)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="card border-3 border-top border-top-primary total-card-color  ">
                            <a href="{{ route('users.filter',['date' => $request->date]) }}" class="d-block">
                                <div class="card-body total-card-body">
                                    <div class="text-center d-flex  ">
                                        <label class="icon p-10px">
                                            <i class="fa fa-users"></i>
                                        </label>
                                        <div class="box-content w-100 text-left">
                                            <h5 class="text-muted">{{ __('dashboard.total_user') }} </h5>
                                            <div class="metric-value d-inline-block">
                                                <h1 class="mb-1">{{ $data['total_user'] }}</h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endif

                @if (hasPermission('total_merchant') == true)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <a href="{{ route('merchant.index',['date' => $request->date]) }}" class="d-block">
                            <div class="card border-3 border-top border-top-primary total-card-color  ">
                                <div class="card-body total-card-body">
                                    <div class="text-center d-flex">
                                        <label class="icon p-10px">
                                            <i class="fa fa-users"></i>
                                        </label>
                                        <div class="box-content w-100 text-left">
                                            <h5 class="text-muted">{{ __('dashboard.total_merchant') }}</h5>
                                            <div class="metric-value d-inline-block">
                                                <h1 class="mb-1">{{ $data['total_merchant'] }}</h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif


                @if (hasPermission('total_delivery_man') == true)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <a href="{{ route('deliveryman.index',['date' => $request->date]) }}" class="d-block">
                            <div class="card border-3 border-top border-top-primary total-card-color  ">
                                <div class="card-body total-card-body">
                                    <div class="text-center d-flex">
                                        <label class="icon p-10px">
                                            <i class="fas fa-users"></i>
                                        </label>
                                        <div class="box-content w-100 text-left">
                                            <h5 class="text-muted">{{ __('dashboard.total_delivery_man') }}</h5>
                                            <div class="metric-value d-inline-block">
                                                <h1 class="mb-1">{{ $data['total_delivery_man'] }}</h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif


                @if (hasPermission('total_hubs') == true)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <a href="{{ route('hubs.filter',['date' => $request->date]) }}" class="d-block">
                            <div class="card border-3 border-top border-top-primary total-card-color  ">
                                <div class="card-body total-card-body">
                                    <div class="text-center d-flex">
                                        <label class="icon p-10px">
                                            <i class="fas fa-warehouse"></i>
                                        </label>
                                        <div class="box-content w-100 text-left">
                                            <h5 class="text-muted">{{ __('dashboard.total_hubs') }}</h5>
                                            <div class="metric-value d-inline-block">
                                                <h1 class="mb-1">{{ $data['total_hubs'] }}</h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif


                @if (hasPermission('total_accounts') == true)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <a href="{{ route('accounts.index',['date' => $request->date]) }}" class="d-block">
                            <div class="card border-3 border-top border-top-primary total-card-color  ">
                                <div class="card-body total-card-body">
                                    <div class="text-center d-flex">
                                        <label class="icon p-10px">
                                            <i class="fa fa-credit-card"></i>
                                        </label>
                                        <div class="box-content w-100 text-left">
                                            <h5 class="text-muted">{{ __('dashboard.total_accounts') }}</h5>
                                            <div class="metric-value d-inline-block">
                                                <h1 class="mb-1">{{ $data['total_accounts'] }}</h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                @if (hasPermission('total_partial_deliverd') == true)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <a href="{{ route('parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::PARTIAL_DELIVERED,'parcel_date'=>$request->date]) }}"
                            class="d-block">
                            <div class="card border-3 border-top border-top-primary total-card-color ">
                                <div class="card-body total-card-body">
                                    <div class="text-center total-card d-flex">
                                        <label class="icon p-10px">
                                            <i class="fas fa-handshake"></i>
                                        </label>
                                        <div class="box-content w-100 text-left">
                                            <h5 class="text-muted">{{ __('dashboard.total_partial_deliverd') }} </h5>
                                            <div class="metric-value d-inline-block">
                                                <h1 class="mb-1">{{ $data['total_partial_deliverd'] }}</h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                @if (hasPermission('total_parcels_deliverd') == true)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <a href="{{ route('parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::DELIVERED,'parcel_date'=>$request->date]) }}"
                            class="d-block">
                            <div class="card border-3 border-top border-top-primary total-card-color ">
                                <div class="card-body total-card-body">
                                    <div class="text-center d-flex">
                                        <label class="icon p-10px">
                                            <i class="fas fa-handshake"></i>
                                        </label>
                                        <div class="box-content w-100 text-left">
                                            <h5 class="text-muted">{{ __('dashboard.total_deliverd') }}</h5>
                                            <div class="metric-value d-inline-block">
                                                <h1 class="mb-1">{{ $data['total_deliverd'] }}</h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif


            </div>
            {{-- salary and account section --}}

            @if (hasPermission('all_statements') == true)
                <div class="row mb-4">
                    <div class="col-md-4">
                        <ul class="list-group mt-2">
                            <li class="list-group-item profile-list-group-item text-center">
                                <span class="font-weight-bold ">
                                    @if(app()->getLocale() == 'ar')
                                        {{ __('dashboard.statements') }} {{ __('dashboard.delivery_man') }}
                                    @else
                                        {{ __('dashboard.delivery_man') }} {{ __('dashboard.statements') }}
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold"> {{ __('income.title') }} </span>
                                <span class="float-right"> {{ settings()->currency }}{{ $d_income }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('expense.title') }} </span>
                                <span class="float-right"> {{ settings()->currency }}{{ $d_expense }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold"> {{ __('dashboard.balance') }}</span>
                                <span class="float-right"> {{ settings()->currency }}{{ $d_income - $d_expense }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <ul class="list-group mt-2">
                            <li class="list-group-item profile-list-group-item text-center">
                                <span class=" font-weight-bold">
                                    @if(app()->getLocale() == 'ar')
                                        {{ __('dashboard.statements') }} {{ __('dashboard.merchant') }}
                                    @else
                                        {{ __('dashboard.merchant') }} {{ __('dashboard.statements') }}
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold"> {{ __('income.title') }} </span>
                                <span class="float-right"> {{ settings()->currency }}{{ $m_income }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('expense.title') }} </span>
                                <span class="float-right"> {{ settings()->currency }}{{ $m_expense }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold"> {{ __('dashboard.balance') }}</span>
                                <span class="float-right"> {{ settings()->currency }}{{ $m_income - $m_expense }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <ul class="list-group mt-2 ">
                            <li class="list-group-item profile-list-group-item text-center">
                                <span class="font-weight-bold">
                                    @if(app()->getLocale() == 'ar')
                                        {{ __('dashboard.statements') }} {{ __('hub.title') }}
                                    @else
                                        {{ __('hub.title') }} {{ __('dashboard.statements') }}
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold"> {{ __('income.title') }} </span>
                                <span class="float-right"> {{ settings()->currency }}{{ $h_income }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('expense.title') }} </span>
                                <span class="float-right"> {{ settings()->currency }}{{ $h_expense }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold"> {{ __('dashboard.balance') }}</span>
                                <span class="float-right"> {{ settings()->currency }}{{ $h_income - $h_expense }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif

            <div class="row">
                @if (hasPermission('income_expense_charts') == true)
                    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="apexcharts" id="apexincomeexpense"></div>
                            </div>
                            <div class="card-footer">
                                <p class="display-7 font-weight-bold">
                                    <span class="legend-text text-primary d-inline-block">{{ settings()->currency }}
                                        {{ $data['income'] }}</span>
                                    <span class="text-secondary float-right">{{ settings()->currency }}
                                        {{ $data['expense'] }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (hasPermission('courier_revenue_charts') == true)
                    <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card">
                            <div class="card-body courier-pie-charts">
                                <div class="apexcharts" id="apexpiecourierrevenue"></div>
                            </div>
                            <div class="card-footer">
                                <p class="display-7 font-weight-bold">
                                    <span class="text-primary d-inline-block">{{ settings()->currency }}
                                        {{ $data['courier_income'] }}</span>
                                    <span class="text-secondary float-right">{{ settings()->currency }}
                                        {{ $data['courier_expense'] }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    {{-- courier revinue pie charts --}}
                @endif
            </div>

            
            <!-- recent parcel  -->

            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="m-0">{{ __('dashboard.recent') }} {{ __('parcel.title') }}</h5>
                                <a href="{{ route('parcel.index') }}" class="btn btn-sm btn-outline-primary">{{ __('levels.view') }}</a>
                            </div>
                            @php($recent = $data['recent_parcels'] ?? collect())
                            @if($recent->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('parcel.tracking_id') }}</th>
                                                <th>{{ __('parcel.customer_name') }}</th>
                                                <th class="text-right">{{ __('parcel.cash_collection') }}</th>
                                                <th>{{ __('parcel.updated_on') }}</th>
                                                <th>{{ __('parcel.status') }}</th>
                                                <th class="text-right">{{ __('levels.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recent as $parcel)
                                                <tr>
                                                    <td><a href="{{ route('parcel.details', $parcel->id) }}">{{ $parcel->tracking_id }}</a></td>
                                                    <td>{{ $parcel->customer_name }}</td>
                                                    <td class="text-right">{{ settings()->currency }}{{ number_format($parcel->cash_collection, 2) }}</td>
                                                    <td>{{ optional($parcel->updated_at)->format('Y-m-d H:i') }}</td>
                                                    <td>{!! $parcel->parcel_status ?? '' !!}</td>
                                                    <td class="text-right">
                                                        <a href="{{ route('parcel.details', $parcel->id) }}" class="btn btn-sm btn-secondary">{{ __('levels.view') }}</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-muted">No recent parcels.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if (hasPermission('calendar_read') == true)
                <div class="row mb-5">
                    <div class=" col-12 ">
                        <div class="card mb-0 mt-4">
                            <div class="card-body ">
                                <div style="overflow:hidden;">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="datetimepicker12"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif


        </div>
    </div>

    </div>
    </div>
    <!-- end wrapper  -->
@endsection

<!-- css  -->
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="{{ static_asset('backend/vendor/calender/main.css') }}" />
    <!-- Tempus Dominus Styles -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/eonasdan-bootstrap-datetimepicker/4.17.49/css/bootstrap-datetimepicker.min.css"
        integrity="sha512-ipfmbgqfdejR27dWn02UftaAzUfxJ3HR4BDQYuITYSj6ZQfGT1NulP4BOery3w/dT2PYAe3bG5Zm/owm7MuFhA==" crossorigin="anonymous"
        referrerpolicy="no-referrer" />
    <style>
        .notification .nav-link.nav-icons {
            margin-top: 0px !important;
        }

        .admin-panel.notification .nav-link.nav-icons .indicator {
            top: 15px !important;
        }
    </style>
@endpush
<!-- js  -->
@push('scripts')
    <script type="text/javascript" src="{{ static_asset('backend/js/charts/apexcharts.js') }}"></script>
    @include('backend.dashboard-charts')
    @include('backend.calender-js')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript"
        src="{{ static_asset('backend/js/date-range-picker/dashboard-date-range-picker-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- datetime -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"
        crossorigin="anonymous"></script>


    <script type="text/javascript">
        $('#datetimepicker12').datetimepicker({
            inline: true,
            sideBySide: true
        });
    </script>
@endpush
