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

        const rbs = document.querySelectorAll('input[name="adultFlag"]');
        const gender = document.querySelectorAll('input[name="genderFlag"]');
        const choir = document.querySelectorAll('input[name="choirFlag"]');

        let isAdult;
        let genderFlag;
        let choirFlag;
        for (const rb of rbs) {
            if (rb.checked) {
                isAdult = parseInt(rb.value);
                break;
            }
        }

        for (const rb of gender) {
            if (rb.checked) {
                genderFlag = rb.value;
                break;
            }
        }

        for (const rb of choir) {
            if (rb.checked) {
                choirFlag = parseInt(rb.value);
                break;
            }
        }

        if (isEmpty(isAdult)) {
            swal({
                title: "Missing age group",
                text: "Please indicate whether your are an adult or not",
                icon: "warning",
            });
            return;
        }

        if (isEmpty(genderFlag)) {
            swal({
                title: "Missing gender",
                text: "Please indicate your gender",
                icon: "warning",
            });
            return;
        }

        let ageIsValid = true;
        let age = parseInt(jQuery('#age').val());

        if (isEmpty(age) || isNaN(age)) {
            age = 0;
        }
        if (isAdult === 1) {
            if (age <= 17) {
                ageIsValid = false;
            }
        } else {
            if (age < 13 || age > 17) {
                ageIsValid = false;
            }
        }


        if (ageIsValid === false) {
            swal({
                closeOnClickOutside: false,
                closeOnEsc: false,
                title: "Incorrect age provided",
                text: isAdult ? "Sorry, age does not meet your selection for adult option" : "Booking not allowed for provided age",
                icon: "error"
            });
            return;
        }
        if (myform[0].checkValidity() === false) {
            myform.addClass('was-validated');
            return;
        }

        if (isEmpty(choirFlag) || isNaN(choirFlag)) {
            swal({
                title: "Missing choir indication",
                text: "Please specify if you are a choir member for this mass or not",
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
