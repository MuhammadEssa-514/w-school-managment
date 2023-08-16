var $ = jQuery.noConflict();
$(document).ready(function() {
  $("#wpsp-import-data").click(function(F) {
    F.preventDefault(), $(".response").html(""), $(".spinner").css("visibility", "visible"), $(this).attr("disabled", "disabled");
    jQuery.post(ajaxurl, {
      action: "ImportContents"
    }, function(F) {
      $("#wpsp-import-data").removeAttr("disabled"), $(".spinner").css("visibility", "hidden"), $(".response").html(F)
    })
  }), $("#contactForm").submit("click", function(F) {
    F.preventDefault();
    var e = $("#inputName").val(),
      u = $("#inputEmail").val(),
      a = $("#inputMessage").val(),
      t = [];
    if (function(F) {
        return new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i).test(F)
      }(u) || t.push("Please enter valid email address!"), e.length < 3 && t.push("Please enter valid name. More than 3 characters!"), a.length < 10 && t.push("Please enter valid message. More than 10 characters!"), 0 === t.length) {
      var s = $(this).serializeArray();
      $.ajax({
        type: "POST",
        url: "http://localhost/wpschoolpress/public/wpadminContact",
        data: s,
        cache: !1,
        success: function(F) {
          $("#contactResponse").removeClass("alert-danger"), $("#contactResponse").addClass("alert-success"), $("#contactResponse").text(F), $("#contactForm").trigger("reset")
        },
        error: function(F) {
          for (var e in $("#contactResponse").addClass("alert-danger"), $("#errorList").empty(), F.responseJSON.message) $("#errorList").append("<li>" + F.responseJSON.message[e] + "</li>")
        }
      })
    } else if (t.length > 0)
      for (var r in $("#contactResponse").addClass("alert-danger"), $("#errorList").empty(), t) $("#errorList").append("<li>" + t[r] + "</li>")
  })
});
