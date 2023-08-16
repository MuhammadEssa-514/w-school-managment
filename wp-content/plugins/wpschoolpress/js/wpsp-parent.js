$(document).ready(function() {
    //   $("#child_list").multiselect({
    //     columns: 1,
    //     placeholder: "Select Student",
    //     search: !0,
    //     searchOptions: {
    //       default: "Search Student",
    //       showOptGroups: !0
    //     }
    //   });
    $("#child_list").selectpicker();
    var e = [];
    $(".dropdown-menu").click(function(e) {
        e.stopPropagation()
    }), $(".select_date").datepicker({
        autoclose: !0,
        dateFormat: date_format,
        todayHighlight: !0,
        changeMonth: !0,
        changeYear: !0,
        yearRange: "-60:+0",
        beforeShow: function(e, t) {
            $(document).off("focusin.bs.modal")
        },
        onClose: function() {
            $(document).on("focusin.bs.modal")
        }
    }), $("#parent_table").dataTable({
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
    }), $("#ClassID").change(function() {
        $("#ClassForm").submit()
    }), $("#AddParent").on("click", function(e) {
        e.preventDefault(), $("#AddModal").modal("show")
    }), $("#ParentEntryForm").submit(function(e) {
        e.preventDefault()
    }), $("#ParentImportForm").submit(function(e) {
        e.preventDefault()
    }), $("#importcsv").change(function(t) {
        var a = $("input#importcsv").val().split(".").pop().toLowerCase();
        if (-1 == $.inArray(a, ["csv"])) return $.fn.notify("error", {
            desc: "File format must be CSV!"
        }), !1;
        if (null != t.target.files) {
            var n = new FileReader;
            n.onload = function(t) {
                for (var a = t.target.result.split("\n"), n = a[0].split(","), o = 1; o < a.length; o++) {
                    var l = a[o].split(",");
                    if (l.length == n.length) {
                        for (var i = {}, r = 0; r < n.length; r++) i[n[r]] = l[r];
                        e.push(i)
                    }
                }
                var p = new Array;
                for (o = 0; o < n.length; o++) {
                    var s = n[o];
                    p.push(s)
                }
                $.each(p, function(e, t) {
                    $("#user_name").append($("<option>", {
                        value: t,
                        text: t
                    })), $("#user_pass").append($("<option>", {
                        value: t,
                        text: t
                    })), $("#user_email").append($("<option>", {
                        value: t,
                        text: t
                    })), $("#rollno").append($("<option>", {
                        value: t,
                        text: t
                    })), $("#full_name").append($("<option>", {
                        value: t,
                        text: t
                    })), $("#gender").append($("<option>", {
                        value: t,
                        text: t
                    })), $("#address").append($("<option>", {
                        value: t,
                        text: t
                    })), $("#bloodgrp").append($("<option>", {
                        value: t,
                        text: t
                    })), $("#DOB").append($("<option>", {
                        value: t,
                        text: t
                    })), $("#doj").append($("<option>", {
                        value: t,
                        text: t
                    })), $("#phone").append($("<option>", {
                        value: t,
                        text: t
                    })), $("#prof").append($("<option>", {
                        value: t,
                        text: t
                    })), $("#student_id").append($("<option>", {
                        value: t,
                        text: t
                    })), $("#qual").append($("<option>", {
                        value: t,
                        text: t
                    }))
                }), $(".mapsection").show()
            }, n.readAsText(t.target.files.item(0))
        }
        return !1
    }), $("#selectall").click(function() {
        1 == $(this).prop("checked") ? $(".ptrowselect").prop("checked", !0) : $(".ptrowselect").prop("checked", !1)
    }), $("#parent_table").on("click", ".ViewParent", function(e) {
        e.preventDefault();
        var t = [],
            a = $(this).data("id");
        t.push({
            name: "action",
            value: "ParentPublicProfile"
        }, {
            name: "id",
            value: a
        }, {
            name: "button",
            value: 1
        }), jQuery.post(ajax_url, t, function(e) {
            $("#ViewModalContent").html(e), $(this).click()
        })
    }), $("#displaypicture").change(function() {
        var e = $("#displaypicture"),
            t = e[0].files.length,
            a = e[0].files,
            n = 0;
        if (t > 0) {
            for (var o = 0; o < t; o++) n += a[o].size;
            n > 3e6 && (document.getElementById("test").innerHTML = "File size must not be more than 3 MB", $("#displaypicture").val(""))
        }
    })
});