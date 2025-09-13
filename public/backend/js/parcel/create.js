"use strict";
$(document).ready(function(){

    $( "#shopID" ).select2();
    $( "#category_id" ).select2();
    $( "#weightID" ).select2();
    $( "#delivery_type_id" ).select2();

        $( "#merchant_id" ).select2({
      
        ajax: {
            url: merchantUrl,
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    searchQuery: true
                };
            },
            processResults: function (response) {

                return {

                    results: response
                };
            },
            cache: true
        }

    });

});

$(document).on('change', '#merchant_id', function () {
    var merchantId = $(this).val();
    var $shopSelect = $('#shopID');
    
    if (merchantId && merchantId !== '') {
        // Enable shop dropdown
        $shopSelect.prop('disabled', false);
        $('#shop-help-text').hide();
        
        var url = $shopSelect.data('url');
        console.log('URL:', url);
        console.log('Merchant ID:', merchantId);
        
        $.ajax({
            type : 'POST',
            url : url,
            data : {
                'id': merchantId, 
                'shop': true
            },
            dataType : "html",
            success : function (data) {
                console.log('Shops loaded successfully:', data);
                $shopSelect.html(data);
                
                // Check if merchant has no shops (only disabled option and create_shop option)
                var hasShops = $shopSelect.find('option:not([disabled]):not([value="create_shop"])').length > 0;
                
                if (!hasShops) {
                    // Show create shop button
                    $('#create-shop-button-container').show();
                } else {
                    // Hide create shop button
                    $('#create-shop-button-container').hide();
                }
                
                shop(url);
                deliveryCharge();
            },
            error : function(xhr, status, error) {
                console.log('AJAX Error:', status, error);
                console.log('Response:', xhr.responseText);
                // If AJAX fails, show error message
                $shopSelect.html('<option value="">Error loading shops</option>');
            }
        });
    } else {
        // Disable shop dropdown and clear options
        $shopSelect.prop('disabled', true);
        // Static JS file: avoid Blade translations here
        $shopSelect.html('<option value="">Select shop</option>');
        $('#shop-help-text').show();
        // Hide create shop button
        $('#create-shop-button-container').hide();
    }

    cod();
});

// Handle create shop button click
$(document).on('click', '#create-shop-btn', function () {
    var merchantId = $('#merchant_id').val();
    
    if (merchantId && merchantId !== '') {
        // Set the merchant ID in the modal
        $('#selectedMerchantId').val(merchantId);
        
        // Show the modal
        showCreateShopModal();
    } else {
        alert('Please select a merchant first before creating a shop.');
    }
});

// Handle shop selection including "Create New Shop" option
$(document).on('change', '#shopID', function () {
    var shopValue = $(this).val();
    
    if (shopValue === 'create_shop') {
        // Trigger the create shop button functionality
        $('#create-shop-btn').click();
        $(this).val(''); // Reset selection
    }
});

// Show create shop modal
function showCreateShopModal() {
    console.log('Showing create shop modal');
    $('#createShopModal').show();
    $('body').addClass('modal-open');
}

// Close create shop modal
function closeCreateShopModal() {
    console.log('Closing create shop modal');
    $('#createShopModal').hide();
    $('body').removeClass('modal-open');
    $('#createShopForm')[0].reset();
}

// Handle modal close events
$(document).on('click', '#closeCreateShopModal, #cancelCreateShopModal', function() {
    closeCreateShopModal();
});

// Close modal when clicking outside
$(document).on('click', '#createShopModal', function(e) {
    if (e.target === this) {
        closeCreateShopModal();
    }
});

// Emergency close function - press Escape key to close modal
$(document).keyup(function(e) {
    if (e.keyCode === 27) { // Escape key
        console.log('Escape key pressed, closing modal');
        closeCreateShopModal();
    }
});

