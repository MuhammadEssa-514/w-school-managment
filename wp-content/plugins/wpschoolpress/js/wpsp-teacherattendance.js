$(document).ready(function() {
  $(document).on("click", ".checkAll", function(e) {
    $(this).prop("checked") && $("input[name=absent\\[\\]]").prop("checked", !1)
  }), $(document).on("click", "input[name=absent\\[\\]]", function(e) {
    $(this).prop("checked") && $(".checkAll").prop("checked", !1)
  }), $(".select_date").datepicker({
    autoclose: !0,
    dateFormat: date_format,
    todayHighlight: !0,
    changeMonth: !0,
    changeYear: !0,
    endDate: "today"
  }), $("#AttendanceEnter").click(function() {
    $("#AddModalContent").html("");
    var e = $("#AttendanceDate").val();
    if ("" == e && $("#AttendanceDate").parent().parent().find("label").addClass("error"), "" != e) {
      var a = [];
      a.push({
        name: "action",
        value: "getTeachersList"
      }, {
        name: "date",
        value: e
      }), $.ajax({
        type: "POST",
        url: ajax_url,
        data: a,
        beforeSend: function() {},
        success: function(e) {
          $(".AttendanceContent").html(e)
        },
        error: function() {
          $(".wpsp-popup-return-data").html("Operation failed.Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
        },
        complete: function() {}
      })
    }
  }), $(document).on("click", "#AttendanceSubmit", function(e) {
    if (e.preventDefault(), $('input[type="checkbox"]:checked').length > 0) {
      var a = $("#AttendanceEntryForm").serializeArray();
      a.push({
        name: "action",
        value: "TeacherAttendanceEntry"
      }), jQuery.post(ajax_url, a, function(e) {
        "success1" == e ? ($(".wpsp-popup-return-data").html("Attendance entered successfully!"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible"), $("#AttendanceEntryForm").trigger("reset"), setTimeout(function() {
          $(".alert").remove(), $("#SuccessModal").css("display", "none"), $("#AttendanceView").click()
        }, 2e3)) : "updated" == e ? ($(".wpsp-popup-return-data").html("Attendance updated successfully!"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible"), setTimeout(function() {
          $(".alert").remove(), $("#SavingModal").modal("hide"), $("#AttendanceView").click()
        }, 1500)) : ($(".wpsp-popup-return-data").html("Operation failed.Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"))
      })
    } else $(".wpsp-popup-return-data").html("If no absent please select Nil at bottom!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
  }), $(document).on("click", ".deleteAttendance", function() {
    if (confirm("Are you want to delete this entry?")) {
      var e = $(this).attr("data-id");
      if ("" == e) $(".wpsp-popup-return-data").html("Attendance information Missing!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible");
      else {
        var a = [];
        a.push({
          name: "action",
          value: "TeacherAttendanceDelete"
        }, {
          name: "aid",
          value: e
        }), $.ajax({
          type: "POST",
          url: ajax_url,
          data: a,
          beforeSend: function() {},
          success: function(e) {
            $(".wpsp-popup-return-data").html("Attendance entry deleted successfully.."), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible")
          },
          error: function() {
            $(".wpsp-popup-return-data").html("Something went wrong. Try after refreshing page.."), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
          },
          complete: function() {
            $(".pnloader").remove()
          }
        })
      }
    }
  }), $("#AttendanceView").click(function() {
    var e = $("#AttendanceDate").val();
    if ("" != e) {
      var a = [];
      a.push({
        name: "action",
        value: "TeacherAttendanceView"
      }, {
        name: "selectedate",
        value: e
      }), $.ajax({
        type: "POST",
        url: ajax_url,
        data: a,
        beforeSend: function() {},
        success: function(e) {
          $(".AttendanceContent").html(e)
        },
        error: function() {
          $(".wpsp-popup-return-data").html("Something went wrong. Try after refreshing page.."), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
        },
        complete: function() {
          $(".pnloader").remove()
        }
      })
    }
  }), $(document).on("click", ".viewAbsentDates", function() {
    var e = $(this).attr("data-id");
    if ($.isNumeric(e)) {
      var a = [];
      a.push({
        name: "action",
        value: "getAbsentDates"
      }, {
        name: "sid",
        value: e
      }), $.ajax({
        type: "POST",
        url: ajax_url,
        data: a,
        beforeSend: function() {},
        success: function(e) {
          $("#ViewModalContent").html(e)
        },
        error: function() {
          $(".wpsp-popup-return-data").html("Something went wrong. Try after refreshing page.."), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
        },
        complete: function() {
          $(".pnloader").remove()
        }
      }), $("#ViewModal").modal("show")
    } else $(".wpsp-popup-return-data").html("Teacher ID Missing.."), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
  }), $("#teacherAttendanceTable").dataTable({
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
    drawCallback: function(e) {
      $(this).closest(".dataTables_wrapper").find(".dataTables_paginate").toggle(this.api().page.info().pages > 1)
    },
    responsive: !0
  })
});
