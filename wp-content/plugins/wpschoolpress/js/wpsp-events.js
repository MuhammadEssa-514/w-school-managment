function edit_event(e) {
  null == e.start ? $("#sdate").val("") : $("#sdate").val(e.start.format("MM/DD/YYYY")), null == e.end ? $("#edate").val("") : $("#edate").val(e.end.format("MM/DD/YYYY")), null == e.start ? $("#stime").val("") : $("#stime").val(e.start.format("h:mm A")), null == e.end ? $("#etime").val("") : $("#etime").val(e.end.format("h:mm A")), $("#evtitle").val(e.title), $("#evdesc").val(e.description), $("#evid").val(e.id), $("#evcolor").val(e.color), $("#editeventPop").hide(), $("#eventPop").addClass("wpsp-popVisible"), $("body").addClass("wpsp-bodyFixed")
}
$(document).ready(function() {
  $(".stime, .etime").timepicker({
    showInputs: !0,
    showMeridian: !1,
    template: !1
  }), $(".sdate").datepicker({
    autoclose: !0,
    dateFormat: date_format,
    todayHighlight: !0,
    changeMonth: !0,
    changeYear: !0,
    beforeShow: function(e, a) {
      $(document).off("focusin.bs.modal")
    },
    onClose: function() {
      $(document).on("focusin.bs.modal")
    },
    onSelect: function(e) {
      $(".edate").datepicker("option", "minDate", e)
    }
  }), $(".edate").datepicker({
    autoclose: !0,
    dateFormat: date_format,
    todayHighlight: !0,
    changeMonth: !0,
    changeYear: !0,
    beforeShow: function(e, a) {
      $(document).off("focusin.bs.modal")
    },
    onClose: function() {
      $(document).on("focusin.bs.modal")
    },
    onSelect: function(e) {
      $(".sdate").datepicker("option", "maxDate", e)
    }
  }), $("#calendar").fullCalendar({
    header: {
      left: "prev,today,next",
      center: "title",
      right: "month,basicWeek,basicDay"
    },
    firstDay: 1,
    editable: !0,
    selectable: !0,
    selectHelper: !0,
    ignoreTimezone: !0,
    timeFormat: "h(:mm)",
    allDaySlot: !1,
    select: function(e, a) {
      $("#calevent_entry")[0].reset(), $("#calevent_save").prop("disabled", !1), $("#calevent_delete").prop("disabled", "disabled");
      var t = e.format("MM/DD/YYYY"),
        l = moment();
      l = a.subtract(1, "days").format("MM/DD/YYYY");
      $("#sdate").val(t), $("#edate").val(l), $("#eventPop").addClass("wpsp-popVisible"), $("body").addClass("wpsp-bodyFixed")
    },
    eventClick: function(e, a, t) {
      $("#viewEventTitle").html(e.title), null == e.start ? $("#eventStart").html("N/A") : $("#eventStart").html(e.start.format("MM/DD/YYYY h:mm A")), null == e.end ? $("#eventEnd").html("N/A") : $("#eventEnd").html(e.end.format("MM/DD/YYYY h:mm A")), $("#eventDesc").html(e.description), $("#editeventPop").addClass("wpsp-popVisible"), $("body").addClass("wpsp-bodyFixed"), $("#editEvent").click(function() {
        edit_event(e)
      }), $("#deleteEvent").click(function() {
        if (1 == confirm("Are you sure want to delete?")) {
          var a = new Array;
          a.push({
            name: "action",
            value: "deleteEvent"
          }), a.push({
            name: "evid",
            value: e.id
          }), jQuery.post(ajax_url, a, function(e) {
            "success" == e ? ($("#response").html("<div class='alert alert-success'>Event deleted successfully..</div>"), location.reload(!0)) : ($("#response").html("<div class='alert alert-danger'>Action failed please refresh and try..</div>"), location.reload(!0))
          })
        }
      })
    },
    eventDrop: function(e) {
      e.preventDefault()
    },
    eventLimit: !0,
    events: {
      url: ajax_url,
      type: "POST",
      data: {
        action: "listEvent"
      },
      dataType: "JSON",
      error: function() {
        alert("There is an error while fetching events!")
      }
    }
  }), $("#calevent_save").click(function() {
    var e = $("#sdate").val(),
      a = $("#edate").val(),
      t = $("#evtitle").val();
    if ("" == t || "" == e || "" == a) $(".wpsp-popup-return-data").html("Title and dates are mandatory.."), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible");
    else {
      $("#calevent_save").prop("disabled", "disabled");
      var l = $("#calevent_entry").serializeArray();
      $.isNumeric($("#evid").val()) ? l.push({
        name: "action",
        value: "updateEvent"
      }) : l.push({
        name: "action",
        value: "addEvent"
      }), jQuery.post(ajax_url, l, function(e) {
        "success" == e ? ($("#evid").val(""), $(".wpsp-popup-return-data").html("Event details saved successfully.."), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible"), location.reload(!0), $("#basicModal").html(""), $("#eventPop").hide(), setTimeout(function() {
          location.reload(!0)
        }, delay)) : ($(".wpsp-popup-return-data").html("Please try Again.."), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"), $("#calevent_save").prop("disabled", !1))
      })
    }
  })
});
