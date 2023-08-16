$(document).ready(function() {
  $.validator.addMethod("notEqualTo", function(a, e, s) {
    return this.optional(e) || a != $(s).val()
  }, "New Password is not same as old password"), $("#changepassword").validate({
    onkeyup: !1,
    rules: {
      oldpw: {
        required: !0
      },
      newpw: {
        notEqualTo: "#oldpw",
        required: !0,
        minlength: 2
      },
      newrpw: {
        notEqualTo: "#oldpw",
        required: !0,
        equalTo: "#newpw"
      }
    },
    messages: {
      oldpw: {
        required: "Please enter Current Password"
      },
      newpw: {
        required: "Please enter New Password",
        notEqualTo: "Old Password is not same as New password"
      },
      newrpw: {
        required: "Please enter Confirm New Password",
        equalTo: "Confirm New Password Should be same as New Password",
        notEqualTo: "Old Password is not same as New password"
      }
    },
    submitHandler: function(a) {
      $("#Change").attr("disabled", "disabled"), $("#message_response").html("");
      var e = $("#changepassword").serializeArray();
      e.push({
        name: "action",
        value: "changepassword"
      }), $.ajax({
        type: "POST",
        url: ajax_url,
        data: e,
        beforeSend: function() {
          $("#Change").attr("disabled", !0)
        },
        success: function(a) {
          var e = jQuery.parseJSON(a);
		  
          $("#Change").removeAttr("disabled"), 1 == e.success ? ($$(".wpsp-popup-return-data").html(e.msg), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"), $("#changepassword").find("input[type=password]").val(""), $("#Change").attr("disabled", !0)) : ($(".wpsp-popup-return-data").html(e.msg), $("#SuccessModal").css("display", "block"), $("#SavingModal").css("display", "none"), $("#SuccessModal").addClass("wpsp-popVisible")), $(".form-control").val("");
		  var delay = 1000; 
            var timeoutID = setTimeout(function() {
             location.reload();
            }, delay);
		  
		  
        },
        error: function() {
          $(".wpsp-popup-return-data").html("Something went wrong!"), $("#SavingModal").css("display", "none"), $("#WarningModal").css("display", "block"), $("#WarningModal").addClass("wpsp-popVisible"), $("#Change").removeAttr("disabled")
        },
        complete: function() {
          $(".pnloader").remove(), $("#Change").removeAttr("disabled")
        }
      })
    }
  })
});
