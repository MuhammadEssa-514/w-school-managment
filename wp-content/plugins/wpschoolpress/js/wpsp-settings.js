$(document).ready(function() {
  $("#wp-end-time").timepicker({
    showInputs: !1,
    showMeridian: !1
  }), $("#timepicker1").timepicker({
    showInputs: !1,
    showMeridian: !1
  }), $("#SettingsSocialForm").submit(function(e) {
    e.preventDefault(), $(".pnloader").remove();
    var a = new FormData,
      s = $("#SettingsSocialForm").serializeArray();
    a.append("action", "GenSettingsocial"), $.each(s, function(e, s) {
      a.append(s.name, s.value)
    }), a.append("data", s), console.log(s), jQuery.ajax({
      type: "POST",
      url: ajax_url,
      data: a,
      cache: !1,
      processData: !1,
      contentType: !1,
      success: function(e) {
        if ("success" == e) {
          var a = "success",
            s = "Information Saved Successfully";
          window.location.reload()
        } else a = "error", s = "" == e ? "Something went wrong" : e;
        $.fn.notify(a, {
          desc: s
        })
      },
      complete: function() {
        $(".pnloader").remove()
      }
    })
  }), $("#Settingslicensing #s_save").click(function(e) {
    e.preventDefault(), $(".pnloader").remove();
    var a = new FormData,
      s = $("#Settingslicensing").serializeArray();
    a.append("action", "GenSettinglicensing"), $.each(s, function(e, s) {
      a.append(s.name, s.value)
    }), a.append("data", s), console.log(s), jQuery.ajax({
      type: "POST",
      url: ajax_url,
      data: a,
      cache: !1,
      processData: !1,
      contentType: !1,
      success: function(e) {
        "success" == e ? ($(".wpsp-popup-return-data").html("Information Saved Successfully"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible"), window.location.reload()) : ($(".wpsp-popup-return-data").html("Something went wrong"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"))
      },
      complete: function() {
        $(".pnloader").remove()
      }
    })
  }), $("#SettingsInfoForm").submit(function(e) {
    e.preventDefault(), $("#overlay").addClass("overlays"), $(".pnloader").remove();
    document.getElementById("displaypicture");
    var a = new FormData,
      s = $("#SettingsInfoForm").serializeArray(),
      i = $("#displaypicture")[0].files[0];
    a.append("action", "GenSetting"), a.append("displaypicture", i), $.each(s, function(e, s) {
      a.append(s.name, s.value)
    }), a.append("data", s), jQuery.ajax({
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
        $("#setting_submit").attr("disabled", !1), "success" == e ? ($(".wpsp-popup-return-data").html("Information Saved Successfully"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible"), window.location.reload()) : ($(".wpsp-popup-return-data").html("Something went wrong"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"))
      },
      complete: function() {
        $(".pnloader").remove(), $("#overlay").removeClass("overlays")
      }
    })
  }), $("#sms_settings_form").submit(function(e) {
    e.preventDefault(), $(".pnloader").remove();
    var a = new FormData,
      s = $("#sms_settings_form").serializeArray();
    a.append("action", "GenSettingsms"), $.each(s, function(e, s) {
      a.append(s.name, s.value)
    }), a.append("data", s), console.log(s), jQuery.ajax({
      type: "POST",
      url: ajax_url,
      data: a,
      cache: !1,
      processData: !1,
      contentType: !1,
      beforeSend: function() {},
      success: function(e) {
        "success" == e ? ($(".wpsp-popup-return-data").html("Teacher Updated successfully !"),
		$("#SuccessModal").css("display", "block"),
		$("#SavingModal").css("display", "none"),
		$("#SuccessModal").addClass("wpsp-popVisible")) : ($(".wpsp-popup-return-data").html("Something went wrong"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"))
      },
      complete: function() {
        $(".pnloader").remove()
      }
    })
  }), $("#AddGradeForm").validate({
    rules: {
      grade_name: {
        required: !0
      },
      grade_point: {
        required: !0
      },
      mark_from: {
        required: !0
      },
      mark_upto: {
        required: !0
      }
    },
    messages: {
      grade_name: "Please Enter Grade Name",
      grade_point: "Please Enter Grade Point",
      mark_from: "Please Enter Mark From",
      mark_upto: "Please Enter Mark Upto"
    },
    submitHandler: function(e) {
      var a = $("#AddGradeForm").serializeArray();
      a.push({
        name: "action",
        value: "manageGrade"
      }), jQuery.ajax({
        method: "POST",
        url: ajax_url,
        data: a,
        beforeSend: function() {
          
		  $("#grade_save").attr("disabled", "disabled")
        },
        success: function(e) {
          if ($("#grade_save").removeAttr("disabled"), $("#AddGradeForm").trigger("reset"), "success" == e) var a = "success",
            s = "Grade Saved Successfully";
          else a = "error", s = "Something went wrong";
          $.fn.notify(a, {
            desc: s,
            autoHide: !0,
            clickToHide: !0
          })
        },
        complete: function() {
          $("#grade_save").removeAttr("disabled"), $(".pnloader").remove(), $("#AddGradeForm").trigger("reset")
        }
      })
    }
  }), $("#displaypicture").change(function() {
    var e = new FileReader;
    e.onload = function(e) {
      $("#image").attr({
        src: e.target.result,
        width: 150,
        height: 150
      }), $(".sch-remove-logo").show(), $(".sch-logo-container").show()
    }, e.readAsDataURL(this.files[0])
  }), $(".sch-remove-logo").click(function(e) {
    $("#sch_logo_control").val(""), $(".sch-logo-container").hide(), $(".sch-remove-logo").hide(), $(".logo-label").html("Upload Logo")
  }), $("#wpsp_grade_list, #wpsp_sub_division_table,#wpsp_class_hours").dataTable({
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
  }), $(".DeleteGrade").click(function() {
    var e = $(this).attr("data-id"),
      a = new Array;
    a.push({
      name: "action",
      value: "manageGrade"
    }, {
      name: "grade_id",
      value: e
    }, {
      name: "actype",
      value: "delete"
    }), jQuery.ajax({
      method: "POST",
      url: ajax_url,
      data: a,
      success: function(e) {
      /*  "success" == e ? (
		$.fn.notify("success", {
          desc: "Grade deleted succesfully!",
          autoHide: !0,
          clickToHide: !0
        }),
		window.location.reload()) : $.fn.notify("error", {
          desc: e,
          autoHide: !0,
          clickToHide: !0
        })*/
		window.location.reload();
      },
      error: function() {
        /*$.fn.notify("error", {
          desc: "Something went wrong",
          autoHide: !0,
          clickToHide: !0
        })*/
      },
      beforeSend: function() {
      
      },
      complete: function() {
        /*$(".pnloader").remove()*/
      }
    })
  }), 
  $("#SubFieldsClass").change(function() {
	  
    $("#SubFieldSubject option:gt(0)").remove();
    var e = new Array,
      a = $(this).val();
    e.push({
      name: "action",
      value: "subjectList"
    }, {
      name: "ClassID",
      value: a
    }), jQuery.ajax({
      method: "POST",
      url: ajax_url,
      data: e,
      success: function(e) {
        var a = $.parseJSON(e),
          s = $("#SubFieldSubject");
        $.each(a.subject, function(e, a) {
          s.append($("<option></option>").attr("value", a.id).text(a.sub_name))
        })
      },
      error: function() {
       /* $.fn.notify("error", {
          desc: "Something went wrong",
          autoHide: !0,
          clickToHide: !0
        })*/
      },
      beforeSend: function() {
       
      },
      complete: function() {
        //PNotify.removeAll()
      }
    })
  }), $("input[type=radio][name=sch_sms_provider]").change(function() {
    var e = this.value;
    "twilio" == e ? ($(".sms_setting_div").hide(), $("#sms_main_" + e).show()) : ($("#sms_main_" + e).hide(), $(".sms_setting_div").show())
  }), $("#SubFieldsForm").validate({
    rules: {
      ClassID: {
          required: (jQuery("input[name='ClassID']").data("is_required")) ? true : false,
      },
      SubjectID: {
        required: (jQuery("input[name='SubjectID']").data("is_required")) ? true : false,
      },
      FieldName: {
        required: (jQuery("input[name='FieldName']").data("is_required")) ? true : false,
      }
    },
    messages: {
      ClassID: "Please Select class name",
      SubjectID: "Please Select Subject Name",
      FieldName: "Please enter Field Name"
    },
    submitHandler: function(e) {
      var a = $("#SubFieldsForm").serializeArray();
      a.push({
        name: "action",
        value: "addSubField"
      }), jQuery.ajax({
        method: "POST",
        url: ajax_url,
        data: a,
        success: function(e) {
          if ("success" == e) {
            $("#SubFieldsForm")[0].reset(), 
			/*$.fn.notify("success", {
              desc: "Fields added succesfully!",
              autoHide: !0,
              clickToHide: !0
            }),*/ $("#AddFieldsModal").html(""), $("#AddFieldsModal").modal("hide");
            setTimeout(function() {
              location.reload(!0)
            }, 1e3), $("#SubFieldsForm .btn-primary").attr("disabled", "disabled")
          } else 
			  /*$.fn.notify("error", {
            desc: e,
            autoHide: !0,
            clickToHide: !0
          });*/
          $("#SubFieldsForm .btn-primary").attr("disabled", !1)
        },
        error: function() {
         /* $.fn.notify("error", {
            desc: "Something went wrong",
            autoHide: !0,
            clickToHide: !0
          })*/
        },
        beforeSend: function() {
        
        },
        complete: function() {
          $(".pnloader").remove()
        }
      })
    }
  }), $(".SFUpdate").click(function() {
    var e = $(this).attr("data-id"),
      a = $("#" + e + "SF").val(),
      s = new Array;
    s.push({
      name: "action",
      value: "updateSubField"
    }, {
      name: "sfid",
      value: e
    }, {
      name: "field",
      value: a
    }), jQuery.ajax({
      method: "POST",
      url: ajax_url,
      data: s,
      success: function(e) {
        if ("success" == e) {
         /* $.fn.notify("success", {
            desc: "Field updated succesfully!",
            autoHide: !0,
            clickToHide: !0
          });*/
          var a = $("#wpsp_locationginal").val() + "/admin.php?page=sch-settings&sc=subField";
          setTimeout(function() {
            window.location.href = a
          }, 1e3)
        } else $(".wpsp-popup-return-data").html("Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      },
      error: function() {
        $(".wpsp-popup-return-data").html("Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      },
      complete: function() {
       /* $(".pnloader").remove()*/
      }
    })
  }), $(document).on("click", "#d_teacher", function(e) {
    var a = $(this).data("id");
    $("#teacherid").val(a), $("#DeleteModal").css("display", "block")
  }), $(document).on("click", ".ClassDeleteBt", function(e) {
    var nn = $('#wps_generate_nonce').val();
    var a = $("#teacherid").val(),
      s = new Array;
    s.push({
      name: "action",
      value: "deleteSubField"
    }, {
      name: "sfid",
      value: a
    },{
      name: "wps_generate_nonce",
      value: nn
    }), jQuery.ajax({
      method: "POST",
      url: ajax_url,
      data: s,
      success: function(e) {
        if ("success" == e) {
          var a = 1e3,
            s = $("#wpsp_locationginal").val() + "/admin.php?page=sch-settings&sc=subField";
          setTimeout(function() {
            window.location.href = s
          }, a);
          $("#DeleteModal").css("display", "none"), ".wpsp-popup-return-data".html("Update successfully!"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible");
          a = 1e3, s = $("#wpsp_locationginal").val() + "/admin.php?page=sch-settings&sc=subField", setTimeout(function() {
            window.location.href = s
          }, a)
        } else $(".wpsp-popup-return-data").html("Operation failed.Something went wrong!");
        $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      },
      error: function() {
        $(".wpsp-popup-return-data").html("Operation failed.Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
      }
    })
  }), $("#displaypicture").change(function() {
    var e = $(this).attr("id");
    ! function(e) {
      if (e.files) {
        var a = new FileReader;
        "displaypicture" == e.id && (a.onload = function(e) {
          $("#img_preview").attr("src", e.target.result).width(112).height(112)
        }), "p_displaypicture" == e.id && (a.onload = function(e) {
          $("#img_preview1").attr("src", e.target.result).width(112).height(112)
        }), a.readAsDataURL(e.files[0])
      }
    }(this), $(".validation-error-" + e).html("");
    var a = document.getElementById(e).files[0].size,
      s = $(this).val();
    a > 3145728 && ($(".validation-error-" + e).html("File Size should be less than 3 MB, Please select another file"), $(this).val(""));
    var i = s.substring(s.lastIndexOf(".") + 1); - 1 == $.inArray(i, ["jpg", "jpeg", "png"]) && ($(".validation-error-" + e).html("Please select either jpg or jpeg, PNG file"), $(this).val(""))
  }), $("#verticalTab").easyResponsiveTabs({
    type: "vertical",
    width: "auto",
    fit: !0
  }), $(window).width() < 991 && ($("#verticalTab").find(".wpsp-resp-tab-active").removeClass("wpsp-resp-tab-active"), $("#verticalTab").find(".wpsp-resp-tab-content-active").removeClass("wpsp-resp-tab-content-active").css("display", "")), jQuery(window).width() < 991 && ($("#verticalTab").find(".wpsp-resp-tab-active").length || $(".wpsp-resp-tabs-list .wpsp-resp-tab-item:first-child").click())
}), jQuery(window).resize(function() {
  jQuery(window).width() > 991 && ($("#verticalTab").find(".wpsp-resp-tab-active").length || $(".wpsp-resp-tabs-list .wpsp-resp-tab-item:first-child").click())
});
