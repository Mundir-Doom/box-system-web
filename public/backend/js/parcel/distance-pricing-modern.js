/**
 * Modern Distance-Based Pricing JavaScript
 * Clean, minimal, and user-friendly interface
 */

$(document).ready(function() {
    initializeModernPricing();
});

function initializeModernPricing() {
    // Initialize Select2 for modern selects
    $('.modern-select').select2({
        theme: 'default',
        width: '100%',
        placeholder: function() {
            return $(this).data('placeholder') || 'Select an option';
        }
    });

    // Handle pricing method toggle
    $('#pricing-method-toggle').on('change', function() {
        var isDistance = $(this).is(':checked');
        togglePricingMethod(isDistance);
    });

    // Handle distance field changes
    $(document).on('change', '#pickup_lat, #pickup_long, #lat, #long, #hub_id, #transfer_hub_id, #weight_kg', function() {
        if ($('#pricing_method').val() === 'distance') {
            calculateDistancePricing();
        }
    });

    // Auto-calculate when coordinates are available
    $(document).on('change', '#pickup_lat, #pickup_long, #lat, #long', function() {
        if ($('#pricing_method').val() === 'distance') {
            setTimeout(calculateDistancePricing, 500);
        }
    });
}

function togglePricingMethod(isDistance) {
    var pricingMethod = isDistance ? 'distance' : 'fixed';
    $('#pricing_method').val(pricingMethod);
    
    // Toggle distance fields
    $('.distance-field').each(function() {
        if (isDistance) {
            $(this).show().addClass('show');
        } else {
            $(this).removeClass('show');
            setTimeout(() => {
                if (!$(this).hasClass('show')) {
                    $(this).hide();
                }
            }, 300);
        }
    });

    // Toggle pricing breakdown
    if (isDistance) {
        $('#distance-pricing-breakdown').slideDown();
    } else {
        $('#distance-pricing-breakdown').slideUp();
    }

    // Recalculate pricing
    deliveryCharge();
}

function calculateDistancePricing() {
    var pickupLat = parseFloat($('#pickup_lat').val());
    var pickupLong = parseFloat($('#pickup_long').val());
    var customerLat = parseFloat($('#lat').val());
    var customerLong = parseFloat($('#long').val());
    var hubId = $('#hub_id').val();
    var transferHubId = $('#transfer_hub_id').val();
    var weight = parseFloat($('#weight_kg').val()) || 0;

    // Validate coordinates
    if (!pickupLat || !pickupLong || !customerLat || !customerLong) {
        return;
    }

    // Calculate distance
    var distance = calculateHaversineDistance(pickupLat, pickupLong, customerLat, customerLong);
    
    // Update distance display
    $('#distance-km').text(distance.toFixed(2) + ' km');

    // Make AJAX call for pricing
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
                updateModernPricingDisplay(response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Pricing calculation error:', error);
        }
    });
}

function updateModernPricingDisplay(response) {
    // Update main pricing display
    $('#deliveryChargeAmount').text(response.delivery_charge.toFixed(2));
    $('#hub-transfer-charge').text(response.transfer_charge.toFixed(2));
    
    // Update breakdown if available
    if (response.breakdown) {
        updateModernPricingBreakdown(response.breakdown);
    }
    
    // Recalculate total
    totalSum();
}

function updateModernPricingBreakdown(breakdown) {
    if (breakdown.base_rate_per_km) {
        $('#base-rate').text(breakdown.base_rate_per_km.toFixed(2) + ' LYD/km');
    }
    
    if (breakdown.weight_multiplier) {
        $('#weight-multiplier').text(breakdown.weight_multiplier.toFixed(1) + 'x');
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

// Override the original deliveryCharge function
var originalDeliveryCharge = window.deliveryCharge;
window.deliveryCharge = function() {
    var pricingMethod = $('#pricing_method').val();
    
    if (pricingMethod === 'distance') {
        calculateDistancePricing();
    } else {
        if (originalDeliveryCharge) {
            originalDeliveryCharge();
        }
    }
};

// Add smooth animations
$(document).ready(function() {
    // Animate form sections on load
    $('.form-section').each(function(index) {
        $(this).css('opacity', '0').delay(index * 100).animate({
            opacity: 1
        }, 500);
    });

    // Add focus effects to inputs
    $('.modern-input, .modern-select').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        $(this).parent().removeClass('focused');
    });
});

// Add loading states
function showLoading(element) {
    $(element).addClass('loading');
}

function hideLoading(element) {
    $(element).removeClass('loading');
}

// Add success animations
function showSuccess(element) {
    $(element).addClass('success');
    setTimeout(() => {
        $(element).removeClass('success');
    }, 2000);
}

// Enhanced error handling
function showModernError(message, element) {
    var errorDiv = $('<div class="modern-error">' + message + '</div>');
    $(element).after(errorDiv);
    
    setTimeout(() => {
        errorDiv.fadeOut(() => {
            errorDiv.remove();
        });
    }, 5000);
}

// Add keyboard shortcuts
$(document).on('keydown', function(e) {
    // Ctrl + Enter to submit form
    if (e.ctrlKey && e.keyCode === 13) {
        $('#basicform').submit();
    }
    
    // Escape to clear form
    if (e.keyCode === 27) {
        if (confirm('Are you sure you want to clear the form?')) {
            $('#basicform')[0].reset();
        }
    }
});

// Add form validation feedback
function validateForm() {
    var isValid = true;
    
    $('.modern-input[required], .modern-select[required]').each(function() {
        if (!$(this).val()) {
            $(this).addClass('error');
            isValid = false;
        } else {
            $(this).removeClass('error');
        }
    });
    
    return isValid;
}

// Add real-time validation
$('.modern-input, .modern-select').on('blur', function() {
    if ($(this).attr('required') && !$(this).val()) {
        $(this).addClass('error');
    } else {
        $(this).removeClass('error');
    }
});

// Add form submission handling
$('#basicform').on('submit', function(e) {
    if (!validateForm()) {
        e.preventDefault();
        showModernError('Please fill in all required fields', '.form-actions');
        return false;
    }
    
    // Show loading state
    $('.btn-primary').html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
});

// Add auto-save functionality (optional)
function autoSave() {
    var formData = $('#basicform').serialize();
    // Implement auto-save logic here
}

// Auto-save every 30 seconds
setInterval(autoSave, 30000);
