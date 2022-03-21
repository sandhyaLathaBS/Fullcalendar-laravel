<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    .error {
        color: red;
        font-size: 12px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>SMT GROUP</h2>
        <br />
        <div id='calendar'></div>
    </div>

    <!-- The Modal -->
    <div class="modal" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Book OT</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{route('booking')}}" id="bookOtAppointment" method="post">
                    <!-- Modal body -->
                    <div class="modal-body">
                        <input type="hidden" value="" onchange="getOTDetails()" required class="form-control"
                            id="otDate" name="otDate">
                        <div class="mb-3">
                            <label for="otDuration" class="form-label">Duration:</label>
                            <input type="number" min="1" required onchange="getOTDetails()" value=""
                                class="form-control" id="otDuration" placeholder="Enter OP Duration" name="otDuration">
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="otStart" class="form-label">Start Time:</label>
                            <input type="time" onchange="getOTDetails()" required value="" class="form-control"
                                id="otStart" placeholder="Enter OP Start Time" name="otStart">
                        </div>
                        <div style="display: none;" id="showOTDetails">
                            <div class="mb-3 mt-3">
                                <label for="otDoctor" class="form-label">Doctor:</label>
                                <select class="form-control" required id="otDoctor" name="otDoctor">
                                    <option value="">Please Choose</option>
                                </select>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="otPateint" class="form-label">Pateint:</label>
                                <select class="form-control" required id="otPateint" name="otPateint">
                                    <option value="">Please Choose</option>
                                </select>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="otRoom" class="form-label">Room:</label>
                                <select class="form-control" required id="otRoom" name="otRoom">
                                    <option value="">Please Choose</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submitButton" disabled class="btn btn-success">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
$(function() {
    $('#bookOtAppointment').validate({
        rules: {
            otDate: {
                required: true,
            },
            otDuration: {
                required: true,
                digits: true,
                min: 0
            },
            otStart: {
                required: true
            },
            otDoctor: {
                required: true
            },
            otPateint: {
                required: true
            },
            otRoom: {
                required: true
            },
            username: {
                required: true
            }
        },
        messages: {
            otDate: {
                required: 'Please Select a date',
            },
            otStart: {
                required: 'Please Select a time',
            },
            otDoctor: {
                required: 'Please Select a Doctor',
            },
            otPateint: {
                required: 'Please Select a Pateint',
            },
            otRoom: {
                required: 'Please Select a Room',
            },
            otDuration: {
                required: 'Please enter a duration <b>(digits only)</b>',
                digits: "Please enter a <i>valid</i>duration",
                min: "Please enter a <i>minimum</i> hour of 1"
            }
        }

    }); //valdate end
});

