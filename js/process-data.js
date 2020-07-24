'use strict'

jQuery(document).ready(function () {

    jQuery("#mass-reg-form").submit(function (e) {
        return false;
    });

    jQuery('.adult').on('change', function () {

        const adultFlag = parseInt(this.value);
        jQuery('#adult').val(adultFlag);
        if (adultFlag === 1) {
            //change to adult labels
            jQuery('#mobile-label').html("What is your mobile number?");
            jQuery('#national-id-label').html("What is your  national id?");
            console.log("Adult here");
        } else {
            jQuery('#mobile-label').html("Please enter your parent's mobile number");
            jQuery('#national-id-label').html("Please enter your parent's national id");
            console.log("Adult not here");
        }
        console.log("Age radio button", this.value);
    });

    jQuery('#group-id').on('change', function () {
        const groupId = this.value;
        const estateId = jQuery("#estate-" + groupId).val();
        jQuery('#estate_name').val(null);
        if (!isEmpty(estateId)) {
            console.log("Estate id is", estateId);
            jQuery.getJSON('utils/get-estates.php', {
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
    });

    jQuery('#btn-register').on('click', function () {


        const myform = jQuery('#mass-reg-form');

        let ageIsValid = true;
        const age = parseInt(jQuery('#age').val());
        let isAdult = jQuery('#adult').val();

        if (isAdult === '1') {
            if (age <= 17) {
                ageIsValid = false;
            }
        }
        if (isAdult === '0') {
            if (age < 13 || age > 17) {
                ageIsValid = false;
            }
        }

        if (ageIsValid === false) {
            swal({
                closeOnClickOutside: false,
                closeOnEsc: false,
                title: "Invalid age",
                text: "Please provide correct age",
                icon: "error"
            });
        }
        if (myform[0].checkValidity() === false || ageIsValid === false) {
            myform.addClass('was-validated');
            swal({
                title: "Missing values",
                text: "Please ensure all information is provided",
                icon: "warning",
            });
            return;
        }
        //proceed with normal operations
        const formData = myform.serialize();

        jQuery.ajax({
            type: 'POST',
            url: 'utils/MassRegister.php',
            dataType: "json",
            data: formData,
            success: function (resp, textStatus, XMLHttpRequest) {
                const scheduleId = resp.mass_schedule_id;
                if (resp.valid === true) {
                    myform.trigger('reset'); //clear the form
                    jQuery('#mass-card').addClass('hidden');
                    jQuery('#success-card').removeClass('hidden');
                    jQuery('#surname-summary').html(resp.data.surname);
                    jQuery('#seat-summary').html(resp.data.seatNo);
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