// Handle form submission
$(document).on('click', '#submitCreateShop', function() {
    var formData = {
        name: $('#shopName').val(),
        contact_no: $('#shopPhone').val(),
        address: $('#shopAddress').val(),
        merchant_id: $('#selectedMerchantId').val(),
        default_shop: $('#setAsDefault').is(':checked') ? 1 : 0,
        status: 1 // Active status
    };
    
    // Validate required fields
    if (!formData.name.trim()) {
        alert('Please enter a shop name.');
        return;
    }
    
    if (!formData.contact_no.trim()) {
        alert('Please enter a contact phone number.');
        return;
    }
    
    if (!formData.address.trim()) {
        alert('Please enter an address.');
        return;
    }
    
    // Show loading state
    var submitBtn = $(this);
    var originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Creating...').prop('disabled', true);
    
    $.ajax({
        url: '/admin/merchant/shops/store-ajax',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            // Close modal
            closeCreateShopModal();
            
            // Reload shops for the merchant
            var merchantId = $('#selectedMerchantId').val();
            var $shopSelect = $('#shopID');
            var url = $shopSelect.data('url');
            
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    'id': merchantId, 
                    'shop': true
                },
                dataType: "html",
                success: function (data) {
                    $shopSelect.html(data);
                    
                    // Check if merchant now has shops
                    var hasShops = $shopSelect.find('option:not([disabled]):not([value="create_shop"])').length > 0;
                    if (hasShops) {
                        $('#create-shop-button-container').hide();
                    }
                    
                    shop(url);
                    deliveryCharge();
                }
            });
            
            // Show success message
            alert('Shop created successfully!');
        },
        error: function(xhr) {
            var errorMessage = 'An error occurred while creating the shop.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert('Error: ' + errorMessage);
        },
        complete: function() {
            // Reset button state
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
});

//cod charge dynamic
 function cod() {

    $.ajax({
        type : 'POST',
        url : $('#merchanturl').data('url'),
        data : {'merchant_id': $('#merchant_id').val()},
        dataType : "json",
        success : function (data) {
            $('#inside_city').val(data.inside_city);
            $('#sub_city').val(data.sub_city);
            $('#outside_city').val(data.outside_city);

        }
    });
 }
 //end cod charge dynamic

$(document).on('change', '#shopID', function () {
    var url = $(this).data('url');
    shop(url)
});

function shop(url){
    var shop_id = $("select#shopID option").filter(":selected").val();
    $.ajax({
        type : 'POST',
        url : url,
        data : {'id': shop_id,'shop':false},
        dataType : "html",
        success : function (data) {
            var shop = JSON.parse(data);
            $('#pickup_phone').val(shop.contact_no);
            $('#pickup_address').val(shop.address);
            $('#pickup_lat').val(shop.merchant_lat);
            $('#pickup_long').val(shop.merchant_long);
        }
    });
}
$('#categoryWeight').hide();
$('#weightID').hide();
$(document).on('change', '#category_id', function () {
    console.log($(this).val());
    var category_id = $(this).val();
    if(category_id !==''){
        $.ajax({
            type : 'POST',
            url : $(this).data('url'),
            data : {'category_id': $(this).val()},
            dataType : "html",
            success : function (data) {
                if(category_id == '1'){
                    $('#categoryWeight').show();
                    $('#weightID').show();
                    $('#weightID').html(data);
                    $( "#weightID" ).select2();
                    $('#weightID').attr('required');
                }else {
                    $('#categoryWeight').hide();
                    $('#weightID').hide();
                }
                deliveryCharge();
            }
        });

    }
});

$(document).on('change', '#delivery_type_id', function () {
    deliveryCharge();

    //cod charge calculation
    var delivery_type   = $(this).val();
    var cash_collection =  parseFloat($("#cash_collection").val());
    if(cash_collection == '' || isNaN(cash_collection)){
        cash_collection = 0;
    }

        var type = 0;
        if(delivery_type == 1 || delivery_type == 2){
            type = $("#inside_city").val();
        }else if(delivery_type == 3){
            type = $("#sub_city").val();
        }else if(delivery_type == 4){
            type = $("#outside_city").val();
        }else{
            type = 0;
        }
        var codAmount       = percentage(cash_collection,type);
        $('#codChargeAmount').text(codAmount.toFixed(2));
    //end cod charge calculation
});

$(document).on('change', '#weightID', function () {
    deliveryCharge();

});
$('#packagingShow').hide();
$(document).on('change', '#packaging_id', function () {
    var amount = parseFloat($("select#packaging_id option").filter(":selected").data('packagingamount'));
    console.log(amount);
    if(isNaN(amount) || amount === ''){
        $('#packagingShow').hide();
        amount = 0;
    }else{
        $('#packagingShow').show();
    }

    $('#packagingAmount').text(amount);
    totalSum();

});

$('.hideShowLiquidFragile').hide();
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
    totalSum();
}


function percentage(totalAmount,percentageAmount) {

     return totalAmount * (percentageAmount / 100);
}

$(document).on('keyup change', '#cash_collection', function () {
    var cash_collection =  parseFloat($(this).val());
    if(cash_collection === '' || isNaN(cash_collection)){
        $('#totalCashCollection').text('0.00');
        $('#codChargeAmount').text('0.00');

    }else {
        var codAmount = percentage(cash_collection, 0);
        $('#codChargeAmount').text(codAmount.toFixed(2));
        $('#totalCashCollection').text(cash_collection);



    }

    totalSum();

});

