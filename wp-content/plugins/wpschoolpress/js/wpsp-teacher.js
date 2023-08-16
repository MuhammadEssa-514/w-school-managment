$(document).ready(function() {
    $("#teacher_table").dataTable({
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
    });
    $("#wpsp_leave_days").on("click", ".wpsp-popclick", function() {
        var e = $(this).attr("data-pop");
        $("#" + e).addClass("wpsp-popVisible"), $("body").addClass("wpsp-bodyFixed")
    }), $(".dropdown-menu").click(function(e) {
        e.stopPropagation()
    }), $("#Dob").datepicker({
        autoclose: !0,
        dateFormat: date_format,
        todayHighlight: !0,
        changeMonth: !0,
        changeYear: !0,
        maxDate: 0,
        yearRange: "-50:+0"
    }), $("#Doj").datepicker({
        autoclose: !0,
        dateFormat: date_format,
        todayHighlight: !0,
        changeMonth: !0,
        changeYear: !0,
        maxDate: 0,
        beforeShow: function(e, a) {
            $(document).off("focusin.bs.modal")
        },
        onClose: function() {
            $(document).on("focusin.bs.modal")
        },
        onSelect: function(e) {
            $(".Dol").datepicker("option", "minDate", e)
        }
    }), $("#Dol").datepicker({
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
            $(".Doj").datepicker("option", "maxDate", e)
        }
    }), $("#ClassID").change(function() {
        $("#TeacherClass").submit()
    }), $("#displaypicture").change(function() {
        var e = $(this).attr("id"),
            a = document.getElementById(e).files[0].size,
            s = $(this).val();
        a > 3145728 && ($("#test").html("File Size should be less than 3 MB, Please select another file"), $(this).val("")), a < 3145728 && $("#test").css("display", "none");
        var t = s.substring(s.lastIndexOf(".") + 1); - 1 == $.inArray(t, ["jpg", "jpeg"]) && ($("#test").html("Please select either jpg or jpeg file"), $(this).val("")),
            function(e) {
                if (e.files) {
                    var a = new FileReader;
                    a.onload = function(e) {
                        $("#img_preview_teacher").attr("src", e.target.result).width(112).height(112)
                    }, a.readAsDataURL(e.files[0])
                }
            }(this)
    }), $("#TeacherEditForm").validate({
        rules: {
            firstname: {
                required: (jQuery("input[name='firstname']").data("is_required")) ? true : false,
            },
            Address: {
                required: (jQuery("input[name='Address']").data("is_required")) ? true : false,
            },
            lastname: {
                required: (jQuery("input[name='lastname']").data("is_required")) ? true : false,
            },
            Username: {
                required: (jQuery("input[name='Username']").data("is_required")) ? true : false,
                minlength: 5
            },
            Password: {
                required: (jQuery("input[name='Password']").data("is_required")) ? true : false,
                minlength: 4
            },
            ConfirmPassword: {
                required: (jQuery("input[name='ConfirmPassword']").data("is_required")) ? true : false,
                minlength: 4,
                equalTo: "#Password"
            },
            Email: {
                required: (jQuery("input[name='Email']").data("is_required")) ? true : false,
                email: !0
            },
            //   Phone: {
            //     required: (jQuery("input[name='Phone']").data("is_required")) ? true : false,
            //     minlength: 7
            //   },
            //   zipcode: {
            //     required: (jQuery("input[name='zipcode']").data("is_required")) ? true : false,
            //     number: !0
            //   },
            //   whours:  {
            //     required: (jQuery("input[name='whours']").data("is_required")) ? true : false,
            //   },
        },
        messages: {
            firstname: "Please Enter Teacher Name",
            Address: "Please Enter current Address",
            lastname: "Please Enter Last Name",
            Username: {
                required: "Please enter a username",
                minlength: "Username must consist of at least 5 characters"
            },
            Password: {
                required: "Please provide a password",
                minlength: "Password must be at least 5 characters long"
            },
            ConfirmPassword: {
                required: "Please provide a ConfirmPassword",
                minlength: "Password must be at least 5 characters long",
                equalTo: "Please enter the same password as above"
            },
            Email: "Please enter a valid email address"
        },
        submitHandler: function(e) {
            document.getElementById("TeacherEditForm");
            var a = new FormData,
                s = $("#TeacherEditForm").serializeArray(),
                t = $("#displaypicture")[0].files[0];
            a.append("action", "UpdateTeacher"), a.append("displaypicture", t), $.each(s, function(e, s) {
                a.append(s.name, s.value)
            }), a.append("data", s), $.ajax({
                type: "POST",
                url: ajax_url,
                data: a,
                cache: !1,
                processData: !1,
                contentType: !1,
                beforeSend: function() {
                    $("#u_teacher").attr("disabled", "disabled"), $("#SavingModal").css("display", "block")
                },
                success: function(e) {
                    if ("success0" == e) {
                        $(".wpsp-popup-return-data").html("Teacher Updated successfully !"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible");
                        var a = $("#wpsp_locationginal").val() + "/admin.php?page=sch-teacher";
                        setTimeout(function() {
                            window.location.href = a
                        }, 1e3);
                        $("#TeacherEditForm").trigger("reset"), $("#u_teacher").attr("disabled", "disabled")
                    } else $(".wpsp-popup-return-data").html(e), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
                },
                error: function() {
                    $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
                },
                complete: function() {
                    $(".pnloader").remove()
                }
            })
        }
    }), $("#TeacherEntryForm").validate({
        rules: {
            firstname: {
                required: (jQuery("input[name='firstname']").data("is_required")) ? true : false,
            },
            Address: {
                required: (jQuery("input[name='Address']").data("is_required")) ? true : false,
            },
            lastname: {
                required: (jQuery("input[name='lastname']").data("is_required")) ? true : false,
            },
            Username: {
                required: (jQuery("input[name='Username']").data("is_required")) ? true : false,
                minlength: 5
            },
            Password: {
                required: (jQuery("input[name='Password']").data("is_required")) ? true : false,
                minlength: 4
            },
            ConfirmPassword: {
                required: (jQuery("input[name='ConfirmPassword']").data("is_required")) ? true : false,
                minlength: 4,
                equalTo: "#Password"
            },
            Email: {
                required: (jQuery("input[name='Email']").data("is_required")) ? true : false,
                email: !0
            },
            // Phone: {
            //     required: (jQuery("input[name='ConfirmPassword']").data("is_required")) ? true : false,
            //     minlength: 7
            // },
            // zipcode: {
            //     required: (jQuery("input[name='ConfirmPassword']").data("is_required")) ? true : false,
            //     number: !0
            // },
            // whours: {
            //     required: (jQuery("input[name='ConfirmPassword']").data("is_required")) ? true : false,
            // }
        },
        messages: {
            firstname: "Please Enter Teacher Name",
            Address: "Please Enter current Address",
            lastname: "Please Enter Last Name",
            Username: {
                required: "Please enter a username",
                minlength: "Username must consist of at least 5 characters"
            },
            Password: {
                required: "Please provide a password",
                minlength: "Password must be at least 5 characters long"
            },
            ConfirmPassword: {
                required: "Please provide a ConfirmPassword",
                minlength: "Password must be at least 5 characters long",
                equalTo: "Please enter the same password as above"
            },
            Email: "Please enter a valid email address"
        },
        submitHandler: function(e) {
            var a = new FormData,
                s = $("#TeacherEntryForm").serializeArray(),
                t = $("#displaypicture")[0].files[0];
            a.append("displaypicture", t), a.append("action", "AddTeacher"), $.each(s, function(e, s) {
                a.append(s.name, s.value)
            }), a.append("data", s), $.ajax({
                type: "POST",
                url: ajax_url,
                data: a,
                cache: !1,
                processData: !1,
                contentType: !1,
                beforeSend: function() {
                    $("#SavingModal").css("display", "block"), $("#teacherform").attr("disabled", "disabled")
                },
                success: function(e) {
                    if ($("#teacherform").removeAttr("disabled"), "success" == e) {
                        $(".wpsp-popup-return-data").html("Teacher added successfully !"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible");
                        var a = $("#wpsp_locationginal").val() + "/admin.php?page=sch-teacher";
                        setTimeout(function() {
                            window.location.href = a
                        }, 1e3)
                    } else $(".wpsp-popup-return-data").html(e), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
                },
                error: function() {
                    $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"), $("#teacherform").removeAttr("disabled")
                },
                complete: function() {
                    $(".pnloader").remove(), $("#teacherform").removeAttr("disabled")
                }
            })
        }
    }), $(document).on("click", ".ViewTeacher", function(e) {
        e.preventDefault();
        var a = [],
            s = $(this).data("id");
        a.push({
            name: "action",
            value: "TeacherPublicProfile"
        }, {
            name: "id",
            value: s
        }), jQuery.post(ajax_url, a, function(e) {
            $("#ViewModalContent").html(e), $(this).click()
        })
    }), $("#AddTeacher").on("click", function(e) {
        e.preventDefault(), $("#AddModal").modal("show")
    }), $("#TeacherEntryForm").submit(function(e) {
        e.preventDefault()
    }), $("#TeacherImportForm").submit(function(e) {
        e.preventDefault()
    }), $("#selectall").click(function() {
        1 == $(this).prop("checked") ? $(".tcrowselect").prop("checked", !0) : $(".tcrowselect").prop("checked", !1)
    }), $(".tcrowselect").click(function() {
        1 != $(this).prop("checked") && $("#selectall").prop("checked", !1)
    }), $(document).on("click", "#d_teacher", function(e) {
        var a = $(this).data("id");
        $("#teacherid").val(a), $("#DeleteModal").css("display", "block")
    }), $(document).on("click", ".ClassDeleteBt", function(e) {
        // var a = $("#teacherid").val(),
        //     s = [];
        // s.push({
        //     name: "action",
        //     value: "DeleteTeacher"
        // }, {
        //     name: "tid",
        //     value: a
        // }), jQuery.post(ajax_url, s, function(e) {
        //     "success" == e ? location.reload() : ($(".wpsp-popup-return-data").html("Operation failed.Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"))
        // })

        $("#DeleteModal").css("display", "none");
        $("#SavingModal").css("display", "block");
        $("#SavingModal").addClass("wpsp-popVisible");
        $(".wpsp-saving-text").html('Deleting user..');
        // var singledata = $("#DeleteModal").attr("data-single-delete");
        // if (singledata == 'true') {
            var a = $("#teacherid").val();
                s = [];
            s.push({
                name: "action",
                value: "DeleteTeacher"
            }, {
                name: "tid",
                value: a
            }), jQuery.post(ajax_url, s, function(e) {
                if ("success" == e) {
                    $(".wpsp-success-text").html("Teacher deleted successfully !"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible"), location.reload()
                } else {
                    $(".wpsp-popup-return-data").html("Operation failed.Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
                }
            })
        // } else {

        // }
    }), $("#bulkaction").change(function() {
        if ("bulkUsersDelete" == $(this).val()) {
            var e = $('input[name^="UID"]').map(function() {
                if (1 == $(this).prop("checked")) return this.value
            }).get();
            if (0 == e.length) return $(".wpsp-popup-return-data").html("No user selected!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"), !1;
            $("#DeleteModal").css("display", "block"), $(document).on("click", ".ClassDeleteBt", function(a) {
                var s = new Array;
                s.push({
                    name: "action",
                    value: "bulkDelete"
                }), s.push({
                    name: "UID",
                    value: e
                }), s.push({
                    name: "type",
                    value: "teacher"
                }), jQuery.post(ajax_url, s, function(e) {
                    // "success" == e ? location.reload() : ($(".wpsp-popup-return-data").html("Operation failed.Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"))
                    if ("success" == e) {
                        $(".wpsp-success-text").html("Teacher deleted successfully!"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible"), location.reload()
                    } else {
                        ($(".wpsp-popup-return-data").html("Operation failed.Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"))
                    }
                })
            })
        }
    }), $("#selectall").click(function() {
        1 == $(this).prop("checked") ? $(".tcrowselect").prop("checked", !0) : $(".tcrowselect").prop("checked", !1)
    })
});