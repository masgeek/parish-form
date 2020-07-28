'use strict'

jQuery(document).ready(function () {

    jQuery("#mass-reg-form").submit(function (e) {
        return false;
    });

    jQuery('.prefill-form').on('change', function () {
        const nationalId = jQuery('#national_id').val();
        const mobileNumber = jQuery('#mobile').val();

        const data = {
            nationalId: nationalId,
            mobileNumber: mobileNumber
        };
        if (!isEmpty(nationalId) && !isEmpty(mobileNumber)) {
            //check prefill values
            jQuery.post('utils/prefill-form.php', data, function (resp, testStatus, jqXHR) {
                if (resp.hasData) {
                    const jd = resp.data;
                    const adultFlag = jd.adult;
                    const genderFlag = jd.gender;
                    jQuery('#surname').val(jd.surname);
                    jQuery('#other_names').val(jd.other_names);
                    jQuery('#age').val(jd.age);
                    jQuery('#group-id').val(jd.group_id).trigger('change');
                    jQuery("input[name=genderFlag][value=" + genderFlag + "]").prop('checked', true);
                    jQuery("input[name=adultFlag][value=" + adultFlag + "]").prop('checked', true).trigger('change');

                    //now we hide the other fields
                    jQuery('.prefill-section').slideUp();
                } else {
                    jQuery('.prefill-section').slideDown();
                }
            }, 'json');
        }
    })

    jQuery('.choir').on('change', function () {
        const flag = parseInt(this.value);
        if (flag === 1) {
            // jQuery('.choir-seats').removeClass('hidden');
            //jQuery('.choir-seats').slideDown();
        } else {
            // jQuery('.choir-seats').addClass('hidden');
            //jQuery('.choir-seats').slideUp();
        }
    })
    jQuery('.adult').on('change', function () {

        const adultFlag = parseInt(this.value);
        if (adultFlag === 1) {
            //change to adult labels
            jQuery('#mobile-label').html("What is your mobile number?");
            jQuery('#national-id-label').html("What is your  national id?");
        } else {
            jQuery('#mobile-label').html("Please enter your parent's mobile number");
            jQuery('#national-id-label').html("Please enter your parent's national id");
        }
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
        jQuery('#schedule_id').val(scheduleID);
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
                jQuery('#choir-seats-left-' + scheduleId).html(resp.choirSeatsLeft);
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
