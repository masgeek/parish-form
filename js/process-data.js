"use strict"

jQuery(document).ready(function () {
    let today = new Date();
    const dd = String(today.getDate()).padStart(2, '0');
    const mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    const yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd;
    const config = {
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
        minDate: "today"
        // minDate: today
    };
    $(".datepicker").flatpickr(config);
    // jQuery('.datepicker').datepicker({
    //     modal: true,
    //     showOnFocus: false,
    //     value: today,
    //     minDate: today,
    //     // disableDaysOfWeek: [1,2,3,4,5,6]
    // });

    // jQuery(function ($) {
    //     //let us call the table data
    //     jQuery.ajax({
    //         type: 'GET',
    //         url: 'outstations.php',
    //         dataType: "json",
    //         success: function (data, textStatus, XMLHttpRequest) {
    //
    //             const arr = data.map(function (item) {
    //
    //                 console.log(item);
    //                 return item;
    //             })
    //
    //         },
    //         error: function (XMLHttpRequest, textStatus, errorThrown) {
    //             console.log(XMLHttpRequest);
    //             console.log(errorThrown);
    //         }
    //     });
    // });

    jQuery('#group').on('change', function () {
        const groupId = this.value;
        const estateId = jQuery("#estate-" + groupId).val();
        console.log(estateId);
        jQuery.getJSON('get-estates.php', {group_id: groupId, estate_id: estateId}, function (data, testStatus, jqXHR) {
            jQuery('#estate')
                .val(data[0].estate_name)
                .trigger('focus');
        });
    });

    jQuery('#mass_date').on('change', function () {
        const massDate = this.value;
        console.log(massDate);
    });
});
