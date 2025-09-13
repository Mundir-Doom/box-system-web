/**
 * Distance-Based Pricing JavaScript
 * Handles the frontend logic for distance-based pricing system
 */

$(document).ready(function() {
    // Initialize distance pricing functionality
    initializeDistancePricing();
});

function initializeDistancePricing() {
    // Handle pricing method change
    $(document).on('change', '#pricing_method', function() {
        var pricingMethod = $(this).val();
        
        if (pricingMethod === 'distance') {
            showDistancePricingFields();
        } else {
            hideDistancePricingFields();
        }
        
        // Recalculate charges
        deliveryCharge();
    });

    // Handle distance calculation button
    $(document).on('click', '#calculate-distance-btn', function() {
        calculateDistanceAndPricing();
    });

    // Auto-calculate when coordinates change
    $(document).on('change', '#pickup_lat, #pickup_long, #lat, #long, #hub_id, #transfer_hub_id, #weight_kg', function() {
        if ($('#pricing_method').val() === 'distance') {
            calculateDistanceAndPricing();
        }
    });

    // Initialize Select2 for new fields
    $('#hub_id, #transfer_hub_id').select2();
}

function showDistancePricingFields() {
    $('#distance-pricing-fields').slideDown();
    $('#distance-pricing-breakdown').show();
    
    // Show distance info in the calculator
    updateDistanceCalculator();
}

function hideDistancePricingFields() {
    $('#distance-pricing-fields').slideUp();
    $('#distance-pricing-breakdown').hide();
    
    // Reset distance calculator
    $('#calculated-distance').text('0.00 km');
    $('#estimated-charge').text('0.00 LYD');
    $('#transfer-charge').text('0.00 LYD');
}

function calculateDistanceAndPricing() {
    var pickupLat = parseFloat($('#pickup_lat').val());
    var pickupLong = parseFloat($('#pickup_long').val());
    var customerLat = parseFloat($('#lat').val());
    var customerLong = parseFloat($('#long').val());
    var hubId = $('#hub_id').val();
    var transferHubId = $('#transfer_hub_id').val();
    var weight = parseFloat($('#weight_kg').val()) || 0;

    // Validate coordinates
    if (!pickupLat || !pickupLong || !customerLat || !customerLong) {
        showDistanceError('Please ensure both pickup and delivery locations are properly set.');
        return;
    }

    // Show loading state
    $('#calculate-distance-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Calculating...');

    // Calculate distance using Haversine formula
    var distance = calculateHaversineDistance(pickupLat, pickupLong, customerLat, customerLong);
    
    // Update distance display
    $('#calculated-distance').text(distance.toFixed(2) + ' km');
    $('#distance-km').text(distance.toFixed(2) + ' km');

    // Make AJAX call to get pricing
    $.ajax({
        type: 'POST',
        url: deliverChargeUrl,
        data: {
            pricing_method: 'distance',
            pickup_lat: pickupLat,
            pickup_long: pickupLong,
            customer_lat: customerLat,
            customer_long: customerLong,
            weight: weight,
            hub_id: hubId,
            transfer_hub_id: transferHubId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updatePricingDisplay(response);
            } else {
                showDistanceError(response.message || 'Error calculating pricing');
            }
        },
        error: function(xhr, status, error) {
            showDistanceError('Error connecting to server: ' + error);
        },
        complete: function() {
            // Reset button state
            $('#calculate-distance-btn').prop('disabled', false).html('<i class="fa fa-sync"></i> Calculate Distance');
        }
    });
}

function updatePricingDisplay(response) {
    // Update main pricing display
    $('#deliveryChargeAmount').text(response.delivery_charge.toFixed(2));
    $('#hub-transfer-charge').text(response.transfer_charge.toFixed(2));
    
    // Update distance calculator
    $('#estimated-charge').text(response.delivery_charge.toFixed(2) + ' LYD');
    $('#transfer-charge').text(response.transfer_charge.toFixed(2) + ' LYD');
    
    // Update breakdown if available
    if (response.breakdown) {
        updatePricingBreakdown(response.breakdown);
    }
    
    // Recalculate total
    totalSum();
}

function updatePricingBreakdown(breakdown) {
    if (breakdown.distance_km) {
        $('#distance-km').text(breakdown.distance_km.toFixed(2) + ' km');
    }
    
    if (breakdown.base_rate_per_km) {
        $('#base-rate').text(breakdown.base_rate_per_km.toFixed(2) + ' LYD/km');
    }
    
    if (breakdown.weight_multiplier) {
        $('#weight-multiplier').text(breakdown.weight_multiplier.toFixed(1) + 'x');
    }
    
    if (breakdown.hub_transfer_charge_calculation) {
        // Show transfer calculation details
        console.log('Transfer calculation: ' + breakdown.hub_transfer_charge_calculation);
    }
}

function updateDistanceCalculator() {
    var pickupLat = parseFloat($('#pickup_lat').val());
    var pickupLong = parseFloat($('#pickup_long').val());
    var customerLat = parseFloat($('#lat').val());
    var customerLong = parseFloat($('#long').val());

    if (pickupLat && pickupLong && customerLat && customerLong) {
        var distance = calculateHaversineDistance(pickupLat, pickupLong, customerLat, customerLong);
        $('#calculated-distance').text(distance.toFixed(2) + ' km');
    }
}

function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
    var R = 6371; // Earth's radius in kilometers
    var dLat = (lat2 - lat1) * Math.PI / 180;
    var dLon = (lon2 - lon1) * Math.PI / 180;
    var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon/2) * Math.sin(dLon/2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function showDistanceError(message) {
    // Show error message
    if (!$('#distance-error').length) {
        $('#distance-info').after('<div id="distance-error" class="alert alert-danger mt-2"><i class="fa fa-exclamation-triangle"></i> ' + message + '</div>');
    } else {
        $('#distance-error').html('<i class="fa fa-exclamation-triangle"></i> ' + message);
    }
    
    // Hide error after 5 seconds
    setTimeout(function() {
        $('#distance-error').fadeOut();
    }, 5000);
}

// Override the original deliveryCharge function to handle distance pricing
var originalDeliveryCharge = window.deliveryCharge;
window.deliveryCharge = function() {
    var pricingMethod = $('#pricing_method').val();
    
    if (pricingMethod === 'distance') {
        // For distance pricing, we'll calculate it when coordinates are available
        if ($('#pickup_lat').val() && $('#pickup_long').val() && $('#lat').val() && $('#long').val()) {
            calculateDistanceAndPricing();
        }
    } else {
        // Use original fixed pricing logic
        if (originalDeliveryCharge) {
            originalDeliveryCharge();
        }
    }
};

// Add visual feedback for distance pricing
$(document).on('change', '#pricing_method', function() {
    var pricingMethod = $(this).val();
    
    if (pricingMethod === 'distance') {
        // Add visual indicator
        $('.card-body').addClass('distance-pricing-active');
        
        // Show helpful tooltip
        if (!$('#pricing-help').length) {
            $('#pricing_method').after('<div id="pricing-help" class="alert alert-info mt-2"><i class="fa fa-lightbulb"></i> <strong>Tip:</strong> Make sure to set both pickup and delivery locations on the map for accurate distance calculation.</div>');
        }
    } else {
        $('.card-body').removeClass('distance-pricing-active');
        $('#pricing-help').remove();
    }
});