function deliveryCharge() {
    var merchant_id            = $("select#merchant_id option").filter(":selected").val();
    var category_id        = $("select#category_id option").filter(":selected").val();
    var weight             = $("select#weightID option").filter(":selected").val();
    var delivery_type_id   = $("select#delivery_type_id option").filter(":selected").val();

    if(merchant_id !=='' && category_id !=='' && delivery_type_id !==''){

        $.ajax({
            type : 'POST',
            url : deliverChargeUrl,
            data : {'merchant_id': merchant_id,'category_id':category_id,'weight':weight,'delivery_type_id':delivery_type_id},
            dataType : "json",
            success : function (data) {

                $('#deliveryChargeAmount').text(data);
                totalSum();
            }
        });
    }

}

function totalSum() {
    merchant();
   var totalCashCollection          =  parseFloat($('#totalCashCollection').text());
   var deliveryChargeAmount         =  parseFloat($('#deliveryChargeAmount').text());
   var codChargeAmount              =  parseFloat($('#codChargeAmount').text());
   var vatTex                       = parseFloat($('#merchantVat').val());
   var merchantCodCharge            = parseFloat( $('#merchantCodCharge').val());
   var liquidFragileAmount          =  parseFloat($('#liquidFragileAmount').text());
   var packagingAmount              =  parseFloat($('#packagingAmount').text());
   var totalAmount = (codChargeAmount+deliveryChargeAmount+liquidFragileAmount+packagingAmount);
   var vat = percentage(totalAmount, vatTex);
    $('#VatAmount').text(vat.toFixed(2));
    $('#totalDeliveryChargeAmount').text(totalAmount.toFixed(2));
    totalAmount +=vat;
   var totalCurrentAmount = (totalCashCollection-totalAmount);
   $('#netPayable').text(totalAmount.toFixed(2));
   $('#currentPayable').text(totalCurrentAmount.toFixed(2));
   var totalDeliveryChargeAmount     =  parseFloat($('#totalDeliveryChargeAmount').text());
   var currentPayable                =  parseFloat($('#currentPayable').text());
   var VatAmount                    =  parseFloat($('#VatAmount').text());
   var obj = {'vatTex':vatTex,'merchantCodCharge':merchantCodCharge,'totalCashCollection':totalCashCollection,'deliveryChargeAmount':deliveryChargeAmount,'codChargeAmount':codChargeAmount,'VatAmount':VatAmount,'liquidFragileAmount':liquidFragileAmount,'packagingAmount':packagingAmount,'totalDeliveryChargeAmount':totalDeliveryChargeAmount,'currentPayable':currentPayable}
   $('#chargeDetails').val(JSON.stringify(obj));

}

function merchant(){
    var merchant_id            = $("select#merchant_id option").filter(":selected").val();
    var delivery_type_id       = $("select#delivery_type_id option").filter(":selected").val();

    if(merchant_id !== ''){
        $.ajax({
            type : 'POST',
            url : merchantUrl,
            data : {'search': merchant_id,'searchQuery': false},
            dataType : "json",
            success : function (data) {
                var cash_collection =  parseFloat($('#cash_collection').val());
                if(isNaN(cash_collection)){
                    cash_collection = 0;
                }
                var merchantCodCharge = 0;
                var codAmount         = 0;
                if(delivery_type_id !=='' && delivery_type_id ==='1' || delivery_type_id ==='2'){
                    merchantCodCharge = data[0].cod_charges.inside_city;
                     codAmount = parseFloat(percentage(cash_collection, data[0].cod_charges.inside_city));
                }else if(delivery_type_id !=='' && delivery_type_id ==='3'){
                    merchantCodCharge = data[0].cod_charges.sub_city;
                     codAmount = parseFloat(percentage(cash_collection, data[0].cod_charges.sub_city));
                }else if(delivery_type_id !=='' && delivery_type_id ==='4') {
                    merchantCodCharge = data[0].cod_charges.outside_city;
                     codAmount = parseFloat(percentage(cash_collection, data[0].cod_charges.outside_city));
                }
                else {
                    merchantCodCharge = 0;
                    codAmount         = parseFloat(percentage(cash_collection, 0));
                }
                $('#merchantVat').val(data[0].vat);
                // Fix incorrect element ID usage (remove '#')
                document.getElementById('merchantCodCharge').value = merchantCodCharge;
            }
        });
    }
}

