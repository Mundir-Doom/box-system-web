@extends('backend.partials.master')
@section('title')
   {{ __('reports.merchant_hub_deliveryman') }}
@endsection
@section('maincontent')
<!-- wrapper  -->
<div class="container-fluid  dashboard-content">
    <!-- pageheader -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('menus.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{ __('reports.title') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('merchant.hub.deliveryman.reports') }}" class="breadcrumb-link">{{ __('reports.merchant_hub_deliveryman') }}</a></li>

                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->
    {{-- Reports tabs: General Dashboard vs Detailed Reports --}}
    <div class="row mb-3">
        <div class="col-12">
            <ul class="nav nav-tabs" id="reportsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="general-tab" href="#" role="tab">{{ __('menus.dashboard') }}</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="detailed-tab" href="#" role="tab">{{ __('reports.title') }}</a>
                </li>
            </ul>
        </div>
    </div>

    {{-- General Dashboard Tab Content --}}
    <div class="row" id="report-general">
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                        <h5 class="m-0">{{ __('reports.title') }} {{ __('reports.dashboard') }}</h5>
                        <span class="text-muted small">
                            {{ $request->parcel_date ?? '' }}
                        </span>
                    </div>
                    @php
                        // Compute scoped totals for KPIs based on selected filters and date range
                        $kpiIncome = 0; $kpiExpense = 0;
                        $kpiLabels = [];
                        if (!empty($request->parcel_date)) {
                            $parts = explode('To', $request->parcel_date);
                            if (is_array($parts)) {
                                $from = \Carbon\Carbon::parse(trim($parts[0]))->startOfDay();
                                $to   = \Carbon\Carbon::parse(trim($parts[1]))->startOfDay();
                                $periodDays = min(30, $from->diffInDays($to) + 1); // cap for safety
                                for ($i = 0; $i < $periodDays; $i++) {
                                    $kpiLabels[] = $from->copy()->addDays($i)->format('Y-m-d');
                                }
                            }
                        }
                        if (empty($kpiLabels)) {
                            for ($i = 7; $i >= 0; $i--) {
                                $kpiLabels[] = \Carbon\Carbon::now()->subDays($i)->format('Y-m-d');
                            }
                        }
                        $ut = (int)($request->user_type ?? 0);
                        foreach ($kpiLabels as $d) {
                            if ($ut === 1 && !empty($request->merchant_id)) {
                                $kpiIncome += dayMerchantIncomeById($d, $request->merchant_id);
                                $kpiExpense += dayMerchantExpenseById($d, $request->merchant_id);
                            } elseif ($ut === 2 && !empty($request->hub_id)) {
                                $kpiIncome += dayHubIncomeById($d, $request->hub_id);
                                $kpiExpense += dayHubExpenseById($d, $request->hub_id);
                            } elseif ($ut === 3 && !empty($request->delivery_man_id)) {
                                $kpiIncome += dayDeliverymanIncomeById($d, $request->delivery_man_id);
                                $kpiExpense += dayDeliverymanExpenseById($d, $request->delivery_man_id);
                            } else {
                                $kpiIncome += dayIncomeCount($d);
                                $kpiExpense += dayExpenseCount($d);
                            }
                        }
                    @endphp
                    <div class="row text-center">
                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <div class="p-3 rounded border h-100">
                                <div class="text-muted">{{ __('income.title') }}</div>
                                <div class="h3 m-0">{{ settings()->currency }}{{ number_format($kpiIncome, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <div class="p-3 rounded border h-100">
                                <div class="text-muted">{{ __('expense.title') }}</div>
                                <div class="h3 m-0">{{ settings()->currency }}{{ number_format($kpiExpense, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 rounded border h-100">
                                <div class="text-muted">{{ __('dashboard.balance') }}</div>
                                <div class="h3 m-0">{{ settings()->currency }}{{ number_format($kpiIncome - $kpiExpense, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div id="reportsRevTrend" class="apexcharts"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12 mt-3 mt-xl-0">
            <div class="card">
                <div class="card-body">
                    <div id="reportsSummaryDonut" class="apexcharts"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Reports Tab Content (existing UI) --}}
    <div class="row d-none" id="report-detailed">
        <!-- data table  -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card" style="margin-bottom: 0px">
                <div class="card-body">
                    <form action="{{route('reports.mhd.reports')}}"  method="GET">
                        @csrf
                        <div class="row">
                            <div class="form-group col-xl-3 col-lg-4 col-sm-6">
                                <label for="parcel_date">{{ __('parcel.date') }}</label>
                                <input type="text" autocomplete="off" id="date" name="parcel_date" class="form-control date_range_picker" value="{{ old('parcel_date',$request->date) }}">

                                @error('parcel_date')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-xl-3 col-lg-4 col-sm-6 ">
                                <label for="parcel_date">{{ __('reports.user_types') }}</label> <span class="text-danger">*</span>
                                <select class="form-control" name="user_type" id="user_type">
                                    <option  disabled selected >{{ __('menus.select') }} {{ __('reports.user_types') }}</option>
                                    <option value="1" @if($request->user_type == 1) selected @endif >{{ __('reports.merchant') }}</option>
                                    <option value="2" @if($request->user_type == 2) selected @endif>{{ __('reports.hub') }}</option>
                                    <option value="3" @if($request->user_type == 3) selected @endif>{{ __('reports.delivery_man') }}</option>
                                </select>
                                @error('user_type')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            {{-- merchant --}}
                            <div class="form-group merchant_search col-xl-3 col-lg-4 col-sm-6" id="merchantType"  >
                                <label for="parcelMerchantid_">{{ __('parcel.merchant') }}</label>
                                <select style="width: 100%" id="parcelMerchantid_"  name="merchant_id" class="form-control @error('merchant_id') is-invalid @enderror" data-url="{{ route('parcel.merchant.shops') }}">
                                    <option value=""> {{ __('menus.select') }} {{ __('merchant.title') }}</option>
                                    @if ($request->merchant_id)
                                        @foreach ($merchants as $merchant)
                                            <option value="{{ $merchant->id }}" @if($merchant->id == $request->merchant_id) selected @endif > {{ $merchant->business_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <input type="hidden" id="merchant_amount" value="0"/>
                                <div  class="merchant_balance active"></div>
                                @error('merchant_id')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            {{-- Hub Search --}}
                            <div class="form-group hub_search col-xl-3 col-lg-4 col-sm-6" id="hubType">
                                <label for="income_hub_id">{{ __('parcel.hub') }}</label>
                                <select style="width: 100%" id="income_hub_id"  name="hub_id" class="form-control @error('hub_id') is-invalid @enderror">
                                    <option value="">{{ __('menus.select') }} {{ __('hub.title') }}</option>
                                    @if ($request->hub_id)
                                        @foreach ($hubs as $hub)
                                            <option value="{{ $hub->id }}" @if($hub->id == $request->hub_id) selected @endif > {{ $hub->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <input type="hidden" id="hub_amount" value="0"/>
                                <div  class="hub_balance active"></div>
                                @error('hub_id')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            {{-- delivery man --}}
                            <div class="form-group delivery_man_search col-xl-3 col-lg-4 col-sm-6" id="deliverymanType">
                                <label for="parcelDeliveryManID_">{{ __('parcel.deliveryman') }}</label>
                                <select style="width: 100%" id="parcelDeliveryManID_"  name="delivery_man_id" data-url="{{ route('parcel.deliveryman.search') }}" class="form-control @error('delivery_man_id') is-invalid @enderror">
                                    <option value=""> {{ __('menus.select') }} {{ __('deliveryman.title') }}</option>
                                    @if ($request->delivery_man_id)
                                        @foreach ($deliverymans as $deliveryman)
                                            <option value="{{ $deliveryman->id }}" @if($deliveryman->id == $request->delivery_man_id) selected @endif > {{ $deliveryman->user->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <input type="hidden" id="deliveryman_amount" value="0"/>
                                <div  class="deliveryman_balance active"></div>
                                @error('delivery_man_id')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-xl-3 col-lg-4 col-sm-6 pt-4">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12  pl-0 d-flex justify-content">
                                    <button type="submit" class="btn btn-space btn-primary"><i class="fa fa-filter"></i> {{ __('levels.filter') }}</button>
                                    <a href="{{ route('parcel.reports') }}" class="btn btn-space btn-secondary"><i class="fa fa-eraser"></i> {{ __('levels.clear') }}</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @if(isset($request->user_type))
                @if($request->user_type == 1 )
                    @include('backend.reports.merchant-hub-deliveryman.merchant')
                @elseif ($request->user_type == 2)
                @include('backend.reports.merchant-hub-deliveryman.hub')
                @elseif ($request->user_type == 3)
                    @include('backend.reports.merchant-hub-deliveryman.deliveryman')
                @endif
            @endif
        </div>
        <!-- end data table  -->
    </div>
</div>
<!-- end wrapper  -->
@endsection()
<!-- css  -->
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        #selectAssignType .select2-container .select2-selection--single {
        height: 32px !important;
        }
        .mhd dl.row.card-header {
        padding-left: 0px;
        padding-bottom: 0px;
    }

    .mhd.deliveryman dl.row.card-header {
        padding-left: 0px;
        padding-bottom: 10px;
        padding-top: 10px!important;
    }
    </style>
@endpush
<!-- js  -->
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
    <script>
        var merchantUrl = '{{ route('parcel.merchant.get') }}';
        var merchantID = '{{ $request->parcel_merchant_id }}';
        var deliveryManID = '{{ $request->parcel_deliveryman_id }}';
        var pickupManID = '{{ $request->parcel_pickupman_id }}';
        var dateParcel = '{{ $request->parcel_date }}';
    </script>
    <script src="{{ static_asset('backend/js/parcel/filter.js') }}"></script>

    <script src="{{ static_asset('backend/js/reports/print.js') }}"></script>
    <script src="{{ static_asset('backend/js/reports/jquery.table2excel.min.js') }}"></script>
    <script type="text/javascript">
        var hubUrl      = '{{ route('parcel.hub.get') }}';
    </script>
    <script src="{{ static_asset('backend/js/reports/reports.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript">
        $("#month").datepicker( {
            format: "mm-yyyy",
            startView: "months",
            minViewMode: "months"
        });
        @if($request->user_type == 1)
            $('#hubType').hide();
            $('#deliverymanType').hide();
        @else
            $('#merchantType').hide();
        @endif;
        @if($request->user_type == 2)
            $('#merchantType').hide();
            $('#deliverymanType').hide();
        @else
            $('#hubType').hide();
        @endif;
        @if($request->user_type == 3 )
            $('#merchantType').hide();
            $('#hubType').hide();
        @else
            $('#deliverymanType').hide();
        @endif;
    </script>

    {{-- Charts for General Dashboard tab --}}
    <script type="text/javascript" src="{{ static_asset('backend/js/charts/apexcharts.js') }}"></script>
    <script>
        // Tabs toggle
        (function(){
            var gen = document.getElementById('general-tab');
            var det = document.getElementById('detailed-tab');
            var genBox = document.getElementById('report-general');
            var detBox = document.getElementById('report-detailed');
            if(gen && det){
                gen.addEventListener('click', function(e){ e.preventDefault(); gen.classList.add('active'); det.classList.remove('active'); genBox.classList.remove('d-none'); detBox.classList.add('d-none'); });
                det.addEventListener('click', function(e){ e.preventDefault(); det.classList.add('active'); gen.classList.remove('active'); detBox.classList.remove('d-none'); genBox.classList.add('d-none'); });
            }
        })();

        @php
            // Build labels from date range or last 8 days
            $labels = [];
            if (!empty($request->parcel_date)) {
                $parts = explode('To', $request->parcel_date);
                if (is_array($parts)) {
                    $from = \Carbon\Carbon::parse(trim($parts[0]))->startOfDay();
                    $to   = \Carbon\Carbon::parse(trim($parts[1]))->startOfDay();
                    $days = $from->diffInDays($to);
                    $maxDays = min(14, $days + 1);
                    for ($i = 0; $i < $maxDays; $i++) {
                        $labels[] = $from->copy()->addDays($i)->format('Y-m-d');
                    }
                }
            }
            if (empty($labels)) {
                for ($i = 7; $i >= 0; $i--) {
                    $labels[] = \Carbon\Carbon::now()->subDays($i)->format('Y-m-d');
                }
            }

            $seriesIncome = [];
            $seriesExpense = [];
            $totalIncome = 0; $totalExpense = 0;
            $ut = (int)($request->user_type ?? 0);
            foreach ($labels as $d) {
                if ($ut === 1 && !empty($request->merchant_id)) {
                    $inc = dayMerchantIncomeById($d, $request->merchant_id);
                    $exp = dayMerchantExpenseById($d, $request->merchant_id);
                } elseif ($ut === 2 && !empty($request->hub_id)) {
                    $inc = dayHubIncomeById($d, $request->hub_id);
                    $exp = dayHubExpenseById($d, $request->hub_id);
                } elseif ($ut === 3 && !empty($request->delivery_man_id)) {
                    $inc = dayDeliverymanIncomeById($d, $request->delivery_man_id);
                    $exp = dayDeliverymanExpenseById($d, $request->delivery_man_id);
                } else {
                    $inc = dayIncomeCount($d);
                    $exp = dayExpenseCount($d);
                }
                $seriesIncome[] = $inc; $seriesExpense[] = $exp; $totalIncome += $inc; $totalExpense += $exp;
            }
        @endphp

        var labels = [@foreach($labels as $d) '{{ $d }}', @endforeach];
        var incomeSeries = [@foreach($seriesIncome as $v) {{ (float)$v }}, @endforeach];
        var expenseSeries = [@foreach($seriesExpense as $v) {{ (float)$v }}, @endforeach];

        var revTrendOptions = {
            series: [
                { name: '{{ __("income.title") }}', type: 'area', data: incomeSeries },
                { name: '{{ __("expense.title") }}', type: 'area', data: expenseSeries }
            ],
            colors:['#2E93fA', '#ff407b'],
            chart: { height: 320, type: 'area' },
            stroke: { curve: 'smooth' },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.6, stops: [0, 100] } },
            title: { text: '{{ __("dashboard.revenue") }}' },
            labels: labels,
            markers: { size: 0 },
            legend: { position: 'top' },
            tooltip: { shared: true, intersect: false }
        };
        new ApexCharts(document.querySelector('#reportsRevTrend'), revTrendOptions).render();

        var donutOptions = {
            series: [{{ (float)$totalIncome }}, {{ (float)$totalExpense }}],
            chart: { type: 'donut', height: 300 },
            labels: ["{{ __('income.title') }}", "{{ __('expense.title') }}"],
            colors: ['#2E93fA', '#ff407b'],
            legend: { position: 'bottom' }
        };
        new ApexCharts(document.querySelector('#reportsSummaryDonut'), donutOptions).render();
    </script>
 @endpush
