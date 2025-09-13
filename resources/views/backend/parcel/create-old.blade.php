@extends('backend.partials.master')
@section('title')
    {{ __('parcel.title') }} {{ __('levels.add') }}
@endsection
@section('maincontent')
    <div class="container-fluid  dashboard-content">
        <!-- pageheader -->
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="page-header">
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                        class="breadcrumb-link">{{ __('parcel.dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('parcel.index') }}"
                                        class="breadcrumb-link">{{ __('parcel.title') }}</a></li>
                                <li class="breadcrumb-item"><a href=""
                                        class="breadcrumb-link active">{{ __('levels.create') }}</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-lg-12 col-xl-8">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <h2 class="modern-title">
                            <i class="fas fa-plus-circle"></i>
                            {{ __('parcel.create_parcel') }}
                        </h2>
                        <div class="pricing-toggle">
                            <label class="toggle-switch">
                                <input type="checkbox" id="pricing-method-toggle">
                                <span class="toggle-slider">
                                    <span class="toggle-label">Fixed</span>
                                    <span class="toggle-label">Distance</span>
                                </span>
                            </label>
                            <small class="toggle-description">Switch between fixed and distance-based pricing</small>
                        </div>
                    </div>
                    <div class="card-body-modern">
                        <form action="{{ route('parcel.store') }}" method="POST" enctype="multipart/form-data" id="basicform">
                            @csrf
                            <input type="hidden" id="pricing_method" name="pricing_method" value="fixed">
                            
                            <!-- Basic Information Section -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-info-circle"></i>
                                    Basic Information
                                </h3>
                                <div class="form-grid">
                                    <div class="form-field">
                                        <label for="merchant_id" class="field-label">
                                            <i class="fas fa-store"></i>
                                            {{ __('merchant.title') }}
                                            <span class="required">*</span>
                                        </label>
                                        <select id="merchant_id" name="merchant_id" class="modern-select" required>
                                            <option value="">{{ __('menus.select') }} {{ __('merchant.title') }}</option>
                                        </select>
                                        <input type="hidden" id="merchanturl" data-url="{{ route('get.merchant.cod') }}" />
                                        <input type="hidden" id="inside_city" value="0" />
                                        <input type="hidden" id="sub_city" value="0" />
                                        <input type="hidden" id="outside_city" value="0" />
                                        @error('merchant_id')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-field">
                                        <label for="shopID" class="field-label">
                                            <i class="fas fa-building"></i>
                                            {{ __('parcel.shop') }}
                                        </label>
                                        <select id="shopID" class="modern-select" name="shop_id" data-url="{{ route('parcel.merchant.shops') }}">
                                            <option value="">{{ __('menus.select') }} {{ __('parcel.shop') }}</option>
                                        </select>
                                        @error('shop_id')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="pickup_phone">{{ __('parcel.pickup_phone') }}</label>
                                    <input id="pickup_phone" type="text" name="pickup_phone"
                                        data-parsley-trigger="change"
                                        placeholder="{{ __('levels.pickup') }} {{ __('levels.phone') }}"
                                        autocomplete="off" class="form-control" value="{{ old('pickup_phone') }}"
                                        required="">
                                    @error('pickup_phone')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="pickup_address">{{ __('parcel.pickup_address') }}</label>
                                    <input id="pickup_address" type="text" name="pickup_address"
                                        data-parsley-trigger="change"
                                        placeholder="{{ __('levels.pickup') }} {{ __('levels.address') }}"
                                        autocomplete="off" class="form-control" value="{{ old('pickup_address') }}"
                                        required="">

                                    <input type="hidden" id="pickup_lat" name="pickup_lat" value="">
                                    <input type="hidden" id="pickup_long" name="pickup_long" value="">

                                    @error('pickup_address')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="cash_collection">{{ __('parcel.cash_collection') }} </label> <span
                                        class="text-danger">*</span>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control cash-collection" id="cash_collection"
                                            value="{{ old('cash_collection') }}" name="cash_collection"
                                            placeholder="{{ __('parcel.Cash_amount_including_delivery_charge') }}"
                                            required="">
                                        @error('cash_collection')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="selling_price">{{ __('parcel.selling_price') }} </label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control cash-collection" id="selling_price"
                                            value="{{ old('selling_price') }}" name="selling_price"
                                            placeholder="{{ __('parcel.Selling_price_of_parcel') }}">
                                        @error('selling_price')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label for="opening_balance">{{ __('parcel.invoice') }}</label>
                                    <input id="invoice_no" type="text" name="invoice_no"
                                        data-parsley-trigger="change"
                                        placeholder="{{ __('parcel.enter_invoice_number') }}" autocomplete="off"
                                        class="form-control" value="{{ old('invoice_no') }}">
                                    @error('invoice_no')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label for="merchant">{{ __('parcel.category') }}</label> <span
                                        class="text-danger">*</span>
                                    <select style="width: 100%" id="category_id" class="form-control select2"
                                        name="category_id" class="form-control @error('category_id') is-invalid @enderror"
                                        data-url="{{ route('parcel.deliveryCategory.deliveryWeight') }}">
                                        <option value=""> {{ __('menus.select') }} {{ __('levels.category') }}
                                        </option>
                                        @foreach ($deliveryCharges as $deliverycharge)
                                            <option value="{{ $deliveryCategories[$deliverycharge]->id }}"
                                                {{ old('category_id') == $deliveryCategories[$deliverycharge]->id ? 'selected' : '' }}>
                                                {{ $deliveryCategories[$deliverycharge]->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group col-12 col-md-6" id="categoryWeight">
                                    <label for="weightID">{{ __('parcel.weight') }}</label> <span
                                        class="text-danger">*</span>
                                    <select style="width: 100%" id="weightID" class="form-control select2"
                                        name="weight">
                                        <option value=""> {{ __('Select Weight') }}</option>

                                    </select>
                                    @error('weight')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>


                                <div class="form-group col-12 col-md-6">
                                    <label for="delivery_type_id">{{ __('parcel.delivery_type') }}</label> <span
                                        class="text-danger">*</span>
                                    <select style="width: 100%" class="form-control select2" id="delivery_type_id"
                                        name="delivery_type_id" required="">
                                        <option value=""> {{ __('menus.select') }} {{ __('menus.delivery_type') }}
                                        </option>
                                        @foreach ($deliveryTypes as $key => $status)
                                            <option
                                                @if ($status->key == 'same_day') value="1" @elseif($status->key == 'next_day') value="2" @elseif($status->key == 'sub_city') value="3" @elseif($status->key == 'outside_City') value="4" @endif>
                                                {{ __('deliveryType.' . $status->key) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('delivery_type_id')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Pricing Method Selection -->
                                <div class="form-group col-12 col-md-6">
                                    <label for="pricing_method">{{ __('Pricing Method') }}</label> <span
                                        class="text-danger">*</span>
                                    <select style="width: 100%" class="form-control select2" id="pricing_method"
                                        name="pricing_method" required="">
                                        <option value="fixed" selected>{{ __('Fixed Pricing') }}</option>
                                        <option value="distance">{{ __('Distance-Based Pricing') }}</option>
                                    </select>
                                    <small class="text-muted">
                                        <i class="fa fa-info-circle"></i> 
                                        {{ __('Fixed: Based on category and weight | Distance: Based on actual delivery distance') }}
                                    </small>
                                    @error('pricing_method')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>


                                <div class="form-group col-12 col-md-6">
                                    <label for="customer_name">{{ __('parcel.customer_name') }}</label> <span
                                        class="text-danger">*</span>
                                    <input id="customer_name" type="text" name="customer_name"
                                        data-parsley-trigger="change" placeholder="{{ __('levels.customer_name') }}"
                                        autocomplete="off" class="form-control" value="{{ old('customer_name') }}"
                                        required="">
                                    @error('customer_name')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="phone">{{ __('parcel.customer_phone') }}</label> <span
                                        class="text-danger">*</span>
                                    <input id="phone" type="text" name="customer_phone"
                                        data-parsley-trigger="change" placeholder="{{ __('levels.customer_phone') }}"
                                        autocomplete="off" class="form-control" value="{{ old('customer_phone') }}"
                                        required="">
                                    @error('customer_phone')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12 col-md-6">
                                    <label for="customer_address">{{ __('parcel.customer_address') }}</label> <span
                                        class="text-danger">*</span>
                                    <input type="hidden" id="lat" name="lat" required="" value="">
                                    <input type="hidden" id="long" name="long" required="" value="">
                                    <div class="main-search-input-item location location-search">
                                        <div id="autocomplete-container" class="form-group random-search">
                                            <input id="autocomplete-input" type="text" name="customer_address"
                                                class="recipe-search2 form-control" placeholder="Location Here!"
                                                required="">
                                            <a href="javascript:void(0)" class="submit-btn btn current-location"
                                                id="locationIcon" onclick="getLocation()">
                                                <i class="fa fa-crosshairs"></i>
                                            </a>
                                            @error('customer_address')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="">
                                        <div id="googleMap" class="custom-map"></div>
                                    </div>
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="note">{{ __('parcel.note') }}</label>
                                    <textarea id="note" name="note" class="form-control" rows="15">{{ old('note') }}</textarea>
                                    @error('note')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Distance-Based Pricing Fields -->
                            <div class="row" id="distance-pricing-fields" style="display: none;">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h5><i class="fa fa-map-marker-alt"></i> Distance-Based Pricing</h5>
                                        <p class="mb-0">The system will calculate delivery charges based on the actual distance between pickup and delivery locations.</p>
                                    </div>
                                </div>
                                
                                <div class="form-group col-12 col-md-6">
                                    <label for="hub_id">{{ __('Origin Hub') }}</label>
                                    <select style="width: 100%" class="form-control select2" id="hub_id" name="hub_id">
                                        <option value="">{{ __('Select Origin Hub') }}</option>
                                        @foreach(\App\Models\Backend\Hub::all() as $hub)
                                            <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">{{ __('Hub where the parcel will be picked up from') }}</small>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label for="transfer_hub_id">{{ __('Transfer Hub') }} <small class="text-muted">(Optional)</small></label>
                                    <select style="width: 100%" class="form-control select2" id="transfer_hub_id" name="transfer_hub_id">
                                        <option value="">{{ __('Select Transfer Hub') }}</option>
                                        @foreach(\App\Models\Backend\Hub::all() as $hub)
                                            <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">{{ __('Hub to transfer the parcel to (adds transfer charges)') }}</small>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label for="weight_kg">{{ __('Weight (kg)') }} <small class="text-muted">(Optional)</small></label>
                                    <input type="number" step="0.1" min="0" class="form-control" id="weight_kg" name="weight_kg" 
                                           placeholder="{{ __('Enter weight in kg') }}" value="{{ old('weight_kg') }}">
                                    <small class="text-muted">{{ __('Weight affects the final pricing calculation') }}</small>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6><i class="fa fa-calculator"></i> Distance Calculator</h6>
                                            <div id="distance-info">
                                                <p class="mb-1"><strong>Distance:</strong> <span id="calculated-distance">0.00 km</span></p>
                                                <p class="mb-1"><strong>Estimated Charge:</strong> <span id="estimated-charge">0.00 LYD</span></p>
                                                <p class="mb-0"><strong>Transfer Charge:</strong> <span id="transfer-charge">0.00 LYD</span></p>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="calculate-distance-btn">
                                                <i class="fa fa-sync"></i> Calculate Distance
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @if (SettingHelper('fragile_liquid_status') == \App\Enums\Status::ACTIVE)
                                    <div class="col-md-6 mt-2">
                                        <label class="form-label"
                                            for="fv-full-name">{{ __('parcel.liquid_check_label') }}</label>
                                        <div class="row pt-1">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <div class="preview-block">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="fragileLiquid"
                                                                data-amount="{{ SettingHelper('fragile_liquid_charge') }}"
                                                                name="fragileLiquid" onclick="processCheck(this);">
                                                            <label class="custom-control-label"
                                                                for="fragileLiquid">{{ __('parcel.liquid_fragile') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group col-md-6" id="PackagingID">
                                    <label for="packaging_id">{{ __('parcel.packaging') }}</label>
                                    <select id="packaging_id" class="form-control" name="packaging_id">
                                        <option value=""> {{ __('menus.select') }} {{ __('menus.packaging') }}
                                        </option>
                                        @foreach ($packagings as $packaging)
                                            <option data-packagingamount="{{ $packaging->price }}"
                                                value="{{ $packaging->id }}"
                                                {{ old('packaging_id') == $packaging->id ? 'selected' : '' }}>
                                                {{ $packaging->name }} ( {{ number_format($packaging->price, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('packaging_id')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6" id="priority">
                                    <label for="priority_id">{{ __('parcel.priority') }}</label>
                                    <select id="priority_id" class="form-control" name="priority_id">
                                        <option value="2" {{ old('priority_id') == 2 ? 'selected' : '' }}>
                                            {{ __('parcel.normal') }} </option>
                                        <option value="1" {{ old('priority_id') == 1 ? 'selected' : '' }}>
                                            {{ __('parcel.high') }} </option>
                                    </select>
                                    @error('priority_id')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <input type="hidden" id="merchantVat" name="vat_tex" value="0" />
                                <input type="hidden" id="merchantCodCharge" name="cod_charge" value="0" />
                                <input type="hidden" id="chargeDetails" name="chargeDetails" value="" />
                            </div>


                            <h3 class="mt-2">Payment Method</h3>
                            @error('parcel_payment_method')
                                <p class="text-danger mt-2">{{ $message }}</p>
                            @enderror
                            <div class="row">
                                <div class="col-md-2 col-6">
                                    <input class="methodInput" type="radio" name="parcel_payment_method" id="cod"  value="{{ App\Enums\ParcelPaymentMethod::COD }}" checked />
                                    <label class="payment-method-box text-center d-block" for="cod">
                                        <i class="fa fa-hand-holding-dollar" style="font-size:50px"></i>
                                        <div class="mt-2">{{ __('ParcelPaymentMethod.'.App\Enums\ParcelPaymentMethod::COD) }}</div>
                                    </label>
                                </div> 
                            </div>

                            <div class="row mt-2">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12  d-flex justify-content-end">
                                    <a href="{{ route('parcel.index') }}"
                                        class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
                                    <button type="submit"
                                        class="btn btn-space btn-primary">{{ __('levels.save') }}</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-lg-12 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="pageheader-title">{{ __('parcel.charge_details') }}</h2>

                        <ul class="list-group">
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('levels.title') }}</span>
                                <span class="float-right">{{ __('levels.amount') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.Cash_Collection') }}</span>
                                <span class="float-right" id="totalCashCollection">{{ __('0.00') }}</span>
                            </li>
                            
                            <!-- Distance-based pricing breakdown -->
                            <div id="distance-pricing-breakdown" style="display: none;">
                                <li class="list-group-item profile-list-group-item">
                                    <span class="float-left font-weight-bold">{{ __('Distance (km)') }}</span>
                                    <span class="float-right" id="distance-km">{{ __('0.00') }}</span>
                                </li>
                                <li class="list-group-item profile-list-group-item">
                                    <span class="float-left font-weight-bold">{{ __('Base Rate') }}</span>
                                    <span class="float-right" id="base-rate">{{ __('0.50') }} LYD/km</span>
                                </li>
                                <li class="list-group-item profile-list-group-item">
                                    <span class="float-left font-weight-bold">{{ __('Weight Multiplier') }}</span>
                                    <span class="float-right" id="weight-multiplier">{{ __('1.0x') }}</span>
                                </li>
                                <li class="list-group-item profile-list-group-item">
                                    <span class="float-left font-weight-bold">{{ __('Hub Transfer Charge') }}</span>
                                    <span class="float-right" id="hub-transfer-charge">{{ __('0.00') }}</span>
                                </li>
                            </div>
                            
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.Delivery_Charge') }}</span>
                                <span class="float-right" id="deliveryChargeAmount">{{ __('0.00') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('reports.COD_Charge') }}</span>
                                <span class="float-right" id="codChargeAmount">{{ __('0.00') }}</span>
                            </li>


                            <li class="list-group-item profile-list-group-item hideShowLiquidFragile">
                                <span class="float-left font-weight-bold">{{ __('parcel.Liquid/Fragile_Charge') }}</span>
                                <span class="float-right" id="liquidFragileAmount">{{ __('0.00') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item" id="packagingShow">
                                <span class="float-left font-weight-bold">{{ __('reports.P.Charge') }}</span>
                                <span class="float-right" id="packagingAmount">{{ __('0.00') }}</span>
                            </li>

                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.Total_Charge') }}</span>
                                <span class="float-right" id="totalDeliveryChargeAmount">{{ __('0.00') }}</span>
                            </li>

                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.Vat') }}</span>
                                <span class="float-right" id="VatAmount">{{ __('0.00') }}</span>
                            </li>

                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.Net_Payable') }}</span>
                                <span class="float-right" id="netPayable">{{ __('0.00') }}</span>
                            </li>

                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.Current_payable') }}</span>
                                <span class="float-right" id="currentPayable">{{ __('0.00') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection()
@push('styles')
    <style>
        .main-search-input-item {
            flex: 1;
            margin-top: 3px;
            position: relative;
        }

        #autocomplete-container,
        #autocomplete-input {
            position: relative;
            z-index: 101;
        }

        .main-search-input input,
        .main-search-input input:focus {
            font-size: 16px;
            border: none;
            background: #fff;
            margin: 0;
            padding: 0;
            height: 44px;
            line-height: 44px;
            box-shadow: none;
        }

        .input-with-icon i,
        .main-search-input-item.location a {
            padding: 5px 10px;
            z-index: 101;
        }

        .main-search-input-item.location a {
            position: absolute;
            right: -50px;
            top: 40%;
            transform: translateY(-50%);
            color: #999;
            padding: 10px;
        }

        .current-location {
            margin-right: 50px;
            margin-top: 5px;
            color: #FFCC00 !important;
        }

        .custom-map {
            width: 100%;
            height: 17rem;
        }

        .pac-container {
            width: 295px;
            position: absolute;
            left: 0px !important;
            top: 28px !important;
        }

        /* Distance Pricing Styles */
        .distance-pricing-active {
            border-left: 4px solid #007bff;
        }

        #distance-pricing-fields {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 15px;
        }

        #distance-pricing-fields .alert {
            border-radius: 6px;
        }

        #distance-info {
            background-color: #fff;
            border-radius: 6px;
            padding: 15px;
            border: 1px solid #dee2e6;
        }

        #distance-info p {
            margin-bottom: 8px;
            font-size: 14px;
        }

        #distance-info p:last-child {
            margin-bottom: 0;
        }

        #calculate-distance-btn {
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 13px;
        }

        #distance-pricing-breakdown {
            background-color: #e3f2fd;
            border-radius: 6px;
            margin: 10px 0;
        }

        #distance-pricing-breakdown .list-group-item {
            background-color: transparent;
            border: none;
            padding: 8px 15px;
            font-size: 14px;
        }

        .pricing-method-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 10px;
            margin-top: 10px;
        }

        .pricing-method-info i {
            color: #856404;
        }

        .pricing-method-info small {
            color: #856404;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
    <script>
        var mapLat = '';
        var mapLong = '';
    </script>
    <script type="text/javascript" src="{{ static_asset('backend/js/parcel/map-current.js') }}"></script>
    <script async
        src="https://maps.googleapis.com/maps/api/js?key={{ googleMapSettingKey() }}&libraries=places&callback=initMap">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        var deliverChargeUrl = '{{ route('parcel.deliveryCharge.get') }}';
        var merchantUrl = '{{ route('parcel.merchant.get') }}';
    </script>
    <script src="{{ static_asset('backend/js/parcel/create.js') }}"></script>
    <script src="{{ static_asset('backend/js/parcel/distance-pricing.js') }}"></script>
@endpush
