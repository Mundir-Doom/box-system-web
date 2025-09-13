@extends('backend.partials.master')
@section('title')
    {{ __('parcel.title') }} {{ __('levels.add') }}
@endsection
@section('maincontent')
    <div class="container-fluid dashboard-content">
        <!-- pageheader -->
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="page-header">
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="breadcrumb-link">{{ __('parcel.dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('parcel.index') }}" class="breadcrumb-link">{{ __('parcel.title') }}</a></li>
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.create') }}</a></li>
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
                        <div class="pricing-info">
                            <i class="fas fa-info-circle"></i>
                            <span>Simple Hub-to-Hub Pricing</span>
                        </div>
                    </div>
                    <div class="card-body-modern">
                        <form action="{{ route('parcel.store') }}" method="POST" enctype="multipart/form-data" id="basicform">
                            @csrf
                            
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

                                    <div class="form-field">
                                        <label for="pickup_phone" class="field-label">
                                            <i class="fas fa-phone"></i>
                                            {{ __('parcel.pickup_phone') }}
                                        </label>
                                        <input id="pickup_phone" type="text" name="pickup_phone" class="modern-input" 
                                               placeholder="{{ __('levels.pickup') }} {{ __('levels.phone') }}" 
                                               value="{{ old('pickup_phone') }}" required>
                                        @error('pickup_phone')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-field">
                                        <label for="pickup_address" class="field-label">
                                            <i class="fas fa-map-marker-alt"></i>
                                            {{ __('parcel.pickup_address') }}
                                        </label>
                                        <input id="pickup_address" type="text" name="pickup_address" class="modern-input" 
                                               placeholder="{{ __('levels.pickup') }} {{ __('levels.address') }}" 
                                               value="{{ old('pickup_address') }}" required>
                                        <input type="hidden" id="pickup_lat" name="pickup_lat" value="">
                                        <input type="hidden" id="pickup_long" name="pickup_long" value="">
                                        @error('pickup_address')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Simple Pricing Section -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-route"></i>
                                    Hub-to-Hub Pricing
                                </h3>
                                <div class="form-grid">
                                    <div class="form-field">
                                        <label for="from_hub_id" class="field-label">
                                            <i class="fas fa-warehouse"></i>
                                            From Hub
                                            <span class="required">*</span>
                                        </label>
                                        <select id="from_hub_id" name="from_hub_id" class="modern-select" required>
                                            <option value="">{{ __('Select From Hub') }}</option>
                                            @foreach(\App\Models\Backend\Hub::all() as $hub)
                                                <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('from_hub_id')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-field">
                                        <label for="to_hub_id" class="field-label">
                                            <i class="fas fa-warehouse"></i>
                                            To Hub
                                            <span class="required">*</span>
                                        </label>
                                        <select id="to_hub_id" name="to_hub_id" class="modern-select" required>
                                            <option value="">{{ __('Select To Hub') }}</option>
                                            @foreach(\App\Models\Backend\Hub::all() as $hub)
                                                <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('to_hub_id')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-field">
                                        <label for="weight" class="field-label">
                                            <i class="fas fa-weight-hanging"></i>
                                            Weight (kg) <small class="text-muted">(Optional)</small>
                                        </label>
                                        <input type="number" step="0.1" min="0" class="modern-input" id="weight" name="weight" 
                                               placeholder="{{ __('Enter weight in kg') }}" value="{{ old('weight') }}">
                                        <small class="text-muted">Adds extra cost per kg if specified</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Details Section (No Pricing Impact) -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-info"></i>
                                    Delivery Details
                                    <small class="text-muted">(For reference only - no pricing impact)</small>
                                </h3>
                                <div class="form-grid">
                                    <div class="form-field">
                                        <label for="category_id" class="field-label">
                                            <i class="fas fa-tags"></i>
                                            {{ __('parcel.category') }}
                                        </label>
                                        <select id="category_id" class="modern-select" name="category_id">
                                            <option value="">{{ __('menus.select') }} {{ __('levels.category') }}</option>
                                            @foreach ($deliveryCharges as $deliverycharge)
                                                <option value="{{ $deliveryCategories[$deliverycharge]->id }}" 
                                                        {{ old('category_id') == $deliveryCategories[$deliverycharge]->id ? 'selected' : '' }}>
                                                    {{ $deliveryCategories[$deliverycharge]->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-field">
                                        <label for="delivery_type_id" class="field-label">
                                            <i class="fas fa-truck"></i>
                                            {{ __('parcel.delivery_type') }}
                                        </label>
                                        <select id="delivery_type_id" class="modern-select" name="delivery_type_id">
                                            <option value="">{{ __('menus.select') }} {{ __('menus.delivery_type') }}</option>
                                            @foreach ($deliveryTypes as $key => $status)
                                                <option @if ($status->key == 'same_day') value="1" @elseif($status->key == 'next_day') value="2" @elseif($status->key == 'sub_city') value="3" @elseif($status->key == 'outside_City') value="4" @endif>
                                                    {{ __('deliveryType.' . $status->key) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('delivery_type_id')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Information Section -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-user"></i>
                                    Customer Information
                                </h3>
                                <div class="form-grid">
                                    <div class="form-field">
                                        <label for="customer_name" class="field-label">
                                            <i class="fas fa-user"></i>
                                            {{ __('parcel.customer_name') }}
                                            <span class="required">*</span>
                                        </label>
                                        <input id="customer_name" type="text" name="customer_name" class="modern-input" 
                                               placeholder="{{ __('levels.customer_name') }}" 
                                               value="{{ old('customer_name') }}" required>
                                        @error('customer_name')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-field">
                                        <label for="phone" class="field-label">
                                            <i class="fas fa-phone"></i>
                                            {{ __('parcel.customer_phone') }}
                                            <span class="required">*</span>
                                        </label>
                                        <input id="phone" type="text" name="customer_phone" class="modern-input" 
                                               placeholder="{{ __('levels.customer_phone') }}" 
                                               value="{{ old('customer_phone') }}" required>
                                        @error('customer_phone')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-field full-width">
                                        <label for="customer_address" class="field-label">
                                            <i class="fas fa-map-marker-alt"></i>
                                            {{ __('parcel.customer_address') }}
                                            <span class="required">*</span>
                                        </label>
                                        <input type="hidden" id="lat" name="lat" required="" value="">
                                        <input type="hidden" id="long" name="long" required="" value="">
                                        <div class="location-input-container">
                                            <input id="autocomplete-input" type="text" name="customer_address" 
                                                   class="modern-input location-input" placeholder="Enter delivery location..." required>
                                            <button type="button" class="location-btn" id="locationIcon" onclick="getLocation()">
                                                <i class="fa fa-crosshairs"></i>
                                            </button>
                                        </div>
                                        @error('customer_address')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                        <div class="map-container">
                                            <div id="googleMap" class="modern-map"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Special Handling Section -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Special Handling
                                </h3>
                                <div class="form-grid">
                                    @if (SettingHelper('fragile_liquid_status') == \App\Enums\Status::ACTIVE)
                                        <div class="form-field">
                                            <label class="field-label">
                                                <i class="fas fa-check-circle"></i>
                                                {{ __('parcel.liquid_check_label') }}
                                            </label>
                                            <div class="checkbox-container">
                                                <label class="modern-checkbox">
                                                    <input type="checkbox" 
                                                           id="fragileLiquid"
                                                           data-amount="{{ SettingHelper('fragile_liquid_charge') }}"
                                                           name="fragileLiquid" 
                                                           onclick="processCheck(this);"
                                                           value="{{ old('fragileLiquid') }}">
                                                    <span class="checkmark"></span>
                                                    <span class="checkbox-label">{{ __('parcel.liquid_fragile') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="form-field">
                                        <label for="packaging_id" class="field-label">
                                            <i class="fas fa-box"></i>
                                            {{ __('parcel.packaging') }}
                                        </label>
                                        <select id="packaging_id" class="modern-select" name="packaging_id">
                                            <option value="">{{ __('menus.select') }} {{ __('parcel.packaging') }}</option>
                                            @foreach ($packagings as $packaging)
                                                <option data-packagingamount="{{ $packaging->price }}"
                                                        value="{{ $packaging->id }}"
                                                        {{ old('packaging_id') == $packaging->id ? 'selected' : '' }}>
                                                    {{ $packaging->name }} ({{ number_format($packaging->price, 2) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('packaging_id')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Information Section -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-money-bill-wave"></i>
                                    Financial Information
                                </h3>
                                <div class="form-grid">
                                    <div class="form-field">
                                        <label for="cash_collection" class="field-label">
                                            <i class="fas fa-money-bill"></i>
                                            {{ __('parcel.cash_collection') }}
                                            <span class="required">*</span>
                                        </label>
                                        <input type="text" class="modern-input cash-collection" id="cash_collection" 
                                               value="{{ old('cash_collection') }}" name="cash_collection" 
                                               placeholder="{{ __('parcel.Cash_amount_including_delivery_charge') }}" required>
                                        @error('cash_collection')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-field">
                                        <label for="selling_price" class="field-label">
                                            <i class="fas fa-tag"></i>
                                            {{ __('parcel.selling_price') }}
                                        </label>
                                        <input type="text" class="modern-input cash-collection" id="selling_price" 
                                               value="{{ old('selling_price') }}" name="selling_price" 
                                               placeholder="{{ __('parcel.Selling_price_of_parcel') }}">
                                        @error('selling_price')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-field">
                                        <label for="invoice_no" class="field-label">
                                            <i class="fas fa-file-invoice"></i>
                                            {{ __('parcel.invoice') }}
                                        </label>
                                        <input id="invoice_no" type="text" name="invoice_no" class="modern-input" 
                                               placeholder="{{ __('parcel.enter_invoice_number') }}" 
                                               value="{{ old('invoice_no') }}">
                                        @error('invoice_no')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Fields -->
                            <input type="hidden" id="merchantVat" name="vat_tex" value="0" />
                            <input type="hidden" id="merchantCodCharge" name="cod_charge" value="0" />
                            <input type="hidden" id="chargeDetails" name="chargeDetails" value="" />

                            <!-- Form Actions -->
                            <div class="form-actions">
                                <a href="{{ route('parcel.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                    {{ __('levels.cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    {{ __('levels.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Simple Pricing Summary Sidebar -->
            <div class="col-md-12 col-lg-12 col-xl-4">
                <div class="pricing-summary">
                    <div class="summary-header">
                        <h3>
                            <i class="fas fa-calculator"></i>
                            Hub-to-Hub Pricing
                        </h3>
                    </div>
                    <div class="summary-content">
                        <div class="summary-item">
                            <span class="summary-label">{{ __('parcel.Cash_Collection') }}</span>
                            <span class="summary-value" id="totalCashCollection">0.00</span>
                        </div>
                        
                        <!-- Hub Pricing Breakdown -->
                        <div class="hub-pricing-breakdown" id="hub-pricing-breakdown" style="display: none;">
                            <div class="summary-item">
                                <span class="summary-label">From Hub</span>
                                <span class="summary-value" id="from-hub-name">-</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">To Hub</span>
                                <span class="summary-value" id="to-hub-name">-</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Weight</span>
                                <span class="summary-value" id="weight-display">0.0 kg</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Base Price</span>
                                <span class="summary-value" id="base-price">0.00 LYD</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Weight Price</span>
                                <span class="summary-value" id="weight-price">0.00 LYD</span>
                            </div>
                        </div>
                        
                        <div class="summary-item total">
                            <span class="summary-label">{{ __('parcel.Delivery_Charge') }}</span>
                            <span class="summary-value" id="deliveryChargeAmount">0.00</span>
                        </div>
                        <div class="summary-item hideShowLiquidFragile">
                            <span class="summary-label">{{ __('parcel.Liquid/Fragile_Charge') }}</span>
                            <span class="summary-value" id="liquidFragileAmount">0.00</span>
                        </div>
                        <div class="summary-item hideShowPackaging">
                            <span class="summary-label">{{ __('parcel.packaging') }} {{ __('parcel.charge') }}</span>
                            <span class="summary-value" id="packagingAmount">0.00</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">{{ __('reports.COD_Charge') }}</span>
                            <span class="summary-value" id="codChargeAmount">0.00</span>
                        </div>
                        <div class="summary-item final">
                            <span class="summary-label">{{ __('parcel.Net_Payable') }}</span>
                            <span class="summary-value" id="netPayable">0.00</span>
                        </div>
                        <div class="summary-item final">
                            <span class="summary-label">{{ __('parcel.Current_payable') }}</span>
                            <span class="summary-value" id="currentPayable">0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection()

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Modern Card Styles */
        .modern-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: none;
            overflow: hidden;
        }

        .card-header-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 24px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modern-title {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modern-title i {
            font-size: 28px;
        }

        .pricing-info {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
        }

        /* Form Sections */
        .card-body-modern {
            padding: 32px;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e2e8f0;
        }

        .section-title i {
            color: #667eea;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .form-field.full-width {
            grid-column: 1 / -1;
        }

        /* Form Fields */
        .form-field {
            display: flex;
            flex-direction: column;
        }

        .field-label {
            font-size: 14px;
            font-weight: 500;
            color: #4a5568;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .field-label i {
            color: #667eea;
            width: 16px;
        }

        .required {
            color: #e53e3e;
            margin-left: 4px;
        }

        .modern-input, .modern-select {
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #ffffff;
        }

        .modern-input:focus, .modern-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .field-error {
            color: #e53e3e;
            font-size: 12px;
            margin-top: 4px;
        }

        /* Location Input */
        .location-input-container {
            position: relative;
        }

        .location-input {
            padding-right: 50px;
        }

        .location-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .location-btn:hover {
            background: #5a67d8;
        }

        /* Map */
        .map-container {
            margin-top: 16px;
        }

        .modern-map {
            width: 100%;
            height: 200px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 16px;
            justify-content: flex-end;
            padding-top: 32px;
            border-top: 2px solid #e2e8f0;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #667eea;
            border: none;
            color: white;
        }

        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #e2e8f0;
            border: none;
            color: #4a5568;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        /* Pricing Summary */
        .pricing-summary {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            position: sticky;
            top: 20px;
        }

        .summary-header {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            padding: 20px 24px;
        }

        .summary-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .summary-content {
            padding: 24px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-item.total {
            border-top: 2px solid #e2e8f0;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 600;
            color: #2d3748;
        }

        .summary-item.final {
            font-weight: 700;
            font-size: 16px;
            color: #667eea;
        }

        .summary-label {
            font-size: 14px;
            color: #4a5568;
        }

        .summary-value {
            font-size: 14px;
            font-weight: 500;
            color: #2d3748;
        }

        .hub-pricing-breakdown {
            background: #f0f4ff;
            border-radius: 8px;
            padding: 16px;
            margin: 16px 0;
        }

        .hub-pricing-breakdown .summary-item {
            border-bottom: 1px solid #cbd5e0;
            padding: 8px 0;
        }

        .hub-pricing-breakdown .summary-item:last-child {
            border-bottom: none;
        }

        /* Modern Checkbox Styles */
        .checkbox-container {
            margin-top: 8px;
        }

        .modern-checkbox {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 14px;
            color: #4a5568;
            position: relative;
        }

        .modern-checkbox input[type="checkbox"] {
            opacity: 0;
            position: absolute;
            width: 0;
            height: 0;
        }

        .checkmark {
            height: 20px;
            width: 20px;
            background-color: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 4px;
            margin-right: 12px;
            position: relative;
            transition: all 0.3s ease;
        }

        .modern-checkbox:hover .checkmark {
            border-color: #667eea;
        }

        .modern-checkbox input:checked ~ .checkmark {
            background-color: #667eea;
            border-color: #667eea;
        }

        .modern-checkbox input:checked ~ .checkmark:after {
            content: "";
            position: absolute;
            left: 6px;
            top: 2px;
            width: 6px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .checkbox-label {
            font-weight: 500;
        }

        .hideShowLiquidFragile {
            display: none;
            margin-top: 16px;
            padding: 16px;
            background: #f0f4ff;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .hideShowPackaging {
            display: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .card-header-modern {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        var mapLat = '';
        var mapLong = '';
    </script>
    <script type="text/javascript" src="{{ static_asset('backend/js/parcel/map-current.js') }}"></script>
    <script async src="https://maps.googleapis.com/maps/api/js?key={{ googleMapSettingKey() }}&libraries=places&callback=initMap"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        var deliverChargeUrl = '{{ route('parcel.deliveryCharge.get') }}';
        var merchantUrl = '{{ route('parcel.merchant.get') }}';
        
        // Initialize fragile/liquid functionality
        $(document).ready(function() {
            // Hide fragile/liquid charge initially
            $('.hideShowLiquidFragile').hide();
            $('.hideShowPackaging').hide();
            
            // Initialize checkbox state
            if($('#fragileLiquid').is(':checked')) {
                processCheck(document.getElementById('fragileLiquid'));
            }
            
            // Initialize packaging state
            if($('#packaging_id').val()) {
                updatePackagingDisplay();
            }
        });
        
        // Process fragile/liquid checkbox
        function processCheck(event) {
            var liquidFragileAmount = 0;
            if($('#fragileLiquid').is(':checked')) {
                $('.hideShowLiquidFragile').show();
                liquidFragileAmount = parseFloat($('#fragileLiquid').data('amount'));
                $('#liquidFragileAmount').text(liquidFragileAmount.toFixed(2));
            } else {
                $('.hideShowLiquidFragile').hide();
                liquidFragileAmount = 0;
                $('#liquidFragileAmount').text(liquidFragileAmount.toFixed(2));
            }
            
            // Recalculate totals when fragile/liquid changes
            if(typeof totalSum === 'function') {
                totalSum();
            }
        }
        
        // Update packaging display
        function updatePackagingDisplay() {
            var selectedPackaging = $('#packaging_id option:selected');
            if (selectedPackaging.length && selectedPackaging.val()) {
                var packagingAmount = parseFloat(selectedPackaging.data('packagingamount')) || 0;
                $('#packagingAmount').text(packagingAmount.toFixed(2));
                $('.hideShowPackaging').show();
            } else {
                $('#packagingAmount').text('0.00');
                $('.hideShowPackaging').hide();
            }
            
            // Recalculate totals when packaging changes
            if(typeof totalSum === 'function') {
                totalSum();
            }
        }
    </script>
    <script src="{{ static_asset('backend/js/parcel/create.js') }}"></script>
    <script src="{{ static_asset('backend/js/parcel/simple-pricing.js') }}"></script>
@endpush
