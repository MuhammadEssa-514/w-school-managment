jQuery(document).ready(function() {
  $('#ClassID').change(function() {
    $('#ExamID').attr('disabled', 'disabled');
    var data = $(this).serializeArray();
    data.push({
      name: 'action',
      value: 'subjectList'
    });
    data.push({
      name: 'get_exam_list',
      value: 1
    });
    jQuery.post(ajax_url, data, function(subject_list) {
      $('#SubjectID').find('option').remove().end();
      $('#ExamID').find('option').remove().end();
      var subject_json = $.parseJSON(subject_list);
      if (subject_json.exam.length != 0) {
        $("#ExamID").append('<option value="">Select Exam</option>');
        $.each(subject_json.exam, function(field, value) {
          $("#ExamID").append('<option value="' + value.eid + '">' + value.e_name + '</option>');
        });
        $('#ExamID').removeAttr('disabled');
      } else {
        $("#ExamID").append('<option value="">Select Exam</option>');
        $('#ExamID').attr('disabled', 'disabled');
        alert('Please create exam for this class.');
      }
    });
  });
  $('#ExamID').change(function() {
    $('#SubjectID').attr('disabled', 'disabled');
    var data = $(this).serializeArray();
    data.push({
      name: 'action',
      value: 'getMarksubject'
    });
    jQuery.post(ajax_url, data, function(subject_list) {
      var subject_json = $.parseJSON(subject_list);
      if (subject_json.length != 0) {
        $('#SubjectID').find('option').remove().end();
        $.each(subject_json.subject, function(field, value) {
          $("#SubjectID").append('<option value="' + value.id + '">' + value.sub_name + '</option>');
        });
        $('#SubjectID').removeAttr('disabled');
      } else {
        $("#SubjectID").append('<option value="">Select subject</option>');
        $('#SubjectID').attr('disabled', 'disabled');
        alert('<p style="color:red">Please add subject for this exam.</p>');
      }
    });
  });
  $('#wp-student-mark').dataTable({
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
    responsive: true
  });
  $('.MarkAction').click(function(e) {
    var mclass = $('#ClassID').val();
    var msub = $('#SubjectID').val();
    var mexam = $('#ExamID').val();
    var err = '';
    if (mclass == '' || mclass == null) {
      err += 'Select class to enter mark . </br>';
    }
    if (msub == '' || msub == null) {
      err += 'Select subject to enter mark . </br>';
    }
    if (mexam == '' || mexam == null) {
      err += 'Select exam to enter mark .';
    }
    if (err != '') {
      e.preventDefault();
      $(".wpsp-popup-return-data").html(err);
      $("#SavingModal").css("display", "none");
      $("#WarningModal").css("display", "block");
      $("#WarningModal").addClass("wpsp-popVisible");
    }
  });
  /***************** Mark Entry **********************/
  $('input.numbers').bind('keypress', function(e) {
    return (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) ? false : true;
  });
  $('input.numbers').bind("cut copy paste", function(e) {
    e.preventDefault();
  });
  $('#AddMark_Submit').click(function(e) {
    e.preventDefault();
    var empty = 0;
    $("#AddMarkForm .markbox").each(function() {
      var value = $(this).val();
      if (value == '') {
        empty = 1;
      }
    });
    if (empty < 1) {
      var postData = $('#AddMarkForm').serializeArray();
      postData.push({
        name: 'action',
        value: 'addMark'
      });
      jQuery.ajax({
        method: "POST",
        url: ajax_url,
        data: postData,
        beforeSend: function() {
        },
        success: function(response) {
          if (response == 'success') {
            document.getElementById("AddMarkForm").reset();
            $(".wpsp-popup-return-data").html('Marks Saved Successfully');
            $("#SuccessModal").css("display", "block");
            $("#SavingModal").css("display", "none");
            $("#SuccessModal").addClass("wpsp-popVisible");
            setTimeout(function() {
              $("#viewmarks").click();
            }, 2000);
            $('#AddMark_Submit').attr('disabled', 'disabled');
          } else if (response == 'false') {
            $(".wpsp-popup-return-data").html('Marks Entry failed');
            $("#SavingModal").css("display", "none");
            $("#WarningModal").css("display", "block");
            $("#WarningModal").addClass("wpsp-popVisible");
          } else if (response == 'update') {
            $(".wpsp-popup-return-data").html('Marks Updated Successfully');
            $("#SuccessModal").css("display", "block");
            $("#SavingModal").css("display", "none");
            $("#SuccessModal").addClass("wpsp-popVisible");
            setTimeout(function() {
              $("#viewmarks").click();
            }, 2000);
          } else {
            $(".wpsp-popup-return-data").html(response);
            $("#SavingModal").css("display", "none");
            $("#WarningModal").css("display", "block");
            $("#WarningModal").addClass("wpsp-popVisible");
          }
        },
        complete: function() {
          $('.pnloader').remove();
        }
      });
    } else {
      $(".wpsp-popup-return-data").html('Mark Box Should not be Empty, Mark Absentees as AB.');
      $("#SavingModal").css("display", "none");
      $("#WarningModal").css("display", "block");
      $("#WarningModal").addClass("wpsp-popVisible");
    }
  });
  /***************** Report *****************************/
  $('#rclass_id').change(function() {
    var cid = $(this).val();
    var getStudent = [];
    getStudent.push({
      name: 'action',
      value: 'get_students_list'
    });
    getStudent.push({
      name: 'cid',
      value: cid
    });
    jQuery.post(ajax_url, getStudent, function(students_list) {
      $('#rstud_id').find('option').remove().end();
      var students_json = $.parseJSON(students_list);
      $.each(students_json, function(field, value) {
        $("#rstud_id").append('<option value="' + value.wp_usr_id + '">' + value.full_name + '</option>');
      });
    });
  });
  $('#rstud_id').change(function() {
    $('#report_sub').submit();
  });
});
/* Custom Tab Js */
$('#verticalTab').easyResponsiveTabs({
  type: 'vertical',
  width: 'auto',
  fit: true
  //$.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
});

if ($(window).width() < 991) {   $('#verticalTab').find('.wpsp-resp-tab-active').removeClass('wpsp-resp-tab-active');
  $('#verticalTab').find('.wpsp-resp-tab-content-active').removeClass('wpsp-resp-tab-content-active').css("display", "");
}
if (jQuery(window).width() < 991) {
    if (!$('#verticalTab').find('.wpsp-resp-tab-active').length) {
      $('.wpsp-resp-tabs-list .wpsp-resp-tab-item:first-child').click();
  }
}
jQuery(window).resize(function() {
  if (jQuery(window).width() > 991) {
      if (!$('#verticalTab').find('.wpsp-resp-tab-active').length) {
        $('.wpsp-resp-tabs-list .wpsp-resp-tab-item:first-child').click();
    }
  }
});