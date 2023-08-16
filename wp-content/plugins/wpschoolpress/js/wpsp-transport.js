$(document).ready(function() {
  $("#transport_table").dataTable({
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
  }), $("#AddNew").click(function() {
    var a = new Array;
    a.push({
      name: "action",
      value: "addTransport"
    }), $.ajax({
      type: "GET",
      url: ajax_url,
      data: a,
      success: function(a) {
        $("#ViewModalContent").html(a), $(this).click()
      },
      complete: function() {
        $(".pnloader").remove(), $(this).click()
      },
      error: function() {
        $(".wpsp-popup-return-data").html("Something went wrong.."), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      }
    })
  }), $(".EditTrans").click(function() {
    var a = $(this).attr("data-id"),
      n = new Array;
    n.push({
      name: "action",
      value: "updateTransport"
    }, {
      name: "id",
      value: a
    }), $.ajax({
      type: "GET",
      url: ajax_url,
      data: n,
      success: function(a) {
        $("#ViewModalContent").html(a)
      },
      complete: function() {
        $(".pnloader").remove(), $(this).click()
      },
      error: function() {
        $(".wpsp-popup-return-data").html("Something went wrong.."), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      }
    })
  }), $(".ViewTrans").click(function() {
    var a = $(this).attr("data-id"),
      n = new Array;
    n.push({
      name: "action",
      value: "viewTransport"
    }, {
      name: "id",
      value: a
    }), $.ajax({
      type: "GET",
      url: ajax_url,
      data: n,
      success: function(a) {
        $("#ViewModalContent").html(a)
      },
      complete: function() {
        $(".pnloader").remove(), $(this).click()
      },
      error: function() {
        $(".wpsp-popup-return-data").html("Something went wrong.."), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      }
    })
  }), $(document).on("click", "#TransSubmit", function(a) {
    a.preventDefault();
    var n = $("#TransEntryForm").serializeArray();
    n.push({
      name: "action",
      value: "addTransport"
    }), $.ajax({
      type: "POST",
      url: ajax_url,
      data: n,
      beforeSend: function() {},
      success: function(a) {
        if ("success" == a) {
          $(".wpsp-popup-return-data").html("Transport details saved successfully."), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible"), $("#TransModalBody").html(""), $("#TransModal").modal("hide");
          setTimeout(function() {
            location.reload(!0)
          }, 1e3)
        } else $(".wpsp-popup-return-data").html(a), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      },
      complete: function() {
        $(".pnloader").remove()
      },
      error: function() {
        $(".wpsp-popup-return-data").html("Somethng went wrong.."), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      }
    })
  }), $(document).on("click", "#TransUpdate", function(a) {
    a.preventDefault();
    var n = $("#TransEditForm").serializeArray();
    n.push({
      name: "action",
      value: "updateTransport"
    }), $.ajax({
      type: "POST",
      url: ajax_url,
      data: n,
      success: function(a) {
        if ("success" == a) {
          $(".wpsp-popup-return-data").html("Transport details saved successfully."), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible"), $("#TransModalBody").html(""), $("#TransModal").modal("hide");
          setTimeout(function() {
            location.reload(!0)
          }, 1e3)
        } else $(".wpsp-popup-return-data").html(a), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      },
      complete: function() {
        $(".pnloader").remove()
      },
      error: function() {
        $(".wpsp-popup-return-data").html("Somethng went wrong.."), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      }
    })
  }), $(document).on("click", "#d_teacher", function(a) {
    var n = $(this).data("id");
    console.log(n), $("#teacherid").val(n), $("#DeleteModal").css("display", "block")
  }), $(document).on("click", ".ClassDeleteBt", function() {
    var nn = $('#wps_generate_nonce').val();
    var a = $("#teacherid").val(),
      n = new Array;
    n.push({
      name: "action",
      value: "deleteTransport"
    }, {
      name: "id",
      value: a
    },{
      name: "wps_generate_nonce",
      value: nn
    }), $.ajax({
      type: "POST",
      url: ajax_url,
      data: n,
      success: function(a) {
        "success" == a ? location.reload() : ($(".wpsp-popup-return-data").html("Somethng went wrong.."), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"))
      },
      complete: function() {
        $(".pnloader").remove()
      },
      error: function() {
        $(".wpsp-popup-return-data").html("Somethng went wrong.."), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      }
    })
  })
});
