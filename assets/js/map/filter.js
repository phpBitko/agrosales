$(function () {

    $('.datepicker').datepicker({
        clearBtn: true,
        language: 'uk',
    });

    $('.advertisement-filter').removeClass('col-3');
    $('.box').removeClass('hidden');

    //-------------------------------маска Inputmask

    $('#item_filter_area_left_number, #item_filter_area_right_number').inputmask("9{1,10}.9{1,4}", {
        placeholder: "",
        radixPoint: ".",
        allowMinus:false,
        showMaskOnHover: false,
        showMaskOnFocus: false,

    });
    $('#item_filter_price_left_number, #item_filter_price_right_number').inputmask("9{1,10}", {
        placeholder: "",
        rightAlign: false,
        allowMinus:false,
    });

})