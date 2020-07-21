'use strict'

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
    // $(".datepicker").flatpickr(config);
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

    // jQuery("#mass-reg-form").submit(function (e) {
    //     return false;
    // });

    jQuery('#group-id').on('change', function () {
        const groupId = this.value;
        const estateId = jQuery("#estate-" + groupId).val();
        jQuery('#estate_name').val(null);
        if (!isEmpty(estateId)) {
            console.log("Estate id is", estateId)
            jQuery.getJSON('get-estates.php', {
                group_id: groupId,
                estate_id: estateId
            }, function (data, testStatus, jqXHR) {
                console.log("Estate data is", data);
                jQuery('#estate_name').val(data[0].estate_name);
            });
        }
    });

    jQuery('.mass_schedule').on('change', function () {
        const scheduleID = this.value;
        const massCapacity = jQuery("#mass-capacity-" + scheduleID).val();
        jQuery('#mass-capacity').val(massCapacity);
        //check for available spaces
        console.log(scheduleID);
    });

    jQuery('#btn-register').on('click', function () {

        const myform = jQuery('#mass-reg-form');
        if (myform[0].checkValidity() === false) {
            myform.addClass('was-validated');
            return;
        }
        //proceed with normal operations
        const formData = myform.serialize();

        jQuery.ajax({
            type: 'POST',
            url: 'MassRegister.php',
            dataType: "json",
            data: formData,
            success: function (resp, textStatus, XMLHttpRequest) {
                console.log(resp);
                const scheduleId = resp.mass_schedule_id;
                if (resp.valid === true) {
                    swal({
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        title: resp.data.message['title'],
                        text: resp.data.message['text'],
                        icon: "success",
                    });
                    myform.trigger('reset'); //clear the form
                    //show a banner
                } else {
                    swal({
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        title: resp.data.message['title'],
                        text: resp.data.message['text'],
                        icon: "error",
                        button: "Retry",
                    });
                }

                jQuery('#seats-left-' + scheduleId).html(resp.seatsLeft);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(XMLHttpRequest.responseText);
                swal({
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    title: "Mass Registration not successful",
                    text: "Your mass registration was not successful, please try again",
                    icon: "danger",
                    button: "Retry",
                });
            }
        });
    });
});

function isEmpty(val) {
    return (val === undefined || val == null || val.length <= 0);
}
