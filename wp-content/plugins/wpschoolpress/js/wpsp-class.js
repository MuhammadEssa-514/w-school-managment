$(document).ready(function() {
  $("input.numbers").bind("keypress", function(a) {
    return 8 == a.which || 0 == a.which || !(a.which < 48 || a.which > 57)
  }), $(".wpsp-start-date").datepicker({
    autoclose: !0,
    dateFormat: date_format,
    todayHighlight: !0,
    changeMonth: !0,
    changeYear: !0,
    startDate: "0m"
  }), $(".wpsp-start-date").on("change", function() {
    console.log($(".wpsp-start-date").val()), $(".wpsp-end-date").datepicker("remove"), $(".wpsp-end-date").datepicker({
      autoclose: !0,
      dateFormat: date_format,
      todayHighlight: !0,
      changeMonth: !0,
      changeYear: !0,
      startDate: $(".wpsp-start-date").val(),
      setStartDate: $(".wpsp-start-date").val()
    }), $(".wpsp-end-date").datepicker("update", $(".wpsp-start-date").val())
  }), $("#class_table").dataTable({
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
  }), $("#ClassAddForm").validate({
    rules: {
      Name: {
        required: (jQuery("input[name='Name']").data("is_required")) ? true : false,
        minlength: 2
      },
      Number: {
        required: (jQuery("input[name='Number']").data("is_required")) ? true : false,
      },
      capacity: {
        required: (jQuery("input[name='capacity']").data("is_required")) ? true : false,
      },
      Sdate: {
        required: (jQuery("input[name='Sdate']").data("is_required")) ? true : false,
      },
      Edate: {
        required: (jQuery("input[name='Edate']").data("is_required")) ? true : false,
      }
    },
    messages: {
      Name: {
        required: "Please enter class name",
        minlength: "Class must consist of at least 2 characters"
      },
      capacity: {
        max: "Class Out of capacity",
        required: "Please enter class capacity"
      },
      Number: "Please enter class number",
      Sdate: "Please enter Start Date",
      Edate: "Please enter End Date"
    },
    submitHandler: function(a) {
      var e = $("#ClassAddForm").serializeArray();
      e.push({
        name: "action",
        value: "AddClass"
      }), $.ajax({
        type: "POST",
        url: ajax_url,
        data: e,
        beforeSend: function() {
          $("#SavingModal").css("display", "block"), $("#c_submit").attr("disabled", "disabled")
        },
        success: function(a) {
          if ("inserted" == a) {
            $(".wpsp-popup-return-data").html("Class created successfully !"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible");
            var e = $("#wpsp_locationginal").val() + "/admin.php?page=sch-class";
            setTimeout(function() {
              window.location.href = e
            }, 1e3);
            $("#ClassAddForm").trigger("reset"), $("#c_submit").attr("disabled", !0)
          } else $(".wpsp-popup-return-data").html(a), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
        },
        complete: function() {}
      })
    }
  }), $("#ClassEditForm").validate({
    rules: {
      Name: {
        required: (jQuery("input[name='Name']").data("is_required")) ? true : false,
        minlength: 2
      },
      capacity: {
        required: (jQuery("input[name='capacity']").data("is_required")) ? true : false,
      },
      ClassTeacherID: {
        required: (jQuery("input[name='ClassTeacherID']").data("is_required")) ? true : false,
      },
      Sdate: {
        required: (jQuery("input[name='Sdate']").data("is_required")) ? true : false,
      },
      Edate: {
        required: (jQuery("input[name='Edate']").data("is_required")) ? true : false,
      }
    },
    messages: {
      Name: {
        required: "Please enter classname",
        minlength: "Class must consist of at least 2 characters"
      },
      capacity: {
        max: "Class Out of capacity"
      }
    },
    submitHandler: function(a) {
      var e = $("#ClassEditForm").serializeArray();
      e.push({
        name: "action",
        value: "UpdateClass"
      }), $.ajax({
        type: "POST",
        url: ajax_url,
        data: e,
        beforeSend: function() {
          $("#SavingModal").css("display", "block"), $("#c_submit").attr("disabled", "disabled")
        },
        success: function(a) {
          if ("updated" == a) {
            $(".wpsp-popup-return-data").html("Class updated successfully !"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible");
            var e = $("#wpsp_locationginal").val() + "/admin.php?page=sch-class";
            setTimeout(function() {
              window.location.href = e
            }, 1e3);
            $("#c_submit").attr("disabled", "disabled")
          } else $(".wpsp-popup-return-data").html(a), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
        },
        complete: function() {}
      })
    }
  }), $(document).on("click", "#d_teacher", function(a) {
    var e = $(this).data("id");
    console.log(e), $("#teacherid").val(e), $("#DeleteModal").css("display", "block")
  }), $(document).on("click", ".ClassDeleteBt", function(a) {
    var nn = $('#wps_generate_nonce').val();
    var e = $("#teacherid").val(),
      s = [];
    s.push({
      name: "action",
      value: "DeleteClass"
    }, {
      name: "cid",
      value: e
    },{
      name: "wps_generate_nonce",
      value: nn
    }), jQuery.post(ajax_url, s, function(a) {
      "success" == a ? location.reload() : ($(".wpsp-popup-return-data").html("Operation failed.Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"))
    })
  })
});
