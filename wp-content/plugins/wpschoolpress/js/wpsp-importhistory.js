$(document).ready(function() {
  $("#import").dataTable({
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
  }), $(document).on("click", ".undoimport", function() {
    var a = $(this).attr("value"),
      e = new Array;
    e.push({
      name: "id",
      value: a
    }, {
      name: "action",
      value: "undoImport"
    }), $.ajax({
      type: "POST",
      url: ajax_url,
      data: e,
      beforeSend: function() {},
      success: function(a) {
        $(".wpsp-popup-return-data").html("Rows removed successfully"), $("#ImportRemove").css("display", "block"), $("#SavingModal").css("display", "none"), $("#ImportRemove").addClass("wpsp-popVisible"), location.reload()
      },
      complete: function() {
        $(".pnloader").remove()
      }
    })
  })
});
