/**
 * Simple Hub-to-Hub Pricing JavaScript
 * Clean and straightforward pricing system
 */

$(document).ready(function() {
    initializeSimplePricing();
});

function initializeSimplePricing() {
    // Initialize Select2 for modern selects
    $('.modern-select').select2({
        theme: 'default',
        width: '100%',
        placeholder: function() {
            return $(this).data('placeholder') || 'Select an option';
        }
    });

    // Handle hub selection changes
    $(document).on('change', '#from_hub_id, #to_hub_id, #weight', function() {
        calculateSimplePricing();
    });

    // Handle cash collection changes
    $(document).on('input', '#cash_collection, #selling_price', function() {
        totalSum();
    });

    // Handle packaging selection changes
    $(document).on('change', '#packaging_id', function() {
        if (typeof updatePackagingDisplay === 'function') {
            updatePackagingDisplay();
        }
        totalSum();
    });

    // Handle fragile/liquid checkbox changes
    $(document).on('change', '#fragileLiquid', function() {
        totalSum();
    });

    // Generate initial invoice number on page load
    generateInvoiceNumber();

    // Handle invoice number generation button click
    $(document).on('click', '#generateInvoiceBtn', function() {
        generateInvoiceNumber();
    });
}

function calculateSimplePricing() {
    var fromHubId = $('#from_hub_id').val();
    var toHubId = $('#to_hub_id').val();
    var weight = parseFloat($('#weight').val()) || 0;

    console.log('Calculating simple pricing:', { fromHubId, toHubId, weight });

    // Validate hub selection
    if (!fromHubId || !toHubId) {
        console.log('Missing hub selection');
        hidePricingBreakdown();
        return;
    }

    if (fromHubId === toHubId) {
        alert('From Hub and To Hub cannot be the same!');
        $('#to_hub_id').val('');
        hidePricingBreakdown();
        return;
    }

    // Show loading state
    $('#deliveryChargeAmount').text('Calculating...');

    // Make AJAX call for pricing
    $.ajax({
        type: 'POST',
        url: deliverChargeUrl,
        data: {
            from_hub_id: fromHubId,
            to_hub_id: toHubId,
            weight: weight
        },
        dataType: 'json',
        success: function(response) {
            console.log('Pricing response:', response);
            if (response.success) {
                updateSimplePricingDisplay(response);
            } else {
                console.error('Pricing calculation error:', response.message);
                $('#deliveryChargeAmount').text('0.00');
                hidePricingBreakdown();
            }
        },
        error: function(xhr, status, error) {
            console.error('Pricing calculation error:', error);
            console.error('Response:', xhr.responseText);
            $('#deliveryChargeAmount').text('0.00');
            hidePricingBreakdown();
        }
    });
}

function updateSimplePricingDisplay(response) {
    // Update main pricing display
    $('#deliveryChargeAmount').text(response.total_charge.toFixed(2));
    
    // Update breakdown
    $('#from-hub-name').text(response.from_hub);
    $('#to-hub-name').text(response.to_hub);
    $('#weight-display').text(response.weight > 0 ? response.weight.toFixed(1) + ' kg' : 'No weight');
    $('#base-price').text(response.delivery_charge.toFixed(2) + ' LYD');
    $('#weight-price').text(response.weight_charge.toFixed(2) + ' LYD');
    
    // Show breakdown
    $('#hub-pricing-breakdown').slideDown();
    
    // Recalculate total
    totalSum();
}

function hidePricingBreakdown() {
    $('#hub-pricing-breakdown').slideUp();
    $('#deliveryChargeAmount').text('0.00');
    totalSum();
}

// Override the original deliveryCharge function for simple pricing
var originalDeliveryCharge = window.deliveryCharge;
window.deliveryCharge = function() {
    // Use simple hub-to-hub pricing
    calculateSimplePricing();
};

