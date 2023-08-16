$(document).ready(function() {
  $("#ExStart").datepicker({
    autoclose: !0,
    dateFormat: date_format,
    todayHighlight: !0,
    changeMonth: !0,
    changeYear: !0,
    startDate: "-0m"
  }), $("#ExStart").on("change", function() {
    $("#ExEnd").datepicker("remove"), $("#ExEnd").datepicker({
      autoclose: !0,
      dateFormat: date_format,
      todayHighlight: !0,
      changeMonth: !0,
      changeYear: !0,
      startDate: $("#ExStart").val(),
      setStartDate: $("#ExStart").val()
    }), $("#ExEnd").datepicker("update", $("#ExStart").val())
  }), $(".select_date").datepicker({
    autoclose: !0,
    dateFormat: date_format,
    todayHighlight: !0,
    changeMonth: !0,
    changeYear: !0,
    beforeShow: function(a, e) {
      $(document).off("focusin.bs.modal")
    },
    onClose: function() {
      $(document).on("focusin.bs.modal")
    }
  }), $("#exam_class_table").dataTable({
    language: {
      paginate: {
        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
      },
      search: "",
      searchPlaceholder: "Search..."
    },
    dom: '<"wpsp-dataTable-top"f>rt<"wpsp-dataTable-bottom"<"wpsp-length-info"li>p<"clear">>',
    order: [],
    columnDefs: [{
      targets: "nosort",
      orderable: !1
    }],
    drawCallback: function(a) {
      $(this).closest(".dataTables_wrapper").find(".dataTables_paginate").toggle(this.api().page.info().pages > 1)
    },
    responsive: !0
  }), $("#ExamEntryForm").validate({
    onkeyup: !1,
    rules: {
      class_name: {
        required: (jQuery("input[name='class_name']").data("is_required")) ? true : false,
      },
      ExName: {
        required: (jQuery("input[name='ExName']").data("is_required")) ? true : false,
        minlength: 2
      },
      ExStart: {
        required: (jQuery("input[name='ExStart']").data("is_required")) ? true : false,
      },
      ExStart: {
        required: (jQuery("input[name='ExStart']").data("is_required")) ? true : false,
      }
    },
    messages: {
      ExamName: {
        required: "Please enter Exam Name",
        minlength: "Exam name must consist of at least 2 characters"
      },
      class_name: {
        required: "Please select class name"
      }
    },
    submitHandler: function(a) {
      var e = $("#ExamEntryForm").serializeArray();
      e.push({
        name: "action",
        value: "AddExam"
      }), $.ajax({
        type: "POST",
        url: ajax_url,
        data: e,
        success: function(a) {
          if ("success" == a) {
            $(".wpsp-popup-return-data").html("Exam Created Successfully !"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible");
            var e = $("#wpsp_locationginal").val() + "/admin.php?page=sch-exams";
            setTimeout(function() {
              window.location.href = e
            }, 1e3);
            $("#ExamEntryForm").trigger("reset"), $("#e_submit").attr("disabled", "disabled")
          } else $(".wpsp-popup-return-data").html(a), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
        }
      })
    }
  }), $("#ExamEditForm").validate({
    onkeyup: !1,
    rules: {
      class_name: {
        required: (jQuery("input[name='class_name']").data("is_required")) ? true : false,
      },
      ExamID: {
        required: (jQuery("input[name='ExamID']").data("is_required")) ? true : false,
        number: !0
      },
      ExName: {
        required: (jQuery("input[name='ExName']").data("is_required")) ? true : false,
        minlength: 2
      },
      ExStart: {
        required: (jQuery("input[name='ExStart']").data("is_required")) ? true : false,
      },
      ExEnd: {
      required: (jQuery("input[name='ExEnd']").data("is_required")) ? true : false,
      }
    },
    messages: {
      SName: {
        required: "Please enter Subject Name",
        minlength: "Subject must consist of at least 2 characters"
      },
      class_name: {
        required: "Please select class name"
      }
    },
    submitHandler: function(a) {
      var e = $("#ExamEditForm").serializeArray();
      e.push({
        name: "action",
        value: "UpdateExam"
      }), $.ajax({
        type: "POST",
        url: ajax_url,
        data: e,
        success: function(a) {
          if ("updated" == a) {
            $(".wpsp-popup-return-data").html("Exam information updated Successfully !"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible");
            var e = $("#wpsp_locationginal").val() + "/admin.php?page=sch-exams";
            setTimeout(function() {
              window.location.href = e
            }, 1e3);
            $("#e_submit").attr("disabled", "disabled")
          } else $(".wpsp-popup-return-data").html(a), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
        }
      })
    }
  }), $(document).on("click", "#d_teacher", function(a) {
    var e = $(this).data("id");
    console.log(e), $("#teacherid").val(e), $("#DeleteModal").css("display", "block")
  }), $(document).on("click", ".ClassDeleteBt", function(a) {
    var nn = $('#wps_generate_nonce').val();
    var e = $("#teacherid").val(),
      t = [];
    t.push({
      name: "action",
      value: "DeleteExam"
    }, {
      name: "eid",
      value: e
    },{
      name: "wps_generate_nonce",
      value: nn
    }), jQuery.post(ajax_url, t, function(a) {
      "deleted" == a ? location.reload() : ($("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"))
    })
  }), $(document).on("change", "#class_name,#edit_class_name", function(a) {
    var e = [];
    $(".action-button").attr("disabled", "disabled"), e.push({
      name: "action",
      value: "subjectList"
    }), e.push({
      name: "ClassID",
      value: $(this).val()
    }), jQuery.post(ajax_url, e, function(a) {
      var e = $.parseJSON(a),
        t = "";
      $.each(e.subject, function(a, e) {
        t += '<input type="checkbox" name="subjectid[]" value="' + e.id + '" class="exam-subjects wpsp-checkbox" id="subject-' + e.id + '"><label for="subject-' + e.id + '" class="wpsp-checkbox-label">' + e.sub_name + "</label>"
      }), $(".exam-class-list").html(t), $(".action-button").removeAttr("disabled"), $(".exam-all-subjects").attr("checked", !1)
    })
  }), $(document).on("click", ".exam-all-subjects", function() {
    1 == $(this).prop("checked") ? $(".exam-subjects").prop("checked", !0) : $(".exam-subjects").prop("checked", !1)
  }), $(document).on("click", ".exam-subjects", function() {
    0 == $(this).prop("checked") && $(".exam-all-subjects").prop("checked", !1)
  })
});
