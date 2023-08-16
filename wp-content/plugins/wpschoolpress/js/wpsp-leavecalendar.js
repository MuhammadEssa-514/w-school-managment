
$(document).ready(function() {
  $("#addLeaveDays").click(function() {
    $("#addLeaveDaysBody").toggle()
  }), $(".leaveAdd").click(function() {
    var a = $(this).attr("data-id");
    var nn = $(this).attr("data-non");
    $("#leaveModalHeader").html("Add Leave Date"), $("#ViewModalContent").html('<form action="" id="addLeaveDateForm" method="post"><input type="hidden" name="noncee" value="'+ nn +'"/><div class="wpsp-row"><div class="wpsp-col-xs-12"><div class="wpsp-card"><div class="wpsp-panel-heading"><h3 class="wpsp-panel-title">Add Leave Date</h3></div><div class="wpsp-card-body"><div class="wpsp-row"><div class="wpsp-col-md-6"><div class="wpsp-form-group"> <label class="wpsp-label" for="from">From <span class="wpsp-required">*</span></label><input type="text" name="spls" class="wpsp-form-control spls select_date"></div></div><div class="wpsp-col-md-6"><div class="wpsp-form-group"><label class="wpsp-label" for="from">To <span class="wpsp-required">*</span></label><input type="text" name="sple" class="wpsp-form-control sple select_date"></div></div><div class="wpsp-col-md-12"><div class="wpsp-form-group"><label class="wpsp-label" for="from">Reason</label><input type="text" name="splr" class="wpsp-form-control"></div></div><div class="wpsp-col-xs-12"><input type="hidden" name="ClassID" value="' + a + '"><input type="submit" class="wpsp-btn wpsp-btn-success" id="addLeaveDateSubmit" value="Submit"> </div></div></form>'), $(this).click()
  }), $("#wpsp_leave_days").dataTable({
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
  }), $(".leaveView").click(function() {
    $("#leaveModalHeader").html("Leave Dates"), $("#ViewModalContent").html("");
    var a = $(this).attr("data-id"),
      e = [];
    e.push({
      name: "action",
      value: "getLeaveDays"
    }, {
      name: "cid",
      value: a
    }), $.ajax({
      type: "POST",
      url: ajax_url,
      data: e,
      success: function(a) {
        $("#ViewModalContent").html(a), $(this).click()
      },
      error: function() {
        $(".wpsp-popup-return-data").html("Operation failed.Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      }
    })
  }), $(".leaveDelete").click(function() {
    var a = $(this).attr("data-id");
    $.isNumeric(a) ? $("#leaveModalBody").html("<h4>Are you sure want to delete all dates?</h4><div class='pull-right'><div class='btn btn-default' data-dismiss='modal' id='AllDeleteCancel'>Cancel</div>&nbsp; <div class='btn btn-danger' data-id='" + a + "' id='AllDeleteConfirm'>Confirm</div></div><div style='clear:both'></div>") : $("#leaveModalBody").html("Class id missing can't delete. Please report it to support for deletion"), $("#leaveModalHeader").html("Delete all date"), $("#leaveModal").modal("show")
  }), $(document).on("click", ".dateDelete", function() {
    var a = $(this).attr("data-id");
    $.isNumeric(a) ? $("#leaveModalBody").html("<h4>Are you sure want to delete this dates?</h4><div class='pull-right'><div class='btn btn-default' data-dismiss='modal' id='DateDeleteCancel'>Cancel</div>&nbsp; <div class='btn btn-danger' data-id='" + a + "' id='DateDeleteConfirm'>Confirm</div></div><div style='clear:both'></div>") : $("#leaveModalBody").html("Class id missing can't delete. Please report it to support for deletion"), $("#leaveModalHeader").html("Delete all date"), $("#leaveModal").modal("show")
  }), $(document).on("click", "#d_teacher", function(a) {
    var e = $(this).data("id");
    console.log(e), $("#teacherid").val(e), $("#DeleteModal").css("display", "block")
  }), $(document).on("click", ".ClassDeleteBt", function() {
    var nn = $('#wps_generate_nonce').val();
    var a = $("#teacherid").val(),
      e = [];
    e.push({
      name: "action",
      value: "deleteAllLeaves"
    }, {
      name: "cid",
      value: a
    },{
      name: "wps_generate_nonce",
      value: nn
    }), $.ajax({
      type: "POST",
      url: ajax_url,
      data: e,
      success: function(a) {
        "success" == a ? location.reload() : ($(".wpsp-popup-return-data").html("Operation failed.Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"))
      }
    })
  }), $(document).on("focus", ".spls", function() {
    $(".spls").datepicker({
      autoclose: !0,
      dateFormat: date_format,
      todayHighlight: !0,
      changeMonth: !0,
      changeYear: !0,
      minDate: "0d",
      beforeShow: function(a, e) {
        $(document).off("focusin.bs.modal")
      },
      onClose: function() {
        $(document).on("focusin.bs.modal")
      }
    })
  }), $(document).on("focus", ".sple", function() {
    $(".sple").datepicker({
      autoclose: !0,
      dateFormat: date_format,
      todayHighlight: !0,
      changeMonth: !0,
      changeYear: !0,
      minDate: "0d",
      beforeShow: function(a, e) {
        $(document).off("focusin.bs.modal")
      },
      onClose: function() {
        $(document).on("focusin.bs.modal")
      }
    })
  }), $(document).on("submit", "#addLeaveDateForm", function(a) {
    a.preventDefault();
    var e = $(this).serializeArray();
    e.push({
      name: "action",
      value: "addLeaveDay"
    }), $.ajax({
      type: "POST",
      url: ajax_url,
      data: e,
      success: function(a) {
        "success" == a ? ($(".wpsp-popup-return-data").html("Dates added Successfully"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible"), setTimeout(function() {
          location.reload()
        }, 2e3)) : ($(".wpsp-popup-return-data").html(a), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")), $("#leaveModal").modal("hide")
      },
      error: function() {
        $(".wpsp-popup-return-data").html("Operation failed.Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      }
    })
  }), $(document).on("click", "#DateDeleteConfirm", function() {
    var a = $(this).attr("data-id"),
      e = [];
    e.push({
      name: "action",
      value: "deleteAllLeaves"
    }, {
      name: "lid",
      value: a
    }), $.ajax({
      type: "POST",
      url: ajax_url,
      data: e,
      success: function(a) {
        "success" == a ? ($(".wpsp-popup-return-data").html("Dates added Successfully"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible")) : ($(".wpsp-popup-return-data").html(a), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")), $("#leaveModal").modal("hide")
      },
      error: function() {
        $(".wpsp-popup-return-data").html("Operation failed.Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      }
    })
  }), $("#teacher_table").on("click", ".wpsp-popclick", function() {
    var a = $(this).attr("data-pop");
    $("#" + a).addClass("wpsp-popVisible"), $("body").addClass("wpsp-bodyFixed")
  }), $("#ClassID").change(function() {
    var a = $(this).val(),
      e = [];
    e.push({
      name: "action",
      value: "getClassYear"
    }, {
      name: "cid",
      value: a
    }), $.ajax({
      type: "POST",
      url: ajax_url,
      data: e,
      success: function(a) {
        try {
          var e = $.parseJSON(a);
          $("#CSDate").val(e.c_sdate), $("#CEDate").val(e.c_edate)
        } catch (a) {}
      },
      error: function() {
        $(".wpsp-popup-return-data").html("Operation failed.Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      }
    })
  })
});
