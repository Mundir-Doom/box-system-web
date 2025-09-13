@extends('backend.partials.master')
@section('title')
    Hub Pricing Management
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
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">Hub Pricing</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <h2 class="modern-title">
                            <i class="fas fa-route"></i>
                            Hub-to-Hub Pricing Management
                        </h2>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addPricingModal">
                            <i class="fas fa-plus"></i>
                            Add New Pricing
                        </button>
                    </div>
                    <div class="card-body-modern">
                        <div class="pricing-grid">
                            @foreach($hubs as $fromHub)
                                <div class="hub-pricing-section">
                                    <h4 class="hub-title">
                                        <i class="fas fa-warehouse"></i>
                                        {{ $fromHub->name }}
                                    </h4>
                                    <div class="pricing-table">
                                        <div class="table-header">
                                            <div class="col">To Hub</div>
                                            <div class="col">Base Price</div>
                                            <div class="col">Per Kg Price</div>
                                            <div class="col">Min Price</div>
                                            <div class="col">Max Price</div>
                                            <div class="col">Actions</div>
                                        </div>
                                        @foreach($hubs as $toHub)
                                            @if($fromHub->id != $toHub->id)
                                                @php
                                                    $pricing = \App\Models\Backend\HubTransferCharge::where('from_hub_id', $fromHub->id)
                                                        ->where('to_hub_id', $toHub->id)->first();
                                                @endphp
                                                <div class="table-row">
                                                    <div class="col">{{ $toHub->name }}</div>
                                                    <div class="col">
                                                        <span class="price-value">{{ $pricing ? number_format($pricing->base_charge, 2) : '0.00' }} LYD</span>
                                                    </div>
                                                    <div class="col">
                                                        <span class="price-value">{{ $pricing ? number_format($pricing->per_km_rate, 2) : '0.00' }} LYD/kg</span>
                                                    </div>
                                                    <div class="col">
                                                        <span class="price-value">{{ $pricing ? number_format($pricing->min_charge, 2) : '0.00' }} LYD</span>
                                                    </div>
                                                    <div class="col">
                                                        <span class="price-value">{{ $pricing ? number_format($pricing->max_charge, 2) : '0.00' }} LYD</span>
                                                    </div>
                                                    <div class="col">
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                onclick="editPricing({{ $fromHub->id }}, {{ $toHub->id }}, {{ $pricing ? $pricing->toJson() : 'null' }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Pricing Modal -->
    <div class="modal fade" id="addPricingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-route"></i>
                        <span id="modalTitle">Add Hub Pricing</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="pricingForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-field">
                                    <label class="field-label">
                                        <i class="fas fa-warehouse"></i>
                                        From Hub
                                    </label>
                                    <select id="from_hub_id" name="from_hub_id" class="modern-select" required>
                                        <option value="">Select From Hub</option>
                                        @foreach($hubs as $hub)
                                            <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-field">
                                    <label class="field-label">
                                        <i class="fas fa-warehouse"></i>
                                        To Hub
                                    </label>
                                    <select id="to_hub_id" name="to_hub_id" class="modern-select" required>
                                        <option value="">Select To Hub</option>
                                        @foreach($hubs as $hub)
                                            <option value="{{ $hub->id }}">{{ $hub->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-field">
                                    <label class="field-label">
                                        <i class="fas fa-money-bill"></i>
                                        Base Price (LYD)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="modern-input" 
                                           id="base_charge" name="base_charge" placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-field">
                                    <label class="field-label">
                                        <i class="fas fa-weight-hanging"></i>
                                        Per Kg Price (LYD)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="modern-input" 
                                           id="per_kg_rate" name="per_kg_rate" placeholder="0.00">
                                    <small class="text-muted">Optional: Additional cost per kg</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-field">
                                    <label class="field-label">
                                        <i class="fas fa-arrow-down"></i>
                                        Minimum Price (LYD)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="modern-input" 
                                           id="min_charge" name="min_charge" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-field">
                                    <label class="field-label">
                                        <i class="fas fa-arrow-up"></i>
                                        Maximum Price (LYD)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="modern-input" 
                                           id="max_charge" name="max_charge" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Save Pricing
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
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

        .card-body-modern {
            padding: 32px;
        }

        .pricing-grid {
            display: grid;
            gap: 2rem;
        }

        .hub-pricing-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid #e2e8f0;
        }

        .hub-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .hub-title i {
            color: #667eea;
        }

        .pricing-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .table-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr;
            background: #667eea;
            color: white;
            font-weight: 600;
            padding: 16px;
        }

        .table-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr;
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
            align-items: center;
        }

        .table-row:last-child {
            border-bottom: none;
        }

        .table-row:hover {
            background: #f8f9fa;
        }

        .col {
            padding: 0 8px;
        }

        .price-value {
            font-weight: 500;
            color: #2d3748;
        }

        .form-field {
            margin-bottom: 20px;
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

        .modern-input, .modern-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .modern-input:focus, .modern-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5a67d8;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .btn-outline-primary {
            background: transparent;
            color: #667eea;
            border: 1px solid #667eea;
        }

        .btn-outline-primary:hover {
            background: #667eea;
            color: white;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .table-header, .table-row {
                grid-template-columns: 1fr;
                gap: 8px;
            }
            
            .col {
                display: flex;
                justify-content: space-between;
                padding: 8px 0;
            }
            
            .col:before {
                content: attr(data-label);
                font-weight: 600;
                color: #4a5568;
            }
        }

        /* Modal styles for vanilla JavaScript */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal.show {
            display: block;
        }

        .modal-dialog {
            position: relative;
            width: auto;
            margin: 1.75rem auto;
            max-width: 500px;
        }

        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 0.3rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.5);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 1rem;
        }

        .modal-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 1rem;
            border-top: 1px solid #dee2e6;
        }

        .close {
            float: right;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: 0.5;
            background: transparent;
            border: 0;
            cursor: pointer;
        }

        .close:hover {
            opacity: 0.75;
        }

        body.modal-open {
            overflow: hidden;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function editPricing(fromHubId, toHubId, pricing) {
            document.getElementById('modalTitle').textContent = 'Edit Hub Pricing';
            document.getElementById('from_hub_id').value = fromHubId;
            document.getElementById('to_hub_id').value = toHubId;
            
            if (pricing) {
                document.getElementById('base_charge').value = pricing.base_charge;
                document.getElementById('per_kg_rate').value = pricing.per_km_rate;
                document.getElementById('min_charge').value = pricing.min_charge;
                document.getElementById('max_charge').value = pricing.max_charge;
            } else {
                document.getElementById('base_charge').value = '';
                document.getElementById('per_kg_rate').value = '';
                document.getElementById('min_charge').value = '';
                document.getElementById('max_charge').value = '';
            }
            
            // Show modal using vanilla JavaScript
            document.getElementById('addPricingModal').style.display = 'block';
            document.getElementById('addPricingModal').classList.add('show');
            document.body.classList.add('modal-open');
        }

        function closeModal() {
            document.getElementById('addPricingModal').style.display = 'none';
            document.getElementById('addPricingModal').classList.remove('show');
            document.body.classList.remove('modal-open');
            document.getElementById('modalTitle').textContent = 'Add Hub Pricing';
            document.getElementById('pricingForm').reset();
        }

        // Close modal when clicking outside
        document.getElementById('addPricingModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with X button
        document.querySelector('#addPricingModal .close').addEventListener('click', closeModal);

        // Close modal with Cancel button
        document.querySelector('#addPricingModal .btn-secondary').addEventListener('click', closeModal);

        document.getElementById('pricingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const fromHubId = formData.get('from_hub_id');
            const toHubId = formData.get('to_hub_id');
            
            if (fromHubId === toHubId) {
                alert('From Hub and To Hub cannot be the same!');
                return;
            }
            
            // Submit form via AJAX
            fetch('{{ route("hub-pricing.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving pricing.');
            });
        });
    </script>
@endpush
