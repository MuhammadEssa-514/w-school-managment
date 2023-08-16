function Popup(e) {
  var a = window.open("", "Timetable Print", "height=400,width=600");
  return a.document.write(e), a.document.close(), a.focus(), a.print(), a.close(), !0
}
$(document).ready(function() {
  $("#sessions_template").change(function() {
    "new" == $(this).val() ? ($("#enter_sessions").show(), $("#select_template").hide()) : ($("#enter_sessions").hide(), $("#select_template").show())
  }), $("#deleteTimetable").click(function() {
    var e = $(this).attr("data-id");
    if (1 == confirm("Are you sure want to delete class Timetable?")) {
      var a = [];
      a.push({
        name: "cid",
        value: e
      }), a.push({
        name: "action",
        value: "deletTimetable"
      }), jQuery.post(ajax_url, a, function(e) {
        "deleted" == e && $("#TimetableContainer").html("<div class='alert alert-info'>Class Timetable deleted Successfully..</div>")
      })
    }
  }), $(document).on("click", ".daleteid", function() {
    var e = $(this),
      a = $(this).attr("data-id"),
      s = $(this).attr("data-rowid");
    if (1 == confirm("Are you sure want to delete this slot?")) {
      var t = [];
      t.push({
        name: "cid",
        value: a
      }), t.push({
        name: "rid",
        value: s
      }), t.push({
        name: "action",
        value: "deletsloat"
      }), jQuery.post(ajax_url, t, function(a) {
        console.log(a), console.log(e), "deleted" == a && e.parent().remove()
      })
    }
  }), $("#timetable_form").validate({
    rules: {
      noh: {
        required: !0
      },
      sessions_template: {
        required: !0
      },
      wpsp_class_name: {
        required: !0
      }
    },
    messages: {
      noh: {
        required: "Please enter number of sessions"
      },
      sessions_template: {
        required: "Please enter number of sessions"
      },
      wpsp_class_name: {
        required: "Please select class"
      }
    }
  }), $(".item").draggable({
    revert: !0,
    scroll: !0,
    proxy: "clone"
  }), $(".drop").droppable({
    accept: ".item",
    onDragEnter: function() {
      $(this).addClass("over")
    },
    onDragLeave: function() {
      $(this).removeClass("over")
    },
    onDrop: function(e, a) {
      if ($(this).removeClass("over"), $(a).hasClass("assigned")) $(this).append(a);
      else {
        var s = $(this).data("sessionid"),
          t = $(this).closest("tr").attr("id"),
          i = $(a).clone().removeClass("item").addClass("wpsp-assigned-item assigned item1");
        i.append('<a href="javascript:void(0)" class="daleteid wpsp-tt-delete-icon" data-id="' + s + '" data-rowid="' + t + '"  ></a>');
        $(this).empty().append(i), i.draggable({
          revert: !0
        })
      }
      $("#ajax_response").html("<p class='wpsp-bg-green'>Saving..</p>");
      var l = $("#class_id").val(),
        n = $(this).attr("tid"),
        r = $(this).data("sessionid"),
        o = $(a).attr("id"),
        d = $(this).closest("tr").attr("id"),
        p = $("#datavalue").attr("data-sessionid"),
        c = $("#datavalue").attr("data-rowid"),
        u = [];
      u.push({
        name: "cid",
        value: l
      }), u.push({
        name: "sessionid",
        value: r
      }), u.push({
        name: "tid",
        value: n
      }), u.push({
        name: "sid",
        value: o
      }), u.push({
        name: "day",
        value: d
      }), u.push({
        name: "deletesid",
        value: p
      }), u.push({
        name: "deleterid",
        value: c
      }), u.push({
        name: "action",
        value: "save_timetable"
      }), jQuery.post(ajax_url, u, function(e) {
        var a = e.split(","),
          s = a.length;
        if (2 == s) var t = a[0],
          i = a[1];
        else i = e;
        "true" == i || "updated" == i ? (2 == s ? $("#ajax_response_exist").html("<p class='wpsp-bg-yellow'> This Teacher also assigned to class </p>" + t) : $("#ajax_response_exist").html(""), $("#ajax_response").html("<p class='wpsp-bg-green'>Saved..</p>")) : $("#ajax_response").html("<p class='wpsp-bg-red'> Not Saved..</p>")
      })
    }
  }), $(".removesubject").droppable({
    accept: ".assigned",
    onDragEnter: function(e, a) {
      $(a).addClass("trash")
    },
    onDragLeave: function(e, a) {
      $(a).removeClass("trash")
    },
    onDrop: function(e, a) {
      $(a).remove()
    }
  }), $("#print_timetable").click(function() {
    var e = document.getElementById("timetable_table");
    newWin = window.open("", "Timetable Print"), newWin.document.write("<style>table{border-collapse: collapse;}table,th,td{border: 1px solid black;}td.break{border:0;}tr.break{border:1px solid #000;}</style>"), newWin.document.write(e.outerHTML), newWin.print(), newWin.close()
  }), $(".daytype").change(function() {
    0 == this.value ? ($(".dayval").show(), $(".daynam").hide()) : ($(".daynam").show(), $(".dayval").hide())
  }), $("#ClassID").change(function() {
    $("#TimetableClass").submit()
  }), $(".wp-delete-timetable").click(function() {
    if (1 == confirm("Are you sure want to delete class Timetable?")) {
      var e = $(this).data("id"),
        a = [];
      a.push({
        name: "action",
        value: "deletTimetable"
      }, {
        name: "cid",
        value: e
      }), $.ajax({
        type: "POST",
        url: ajax_url,
        data: a,
        beforeSend: function() {},
        success: function(e) {
          "deleted" == e ? ($(".wpsp-popup-return-data").html("Time Table Deleted Successfully"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible")) : ($(".wpsp-popup-return-data").html("Try Again Later"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"))
        },
        complete: function() {
          $(".pnloader").remove()
        }
      })
    }
  }), $("#wpsp-dd-tt-table").dataTable({
    paging: !1,
    ordering: !1,
    searching: !1,
    info: !1
  }), $("#timetable_table").dataTable({
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
}), $("#verticalTab").easyResponsiveTabs({
  type: "vertical",
  width: "auto",
  fit: !0
}), $(window).width() < 991 && ($("#verticalTab").find(".wpsp-resp-tab-active").removeClass("wpsp-resp-tab-active"), $("#verticalTab").find(".wpsp-resp-tab-content-active").removeClass("wpsp-resp-tab-content-active").css("display", "")), jQuery(window).width() < 991 && ($("#verticalTab").find(".wpsp-resp-tab-active").length || $(".wpsp-resp-tabs-list .wpsp-resp-tab-item:first-child").click()), jQuery(window).resize(function() {
  jQuery(window).width() > 991 && ($("#verticalTab").find(".wpsp-resp-tab-active").length || $(".wpsp-resp-tabs-list .wpsp-resp-tab-item:first-child").click())
});
