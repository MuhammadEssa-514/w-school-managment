$(document).ready(function($) {
    $('#receiverGroups').show();
    $('#receiverTeachers').hide();
    $('#receiverParents').hide();
    $('#receiverStudents').hide();
    $("#showGroup").click(function() {
        // $('.r_id').multiselect("clearSelection");
        // $('.r_id').multiselect('refresh');
        $('.r_id').selectpicker();
        $(this).closest('ul').find('li').removeClass('active');
        $(this).closest('li').addClass('active');
        $('#receiverGroups').show();
        $('#receiverTeachers').hide();
        $('#receiverParents').hide();
        $('#receiverStudents').hide();
    });
    $("#showTeachers").click(function() {
        // $('.r_id').multiselect("clearSelection");
        // $('.r_id').multiselect('refresh');
        $('.r_id').selectpicker();
        $(this).closest('ul').find('li').removeClass('active');
        $(this).closest('li').addClass('active');
        $('#receiverGroups').hide();
        $('#receiverTeachers').show();
        $('#receiverParents').hide();
        $('#receiverStudents').hide();
    });
    $("#showStudents").click(function() {
        // $('.r_id').multiselect("clearSelection");
        // $('.r_id').multiselect('refresh');
        $('.r_id').selectpicker();
        $(this).closest('ul').find('li').removeClass('active');
        $(this).closest('li').addClass('active');
        $('#receiverGroups').hide();
        $('#receiverTeachers').hide();
        $('#receiverParents').hide();
        $('#receiverStudents').show();
    });
    $("#showParents").click(function() {
        // $('.r_id').multiselect("clearSelection");
        // $('.r_id').multiselect('refresh');
        $('.r_id').selectpicker();
        $(this).closest('ul').find('li').removeClass('active');
        $(this).closest('li').addClass('active');
        $('#receiverGroups').hide();
        $('#receiverTeachers').hide();
        $('#receiverParents').show();
        $('#receiverStudents').hide();
    });
    $(document).on("click", ".wpsp-replay-message-btn", function() {
        var main_m_id = $(this).attr("data-main_m_id");
        var replay_m_id = $(this).attr("data-replay_m_id");
        var senderid = $(this).attr("data-senderid");
        var reciver_id = $(this).attr("data-reciver_id");
        var html = '<form name="replySubMessage" action="javascript:;" class="replySubMessageForm" method="post">' +
            '<div class="form-group bubble">' +
            '<input type="hidden" name="main_m_id" value="' + main_m_id + '">' +
            '<input type="hidden" name="replay_m_id" value="' + replay_m_id + '">' +
            '<input type="hidden" name="reciver_id" value="' + reciver_id + '">' +
            '<textarea id="message" name="message" style="width:100%;height:100%;background:transparent" class="wpsp-form-control" placeholder="Enter Message"></textarea></div></br><span class="form-group">' +
            '<input type="submit" class="wpsp-btn wpsp-btn-success" class="sendSubReply" style="margin:10px" value="SEND">' +
            '</span><div class="viewMessageContainer"></div>' +
            '</form>';
        $(this).closest('li').append(html);
    });
    $(document).on('submit', '.replySubMessageForm', function() {
        if ($(this).find('textarea').val() != '') {
            var data = $(this).serializeArray();
            $('.sendSubReply').attr('disabled', 'disabled');
            data.push({
                name: 'action',
                value: 'sendSubMessage'
            });
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                success: function(mres) {
                    location.reload(true);
                }
            });
        }
    });
    $(document).on("click", "#createMessage", function() {
        var linkAttrib = $(this).attr('data-pop');
        $('#' + linkAttrib).addClass("wpsp-popVisible");
        $('body').addClass('wpsp-bodyFixed')
    });
    $("#checkAll").click(function() {
        $(".mid_checkbox").prop('checked', $(this).prop('checked'));
    });
    $('#message-list').dataTable({
        language: {
            paginate: {
                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
            },
            search: "",
            searchPlaceholder: "Search..."
        },
        "dom": '<"wpsp-dataTable-top"f>rt<"wpsp-dataTable-bottom"<"wpsp-length-info"li>p<"clear">>',
        "order": [],
        "columnDefs": [{
            "targets": 'nosort',
            "orderable": false,
        }],
        drawCallback: function(settings) {
            var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
            pagination.toggle(this.api().page.info().pages > 1);
        },
        responsive: true,
        pageLength: 25,
    });

    // $('.teacher_multi_select').multiselect({
    //     columns: 1,
    //     placeholder: 'Select teacher(s)',
    //     search: true
    // });
    // $('.student_multi_select').multiselect({
    //     columns: 1,
    //     placeholder: 'Select student(s)',
    //     search: true
    // });

    // $('.parent_multi_select').multiselect({
    //     columns: 1,
    //     placeholder: 'Select parent(s)',
    //     search: true
    // });
    $("#newMessageForm").validate({
        onkeyup: false,
        rules: {
            'r_id': {
                required: true
            },
            'subject': {
                required: true
            },
            'message': {
                required: true,
                minlength: 10
            }
        },
        messages: {
            r_id: {
                required: "Please add at least one receiver"
            },
            subject: {
                required: "Please enter subject"
            },
            message: {
                required: "Please enter message",
                minlength: "Message should contain of at least 10 characters"
            }
        },
        submitHandler: function(form) {
            $('#send').prop('disabled', true);
            $('#send').val('Sending...');
            var data = $('#newMessageForm').serializeArray();
            data.push({
                name: 'action',
                value: 'sendMessage'
            });
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                success: function(mres) {
                    if (mres == 'Message sent successfully') {
                        $('#message-resposive').html('').removeClass('errormessage').addClass('successmessage').html(mres);
                        var delay = 1000;
                        setTimeout(function() {
                            location.reload(true);
                        }, delay);
                        $('#newMessageForm').trigger("reset");
                    } else {
                        $('#message-resposive').html('').removeClass('successmessage').addClass('errormessage').html(mres);
                    }
                    $('#send').prop('disabled', false);
                    $('#send').val('Send')
                },
                error: function() {
                    $('#send').prop('disabled', false);
                    $('#send').val('Send')
                },
                complete: function() {
                    $('.pnloader').remove();
                    $('#send').removeAttr('disabled');
                }
            });
        }
    });
    $(document).on('submit', '#replyMessageForm', function() {
        if ($('#replyMessageForm').find('#message').val() != '') {
            var data = $(this).serializeArray();
            $('#sendReply').attr('disabled', 'disabled');
            data.push({
                name: 'action',
                value: 'sendMessage'
            });
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                success: function(mres) {
                    $('#viewMessageContainer').html(mres);
                    $('#sendReply').removeAttr('disabled');
                    location.reload(true);
                }
            });
        }
    });
    $(document).on('click', '#student_teacher', function(e) {
        if ($('#student_teacher').is(':checked')) {
            $('.wp-subject-list').addClass('none');
            var classid = $('input[name=childname]:checked').attr('classid');
            $('.wp-subject-name').attr('checked', false); //uncheck checkbox
            $('.class-name-' + classid).removeClass('none');
        } else {
            $('.wp-subject-list').addClass('none');
            $('.wp-subject-name').attr('checked', false); //uncheck checkbox
        }
    });
    $(document).on('click', '.msg-child-list', function(e) {
        $('#student_classteacher').val($(this).attr('teacherid'));
        $('.wpsps-message-list').removeClass('none');
        $('.wp-subject-list').addClass('none');
        if ($('#student_teacher').is(':checked')) {
            $('.wp-subject-list').addClass('none');
            var classid = $('input[name=childname]:checked').attr('classid');
            $('.wp-subject-name').attr('checked', false); //uncheck checkbox
            $('.class-name-' + classid).removeClass('none');
        } else {
            $('.wp-subject-list').addClass('none');
            $('.wp-subject-name').attr('checked', false); //uncheck checkbox
        }
    });
    $(document).on('click', '.delete_messages', function(e) {
        $("#DeleteModal").css("display", "block");
        var mid = $(this).data('id');
        var tid = $(this).data('trash');
        $("#DeleteModal").attr('data-id', mid);
        $("#DeleteModal").attr('data-trash', tid);
        $("#DeleteModal").attr('data-multidetele', 0);
    });
    $(document).on('click', '.ClassDeleteBt', function(e) {
        jQuery(this).attr("disabled", true);
        var trashid = $(this).closest('#DeleteModal').data('trash');
        var multidetele = $(this).closest('#DeleteModal').data('multidetele');
        var mid = [];
        if (multidetele == 1) {

            jQuery("input[name='mid[]']:checked").each(function() {
                mid.push(jQuery(this).val());
            });
        } else {
            mid = $(this).closest('#DeleteModal').data('id');
        }
        var data = [];
        data.push({
            name: 'action',
            value: 'deleteMessage'
        }, {
            name: 'mid',
            value: mid
        }, {
            name: 'trashid',
            value: trashid
        }, {
            name: 'multipledelete',
            value: multidetele
        });
        //cid = '0';
        jQuery.post(ajax_url, data, function(cddata) {
            if (cddata == 'true') {
                $("#DeleteModal").css("display", "none");
                $("#SuccessModal").css("display", "block");
                location.reload();
            } else {
                $(".wpsp-popup-return-data").html('Operation failed.Something went wrong!');
                $("#SuccessModal").css("display", "block");
                $("#SavingModal").css("display", "none");
                $("#WarningModal").css("display", "block");
                $("#WarningModal").addClass("wpsp-popVisible");
            }
            jQuery('.ClassDeleteBt').attr("disabled", false);
        });
    });
    $(document).on('change', '#bulkaction', function(e) {
        $("#DeleteModal").css("display", "block");
        var tid = $(this).data('trash');
        var mid = $(this).data('id');
        $("#DeleteModal").attr('data-multidetele', 1);
        $("#DeleteModal").attr('data-trash', tid);
    });
});