jQuery(document).ready(function ($) {
    $('#authorize_payment_card_holder_name').on('input', function () {
        var creditCardName = $(this).val();
        var regex = /^[a-zA-Z]{2,}\s[a-zA-Z]{2,}$/;
        $('#authorize_payment_card_holder_name-error').remove(); // Remove any previous error messages
        if (!regex.test(creditCardName)) {
            $(this).after('<span id="authorize_payment_card_holder_name-error" class="afcf7-error">Invalid Name</span>');
        }
    });


    $('#authorize_payment_cvv').on('input', function () {
        var cvv = $(this).val();
        var regex = /^[0-9]{3,4}$/;
        $('#cvv-input-error').remove(); // Remove any previous error messages
        if (!regex.test(cvv)) {
            $(this).after('<span id="cvv-input-error" class="afcf7-error">Invalid CVV</span>');
        }
    });


    //Format and validate card expire
    jQuery("#authorize_payment_expire").keypress(function (event) {
        var charCode = (event.which) ? event.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            console.log('character entry');
            return false;
        }
        let formatedExpire = formatExpiry(jQuery("#authorize_payment_expire").val());
        jQuery("#authorize_payment_expire").val(formatedExpire);
    });


    //Format and validate credit card number
    jQuery("#authorize_payment_card_number").keypress(function (event) {

        //Allow only number entry
        var charCode = (event.which) ? event.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            console.log('character entry');
            return false;
        }

        //add space every 4 number
        if (jQuery("#authorize_payment_card_number").val().length <= 19) {
            let formatedValue = formatCreditCardNumber(jQuery("#authorize_payment_card_number").val());
            jQuery("#authorize_payment_card_number").val(formatedValue);
        }
    });
});


function formatExpiry(value) {

    let expiry = value
        .replace(
            /^([1-9]\/|[2-9])$/g,
            '0$1/' // 3 > 03/
        )
        .replace(
            /^(0[1-9]|1[0-2])$/g,
            '$1/' // 11 > 11/
        )
        .replace(
            /^([0-1])([3-9])$/g,
            '0$1/$2' // 13 > 01/3
        )
        .replace(
            /^(0?[1-9]|1[0-2])([0-9]{2})$/g,
            '$1/$2' // 141 > 01/41
        )
        .replace(
            /^([0]+)\/|[0]+$/g,
            '0' // 0/ > 0 and 00 > 0
        )
        .replace(
            /[^\d\/]|^[\/]*$/g,
            '' // To allow only digits and `/`
        )
        .replace(
            /\/\//g,
            '/' // Prevent entering more than 1 `/`
        )

    if (expiry.length <= 5) {
        return expiry;
    }
}


function formatCreditCardNumber(value) {

    let formatValue = value
        .replace(/[^0-9]/g, '')
        .replace(/(.{4})/g, '$1 ')
        .trim()
    if (formatValue.length <= 19) {
        return formatValue;
    }
}


// jQuery(document).ready(function ($) {
//     $('#authorize_payment_expire').on('input', function () {
//         var expireDate = $(this).val();
//         var regex = /^(0[1-9]|1[0-2])\/?([0-9]{4}|[0-9]{2})$/;
//         $('#expire-date-input-error').remove(); // Remove any previous error messages
//         if (expireDate.length == 2 && expireDate.indexOf('/') == -1) {
//             $(this).val(expireDate + '/');
//         } else if (!regex.test(expireDate)) {
//             $(this).after('<span id="expire-date-input-error" class="error">Invalid date format</span>');
//         } else {
//             var currentDate = new Date();
//             var inputDate = new Date('01/' + expireDate);
//             if (inputDate < currentDate) {
//                 $(this).after('<span id="expire-date-input-error" class="error">Card has expired</span>');
//             }
//         }
//     });
// });