function getOTDetails() {
    inputError = 0;
    $("#bookOtAppointment").find("input").not(':hidden').each(function() {
        if ($.trim($(this).val()) == '') {
            inputError++;
            $(this).focus();
        }
    });
    if (inputError == 0) {
        start = $("#bookOtAppointment #otDate").val();
        otDuration = $("#bookOtAppointment #otDuration").val();
        otStart = $("#bookOtAppointment #otStart").val();
        var today = moment(new Date(), 'DD.MM.YYYY').format('YYYY-MM-DD');
        if (start >= today) {
            $.ajax({
                url: "{{url('theater-details')}}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'start': start,
                    'otDuration': otDuration,
                    'otStart': otStart
                },
                type: "post",
                success: function(data) {
                    console.log(data);
                    if (data.length !== 0) {
                        docorOptions = '';
                        pateintOptions = '';
                        OTOptions = '';
                        selectedDoctor = '';
                        selectedPateint = '';
                        selectedOT = '';
                        if ($('#showOTDetails').is(':visible')) {
                            selectedDoctor = $('#showOTDetails #otDoctor').val();
                            selectedPateint = $('#showOTDetails #otPateint').val();
                            selectedOT = $('#showOTDetails #otRoom').val();
                        }
                        if (data.doctors.length > 0) {
                            data.doctors.map(function(doctor) {
                                selected = '';
                                if (selectedDoctor != '' && selectedDoctor == doctor.id) {
                                    selected = 'selected';
                                }
                                docorOptions += '<option ' + selected + ' value="' + doctor.id +
                                    '"> ' + doctor.name + '</option>';
                                $("#showOTDetails #otDoctor").find('option').not(':first').remove();
                                $("#showOTDetails #otDoctor option:first").after(docorOptions);
                            });
                        }
                        if (data.pateints.length > 0) {
                            data.pateints.map(function(pateint) {
                                selected = '';
                                if (selectedPateint != '' && selectedPateint == pateint.id) {
                                    selected = 'selected';
                                }
                                pateintOptions += '<option ' + selected + ' value="' + pateint.id +
                                    '"> ' + pateint.name + '</option>';
                                $("#showOTDetails #otPateint").find('option').not(':first')
                                    .remove();
                                $("#showOTDetails #otPateint option:first").after(pateintOptions);
                            });
                        }
                        if (data.otRomms.length > 0) {
                            data.otRomms.map(function(otRoom) {
                                selected = '';
                                if (selectedOT != '' && selectedOT == otRoom.id) {
                                    selected = 'selected';
                                }
                                OTOptions += '<option ' + selected + ' value="' + otRoom.id +
                                    '"> ' + otRoom.room_no + '</option>';
                                $("#showOTDetails #otRoom").find('option').not(':first')
                                    .remove();
                                $("#showOTDetails #otRoom option:first").after(OTOptions);
                            });
                        }
                        $("#showOTDetails").show();
                        $("#submitButton").removeAttr('disabled');
                    } else {
                        refreshModal();
                        $("#myModal").modal('hide');
                        swal({
                            title: "Invalid..!",
                            text: "Please select another date",
                        });
                    }
                }
            });
        }
    }
}

function refreshModal() {
    $("#bookOtAppointment").find("input, select").val('');
    $("#showOTDetails #otPateint").find('option').not(':first')
        .remove();
    $("#showOTDetails #otDoctor").find('option').not(':first').remove();
    $("#showOTDetails #otRoom").find('option').not(':first')
        .remove();
    $("#showOTDetails").hide();
    $("#submitButton").attr('disabled', true);
}

$(document).ready(function() {
    var calendar = $('#calendar').fullCalendar({
        initialView: 'timeGridWeek',
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,basicWeek,basicDay'
        },
        navLinks: true,
        editable: true,
        events: "getevent",
        displayEventTime: true,
        eventRender: function(event, element, view) {
            if (event.allDay === 'true') {
                event.allDay = true;
            } else {
                event.allDay = false;
            }
        },
        dayClick: (function() {
            return function(date, jsEvent, view) {
                var start = moment(date._d, 'DD.MM.YYYY').format('YYYY-MM-DD');
                var today = moment(new Date(), 'DD.MM.YYYY').format('YYYY-MM-DD');
                if (start >= today) {
                    refreshModal();
                    $("#bookOtAppointment #otDate").val(start);
                    $("#myModal").modal('show');
                } else {
                    swal("Invalid", "Please select a valid date", "error");
                }
            }
        })(),
        eventClick: function(event) {
            swal({
                title: "Delete",
                text: "Do you really want to delete?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "cancel",
                closeOnConfirm: false,
                closeOnCancel: false
            }).then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        type: "POST",
                        url: "delete",
                        data: "&id=" + event.id + '&_token=' +
                            "{{ csrf_token() }}",
                        success: function(response) {
                            if (parseInt(response) > 0) {
                                $('#calendar').fullCalendar('refetchEvents');
                                swal("Deleted", "You cancelled a booking)",
                                    "success");
                            } else {
                                swal("Warning", "Booking Expired", "error");
                            }
                        }
                    });
                }
            });
        }
    });
});
</script>

</html>