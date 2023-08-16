$(document).ready(function() {
  $("#Doj").datepicker({
    autoclose: !0,
    dateFormat: date_format,
    todayHighlight: !0,
    changeMonth: !0,
    changeYear: !0,
    yearRange: "-50:+0",
    maxDate: 0
  }), $("#Dob").datepicker({
    autoclose: !0,
    dateFormat: date_format,
    todayHighlight: !0,
    changeMonth: !0,
    changeYear: !0,
    maxDate: 0,
    yearRange: "-50:+0",
    beforeShow: function(e, a) {
      $(document).off("focusin.bs.modal")
    },
    onClose: function() {
      $(document).on("focusin.bs.modal")
    },
    onSelect: function(e) {
      $(this).valid()
    }
  }), $("#displaypicture").change(function() {
    var e = $(this).attr("id"),
      a = document.getElementById(e).files[0].size,
      s = $(this).val();
    a > 3145728 && ($("#test").html("File Size should be less than 3 MB, Please select another file"), $(this).val(""));
    var t = s.substring(s.lastIndexOf(".") + 1); - 1 == $.inArray(t, ["jpg", "jpeg"]) && ($("#test").html("Please select either jpg or jpeg file"), $(this).val("")),
      function(e) {
        if (e.files) {
          var a = new FileReader;
          a.onload = function(e) {
            $("#img_preview").attr("src", e.target.result).width(112).height(112)
          }, a.readAsDataURL(e.files[0])
        }
      }(this)
  }), $("#StudentEditForm").validate({
    rules: {
      s_fname: "required",
      s_address: "required",
      s_lname: "required",
      s_zipcode: "required",
      s_rollno: "required",
      s_zipcode: {
        required: !0,
        number: !0
      },
      s_pzipcode: {
        required: !0,
        number: !0
      }
    },
    messages: {
      s_fname: "Please enter first Name",
      s_address: "Please enter current address",
      s_lname: "Please enter last Name",
      s_rollno: "Please enter Roll Number"
    },
    submitHandler: function(e) {
      document.getElementById("StudentEditForm");
      var a = new FormData,
        s = $("#StudentEditForm").serializeArray(),
        t = $("#displaypicture")[0].files[0];
      a.append("action", "UpdateStudent"), a.append("displaypicture", t), $.each(s, function(e, s) {
        a.append(s.name, s.value)
      }), a.append("data", s), $.ajax({
        type: "POST",
        url: ajax_url,
        data: a,
        cache: !1,
        processData: !1,
        contentType: !1,
        beforeSend: function() {
          $("#SavingModal").css("display", "block"), $("#studentform").attr("disabled", "disabled")
        },
        success: function(e) {
          if ($("#studentform").removeAttr("disabled"), "success0" == e) {
            $(".wpsp-popup-return-data").html("Student update successfully !"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible");
            var a = $("#wpsp_locationginal").val() + "admin.php?page=sch-editprofile";
            setTimeout(function() {
              window.location.href = a
            }, 1e3);
            $("#StudentEntryForm").trigger("reset"), $("#studentform").attr("disabled", "disabled")
          } else $(".wpsp-popup-return-data").html(e), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
        },
        error: function() {
          $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"), $("#teacherform").removeAttr("disabled")
        },
        complete: function() {
          $(".pnloader").remove(), $("#studentform").removeAttr("disabled")
        }
      })
    }
  }), $("#ParentEditForm").validate({
    submitHandler: function(e) {
      document.getElementById("ParentEditForm");
      var a = new FormData,
        s = $("#ParentEditForm").serializeArray(),
        t = $("#displaypicture")[0].files[0];
      a.append("action", "UpdateStudent"), a.append("displaypicture", t), $.each(s, function(e, s) {
        a.append(s.name, s.value)
      }), a.append("data", s), console.log(s), $.ajax({
        type: "POST",
        url: ajax_url,
        data: a,
        cache: !1,
        processData: !1,
        contentType: !1,
        beforeSend: function() {
          $("#SavingModal").css("display", "block"), $("#parentform").attr("disabled", "disabled")
        },
        success: function(e) {
          if ($("#parentform").removeAttr("disabled"), "success0" == e) {
            $(".wpsp-popup-return-data").html("Parent updated successfully !"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible");
            var a = $("#wpsp_locationginal").val() + "admin.php?page=sch-editprofile";
            setTimeout(function() {
              window.location.href = a
            }, 1e3);
            $("#parentform").attr("disabled", "disabled")
          } else $(".wpsp-popup-return-data").html(e), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible")
        },
        error: function() {
          $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"), $("#parentform").removeAttr("disabled")
        },
        complete: function() {
          $(".pnloader").remove(), $("#parentform").removeAttr("disabled")
        }
      })
    }
  }), $("#StudentEditForm").submit(function(e) {
    e.preventDefault()
  }), $("#TeacherEditForm").validate({
    rules: {
      firstname: "required",
      Address: "required",
      lastname: "required",
      Username: {
        required: !0,
        minlength: 5
      },
      Password: {
        required: !0,
        minlength: 4
      },
      ConfirmPassword: {
        required: !0,
        minlength: 4,
        equalTo: "#Password"
      },
      Email: {
        required: !0,
        email: !0
      },
      Phone: {
        number: !0,
        minlength: 7
      },
      zipcode: {
        required: !0,
        number: !0
      },
      whours: "required"
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
      Confirm_password: {
        required: "Please provide a password",
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
            $(".wpsp-popup-return-data").html("Teacher updated successfully !"), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible");
            var a = $("#wpsp_locationginal").val() + "/admin.php?page=sch-editprofile";
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
  })
});