// Enhanced totalSum function for simple pricing
var originalTotalSum = window.totalSum;
window.totalSum = function() {
    if (originalTotalSum) {
        originalTotalSum();
    }
    
    // Additional simple pricing calculations if needed
    var cashCollection = parseFloat($('#cash_collection').val()) || 0;
    var sellingPrice = parseFloat($('#selling_price').val()) || 0;
    var deliveryCharge = parseFloat($('#deliveryChargeAmount').text()) || 0;
    var codCharge = parseFloat($('#codChargeAmount').text()) || 0;
    var liquidFragileCharge = parseFloat($('#liquidFragileAmount').text()) || 0;
    var packagingCharge = 0;
    
    // Calculate packaging charge
    var selectedPackaging = $('#packaging_id option:selected');
    if (selectedPackaging.length && selectedPackaging.val()) {
        packagingCharge = parseFloat(selectedPackaging.data('packagingamount')) || 0;
    }
    
    // Update cash collection display
    $('#totalCashCollection').text(cashCollection.toFixed(2));
    
    // Calculate net payable including all charges
    var netPayable = deliveryCharge + codCharge + liquidFragileCharge + packagingCharge;
    $('#netPayable').text(netPayable.toFixed(2));
    $('#currentPayable').text(netPayable.toFixed(2));
};

// Add form validation
function validateSimpleForm() {
    var isValid = true;
    var errors = [];
    
    // Check required fields
    if (!$('#from_hub_id').val()) {
        errors.push('Please select a From Hub');
        isValid = false;
    }
    
    if (!$('#to_hub_id').val()) {
        errors.push('Please select a To Hub');
        isValid = false;
    }
    
    if ($('#from_hub_id').val() === $('#to_hub_id').val()) {
        errors.push('From Hub and To Hub cannot be the same');
        isValid = false;
    }
    
    if (!isValid) {
        alert('Please fix the following errors:\n' + errors.join('\n'));
    }
    
    return isValid;
}

// Add form submission handling
$('#basicform').on('submit', function(e) {
    if (!validateSimpleForm()) {
        e.preventDefault();
        return false;
    }
    
    // Show loading state
    $('.btn-primary').html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
});

// Add real-time validation feedback
$('#from_hub_id, #to_hub_id').on('change', function() {
    var fromHub = $('#from_hub_id').val();
    var toHub = $('#to_hub_id').val();
    
    if (fromHub && toHub && fromHub === toHub) {
        $(this).addClass('error');
        showSimpleError('From Hub and To Hub cannot be the same', $(this));
    } else {
        $(this).removeClass('error');
        hideSimpleError($(this));
    }
});

// Error handling functions
function showSimpleError(message, element) {
    var errorDiv = $('<div class="simple-error">' + message + '</div>');
    element.after(errorDiv);
    
    setTimeout(() => {
        errorDiv.fadeOut(() => {
            errorDiv.remove();
        });
    }, 5000);
}

function hideSimpleError(element) {
    element.siblings('.simple-error').remove();
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
            $('.modern-select').trigger('change');
        }
    }
});

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

// Add success animations
function showSuccess(element) {
    $(element).addClass('success');
    setTimeout(() => {
        $(element).removeClass('success');
    }, 2000);
}

// Add loading states
function showLoading(element) {
    $(element).addClass('loading');
}

function hideLoading(element) {
    $(element).removeClass('loading');
}

// Invoice Number Generation
function generateInvoiceNumber() {
    var $btn = $('#generateInvoiceBtn');
    var $input = $('#invoice_no');
    
    // Show loading state
    $btn.addClass('loading').prop('disabled', true);
    $input.val('Generating...');
    
    // Make AJAX request to generate invoice number
    $.ajax({
        type: 'GET',
        url: generateInvoiceUrl,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $input.val(response.invoice_number);
                showSuccess($input);
                console.log('Generated invoice number:', response.invoice_number);
            } else {
                console.error('Error generating invoice number:', response.message);
                $input.val('');
                alert('Error generating invoice number: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Invoice generation error:', error);
            $input.val('');
            alert('Error generating invoice number. Please try again.');
        },
        complete: function() {
            // Hide loading state
            $btn.removeClass('loading').prop('disabled', false);
        }
    });
}
