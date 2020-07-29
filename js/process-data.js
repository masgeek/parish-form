'use strict'

let jsonProfile

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
                    jsonProfile = jd;
                    const myContainer = jQuery('#multiRecordMatches');
                    myContainer.html(null);
                    const defaultData = jd[0];
                    jQuery('#group-id').val(defaultData.group_id).trigger('change');

                    jd.forEach(function (jsonData, index) {
                        const openDiv = `<div class="funkyradio-primary">`
                        const closeDiv = `</div>`;
                        const radioLabel = `<label for="prefill-${index}">${jsonData.surname} ${jsonData.other_names}</label>`;
                        const radio = `<input type="radio" id="prefill-${index}" name="recordMatches" class="record-matches" value="${index}">`;

                        const theString = openDiv + radio + radioLabel + closeDiv;
                        myContainer.append(theString)
                    });

                    jQuery('#multiRecordModal').modal('show');
                } else {
                    jQuery('#surname').val(null).prop("readonly", false);
                    jQuery('#other_names').val(null).prop("readonly", false);
                    jQuery('#age').val(null);
                    jQuery('#group-id').val(null).trigger('change');
                    $('input[name="adultFlag"]').prop('checked', false);
                    $('input[name="genderFlag"]').prop('checked', false);
                    jQuery('.prefill-section').slideDown();
                }
            }, 'json');
        }
    });

    jQuery("#multiRecordMatches").on("change", "input", function () {
        const id = parseInt(this.value);

        const jd = jsonProfile[id];

        const adultFlag = jd.adult;
        const genderFlag = jd.gender;
        jQuery('#surname').val(jd.surname).prop("readonly", true);
        jQuery('#other_names').val(jd.other_names).prop("readonly", true);
        jQuery('#age').val(jd.age);
        jQuery('#group-id').val(jd.group_id).trigger('change');
        jQuery("input[name=genderFlag][value=" + genderFlag + "]").prop('checked', true);
        jQuery("input[name=adultFlag][value=" + adultFlag + "]").prop('checked', true).trigger('change');
        //now we hide the other fields
        jQuery('.prefill-section').slideUp();

    })

    jQuery('#add-child').on('click', function () {
        jQuery('#surname').val(null).prop("readonly", false);
        jQuery('#other_names').val(null).prop("readonly", false);
        jQuery('#age').val(null);
        //$('input[name="adultFlag"]').prop('checked', false);
        jQuery('input[name="genderFlag"]').prop('checked', false);
        jQuery('.prefill-section').slideDown();
        jQuery('#multiRecordModal').modal('hide');
    });

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
        jQuery('#choir_seat_no').val(null);
        //let us evaluate the seating
        jQuery.post('utils/sitting-chart.php', {schedule_id: scheduleID}, function (resp, testStatus, jqXHR) {
            if (resp.hasData) {
                const seatData = resp.data;
                //let us process this things now
                const myContainer = jQuery('#choirSeatsContainer');
                myContainer.html(null);
                seatData.forEach(function (seat, index) {
                    let seatRow = index + 1;
                    let rowLabel;
                    switch (seatRow) {
                        case 1:
                            rowLabel = `Soprano seats `;
                            break;
                        case 2:
                            rowLabel = `Alto seats `;
                            break;
                        case 3:
                            rowLabel = `Tenor seats `;
                            break;
                        case 4:
                            rowLabel = `Bass seats `;
                            break;
                        default:
                            rowLabel = `Row ${seatRow} seats `;
                    }

                    let theString = `<div class="row no-radio"><label>${rowLabel}</label></div>`;
                    const closeDiv = `</div>`;
                    const radioLabel = `<div class="radio-toolbar row">`;
                    theString = theString.concat(radioLabel);
                    seat.forEach(function (seatInfo, seatIndex) {
                        const seatTaken = seatInfo.taken;
                        let seatRadioLabel = `<label for="choir-seats-${seatInfo.seatNo}">${seatInfo.seatNo}</label>`;
                        let radio = `<input type="radio" id="choir-seats-${seatInfo.seatNo}" name="choirSeats" class="choir-seats" value="${seatInfo.seatNo}">`;
                        if (seatTaken) {
                            seatRadioLabel = `<label for="choir-seats-${seatInfo.seatNo}">${seatInfo.seatNo}</label>`;
                            radio = `<input type="radio" id="choir-seats-${seatInfo.seatNo}" name="choirSeats" class="choir-seats" value="${seatInfo.seatNo}" disabled>`;
                        }

                        theString = theString.concat(radio + seatRadioLabel);
                    });

                    const closed = theString.concat(closeDiv);
                    myContainer.append(closed);

                    console.log(closed);
                });
            }
        }, 'json');

        jQuery('#schedule_id').val(scheduleID);
    });

    jQuery('.choir').on('change', function () {
        const isChoir = parseInt(this.value);
        checkChoirSelection(isChoir);
    }).on('click', function () {
        const isChoir = parseInt(this.value);
        checkChoirSelection(isChoir);
    });

    function checkChoirSelection(isChoir) {
        if (isChoir === 1) {
            jQuery('.lector').slideUp(function () {
                jQuery("input[name=lectorFlag][value=" + 0 + "]").prop('checked', true);
            });

            jQuery('#choirSeatsModal').modal('show');

        } else {
            jQuery('.lector').slideDown(function () {
                $('input[name="lectorFlag"]').prop('checked', false);
            });
        }
    }

    jQuery('#choirSeatsContainer').on('change', "input", function () {
        const seatNo = parseInt(this.value);
        jQuery('#choir_seat_no').val(seatNo);
    });

    jQuery('#btn-register').on('click', function () {
        const myform = jQuery('#mass-reg-form');
        const rbs = document.querySelectorAll('input[name="adultFlag"]');
        const gender = document.querySelectorAll('input[name="genderFlag"]');
        const choir = document.querySelectorAll('input[name="choirFlag"]');
        const lector = document.querySelectorAll('input[name="lectorFlag"]');
        const choirSeatNo = jQuery('#choir_seat_no').val();

        let isAdult;
        let genderFlag;
        let choirFlag;
        let lectorFlag;
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

        for (const rb of lector) {
            if (rb.checked) {
                lectorFlag = parseInt(rb.value);
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

        if (choirFlag === 1) {
            if (isEmpty(choirSeatNo) || isNaN(choirSeatNo)) {
                swal({
                    title: "Missing seat number",
                    text: "It appears you have not selected a seat number",
                    icon: "warning",
                });
                return;
            }
        }
        if (isEmpty(lectorFlag) || isNaN(lectorFlag)) {
            swal({
                title: "Missing lector indication",
                text: "Please specify if you are a lector for this mass or not",
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
