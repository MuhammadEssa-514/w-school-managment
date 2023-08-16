$(document).ready(function() {
  $(".multiselect").change(function() {
    var classarray = [];
    $.each($(".multiselect option:selected"), function() {
      var data = {};
      data.val = $(this).val();
      data.text = $(this).text();
      classarray.push(data);
    });
    $('.multiselect').closest(".wpsp-col-md-12").find('.date-input-block table').html('');
    jQuery.each(classarray, function(i, val) {
      $('.multiselect').closest(".wpsp-col-md-12").find('.date-input-block table').append("<tr><td style='border:1px solid;padding:5px;'><strong id='" + val['text'] + "'>" + val['text'] + "</strong></td><td style='border:1px solid;padding:5px;'><input type='text' class='someclass datepicker' name='Classdata[]' placeholder='yyyy-mm-dd'></td></tr>");
    });
  });
  jQuery(document).on('change', '.multiselect.student-profile', function() {
    // wp_usr_id
    var student_id = jQuery("input[name='UID']").val();
    var data = {};
    var classarray = [];
    data.val = $(this).val();
    data.text = $(this).text();
    classarray.push(data);
    jQuery.ajax({
      url: ajax_url,
      type: "POST",
      dataType: 'json',
      data: {
        action: "get_student_date",
        data: classarray,
        student_id: student_id,
      },
    }).success(function(data) {
      if (data.result === true) {
        jQuery('.date-input-block table').html('').append(data.data);
      }
    });
  });
  $('body').on('focus', '.datepicker', function() {
    $(this).datepicker({
      format: "yyyy-mm-dd"
    })
  });
  $('.datepicker').datepicker({
    format: "yyyy-mm-dd"
  });
  var lines = [];
  $("#Doj").datepicker({
    autoclose: true,
    dateFormat: date_format,
    todayHighlight: true,
    changeMonth: true,
    changeYear: true,
    yearRange: "-50:+0",
    maxDate: 0,
  });
  $("#Dob").datepicker({
    autoclose: true,
    dateFormat: date_format,
    todayHighlight: true,
    changeMonth: true,
    changeYear: true,
    maxDate: 0,
    yearRange: "-50:+0",
    beforeShow: function(input, inst) {
      $(document).off('focusin.bs.modal');
    },
    onClose: function() {
      $(document).on('focusin.bs.modal');
    },
    onSelect: function(selectedDate) {
      $(this).valid();
    }
  });
  $('#student_table').dataTable({
    language: {
      paginate: {
        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
      },
      search: "",
      searchPlaceholder: "Search..."
    },
    "dom": '<"wpsp-dataTable-top"f>rt<"wpsp-dataTable-bottom"<"wpsp-length-info"li>p<"clear">>',
    "order": [],
    "columnDefs": [{
      "targets": 'nosort',
      "orderable": false,
    }],
    drawCallback: function(settings) {
      var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
      pagination.toggle(this.api().page.info().pages > 1);
    },
    responsive: true
  });
  $("#displaypicture,#p_displaypicture").change(function() {
    var id = $(this).attr('id');
    imagePreview(this); // Use Image Preview
    $('.validation-error-' + id).html('');
    var fsize = document.getElementById(id).files[0].size;
    var fileName = $(this).val();
    var maxsize = 3 * 1024 * 1024;
    if (fsize > maxsize) {
      $('.validation-error-' + id).html('File Size should be less than 3 MB, Please select another file');
      $(this).val('');
    }
    var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
    if ($.inArray(fileExtension, ['jpg', 'jpeg']) == -1) {
      $('.validation-error-' + id).html('Please select either jpg or jpeg file');
      $(this).val('');
    }
  });
  var sid = $('#studID').val();
  $(".thumb").click(function(event) {
    vent = event || window.event;
    var target = event.target || event.srcElement,
      link = target.src ? target.parentNode : target,
      options = {
        index: link,
        event: event
      },
      links = this.getElementsByTagName('a');
    blueimp.Gallery(links, options);
  });
  $(".deleteImage").click(function() {
    var iname = $(this).attr('imglink');
    var data = [];
    data.push({
      name: 'action',
      value: 'deletePhoto'
    }, {
      name: 'iname',
      value: iname
    }, {
      name: 'sid',
      value: sid
    });
    var result = window.confirm('Are you sure want to delete?');
    if (result == true) {
      $.ajax({
        type: "POST",
        url: ajax_url,
        data: data,
        beforeSend: function() {
          $.fn.notify('loader', {
            'desc': 'Deleting image..'
          });
        },
        success: function(pdata) {
          $.fn.notify('success', {
            'desc': pdata
          });
          location.reload();
        },
        complete: function() {
          $('.pnloader').remove();
        }
      });
    }
  });
  $("#StudentEntryForm").validate({
    rules: {
      s_fname: {
        required: (jQuery("input[name='s_fname']").data("is_required")) ? true : false,
      },
      s_mname:  {
        required: (jQuery("input[name='s_mname']").data("is_required")) ? true : false,
      },
      s_address: {
        required: (jQuery("input[name='s_address']").data("is_required")) ? true : false,
      },
      s_lname: {
        required: (jQuery("input[name='s_lname']").data("is_required")) ? true : false,
      },
      Email: {
        required: (jQuery("input[name='Email']").data("is_required")) ? true : false,
      },
      s_rollno: {
        required: (jQuery("input[name='s_rollno']").data("is_required")) ? true : false,
      },
      Username: {
        required: (jQuery("input[name='Username']").data("is_required")) ? true : false,
        minlength: 5,
      },

      Password: {
        required: (jQuery("input[name='Password']").data("is_required")) ? true : false,
        minlength: 5
      },
      ConfirmPassword: {
        required: (jQuery("input[name='ConfirmPassword']").data("is_required")) ? true : false,
        minlength: 5,
        equalTo: "#Password"
      },

      s_phone: {
        required: (jQuery("input[name='s_phone']").data("is_required")) ? true : false,
        number: true,
        minlength: 10,
        maxlength: 12
      },
      s_zipcode: {
        required: (jQuery("input[name='s_zipcode']").data("is_required")) ? true : false,
        number: true
      },
      s_pzipcode: {
        required: (jQuery("input[name='s_pzipcode']").data("isrequired")) ? true : false,
        number: true
      },
    },
    messages: {
      s_fname: "Please enter first Name",
      s_address: "Please enter current address",
      s_lname: "Please enter last Name",
      s_mname: "Please enter middle Name",
      s_rollno: "Please enter Roll Number",
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
      Email: "Please enter a valid email address",
    },
    submitHandler: function(form) {
      var data = new FormData();
      var fdata = $('#StudentEntryForm').serializeArray();
      var ufile = $('#displaypicture')[0].files[0];
      var pfile = $('#p_displaypicture')[0].files[0];
      data.append('action', 'AddStudent');
      data.append('displaypicture', ufile);
      data.append('pdisplaypicture', pfile); //parent file
      $.each(fdata, function(key, input) {
        data.append(input.name, input.value);
      });
      data.append('data', fdata);
      $.ajax({
        type: "POST",
        url: ajax_url,
        data: data,
        cache: false,
        processData: false,
        contentType: false,
        beforeSend: function() {
          //$.fn.notify('loader', {'desc': 'Saving data..'});
          //$('#studentform').attr("disabled", true);
          $("#SavingModal").css("display", "block");
          $('#studentform').attr("disabled", 'disabled');
        },
        success: function(rdata) {
          $('#studentform').removeAttr('disabled');
          if (rdata == 'success') {
            $(".wpsp-popup-return-data").html('Student added successfully !');
            $("#SuccessModal").css("display", "block");
            $("#SavingModal").css("display", "none");
            $("#SuccessModal").addClass("wpsp-popVisible");
            var wpsp_pageURL = $('#wpsp_locationginal1').val();
            var delay = 1000;
            var url = wpsp_pageURL + "admin.php?page=sch-student";
            var timeoutID = setTimeout(function() {
              window.location.href = url;
            }, delay);
            $('#StudentEntryForm').trigger("reset");
            $('#studentform').attr("disabled", true);
          } else {
            $(".wpsp-popup-return-data").html(rdata);
            $("#SavingModal").css("display", "none");
            $("#WarningModal").css("display", "block");
            $("#WarningModal").addClass("wpsp-popVisible");
          }
        },
        error: function() {
          $("#SavingModal").css("display", "none");
          $("#WarningModal").css("display", "block");
          $("#WarningModal").addClass("wpsp-popVisible");
          $('#teacherform').removeAttr('disabled');
          //$('#formresponse').html("<div class='alert alert-danger'>Something went wrong!</div>");
          //$('#studentform').removeAttr('disabled');
        },
        complete: function() {
          $('.pnloader').remove();
          $('#studentform').removeAttr('disabled');
        }
      });
    }
  });
  $("#StudentEditForm").validate({
    rules: {
      s_fname: {
        required: (jQuery("input[name='s_fname']").data("is_required")) ? true : false,
      },
      //s_mname: "required",
      s_address: {
        required: (jQuery("input[name='s_address']").data("is_required")) ? true : false,
      },
      //p_fname:"required",
      s_lname: {
        required: (jQuery("input[name='s_lname']").data("is_required")) ? true : false,
      },
      s_zipcode: {
        required: (jQuery("input[name='s_zipcode']").data("is_required")) ? true : false,
      },
      s_rollno: {
        required: (jQuery("input[name='s_rollno']").data("is_required")) ? true : false,
      },
      // s_phone: {
      // 	required: true,
      // 	number: true,
      // 	minlength: 7,
      //              maxlength: 10
      // },
      s_zipcode: {
        required: (jQuery("input[name='s_zipcode']").data("is_required")) ? true : false,
        number: true
      },
      s_pzipcode: {
        required: (jQuery("input[name='s_pzipcode']").data("is_required")) ? true : false,
        number: true
      },
    },
    messages: {
      s_fname: "Please enter first Name",
      s_address: "Please enter current address",
      s_lname: "Please enter last Name",
      //s_mname: "Please enter middle Name",
      s_rollno: "Please enter Roll Number",
    },
    submitHandler: function(form) {
      var myform = document.getElementById("StudentEditForm");
      var data = new FormData();
      var fdata = $('#StudentEditForm').serializeArray();
      var ufile = $('#displaypicture')[0].files[0];
      var pfile = $('#p_displaypicture')[0].files[0];
      data.append('action', 'UpdateStudent');
      data.append('displaypicture', ufile);
      data.append('p_displaypicture', pfile); //parent file
      $.each(fdata, function(key, input) {
        data.append(input.name, input.value);
      });
      data.append('data', fdata);
      $.ajax({
        type: "POST",
        url: ajax_url,
        data: data,
        cache: false,
        processData: false,
        contentType: false,
        beforeSend: function() {
          //$.fn.notify('loader', {'desc': 'Saving data..'});
          //$('#studentform').attr("disabled","disabled");
          $("#SavingModal").css("display", "block");
          $('#studentform').attr("disabled", 'disabled');
        },
        success: function(rdata) {
          $('#studentform').removeAttr('disabled');
          if (rdata == 'success0') {
            //$.fn.notify('success', {'desc': 'Student added successfully', autoHide: true, clickToHide: true});
            $(".wpsp-popup-return-data").html('Student added successfully !');
            $("#SuccessModal").css("display", "block");
            $("#SavingModal").css("display", "none");
            $("#SuccessModal").addClass("wpsp-popVisible");
            var wpsp_pageURL = $('#wpsp_locationginal').val();
            var delay = 1000;
            var url = wpsp_pageURL + "admin.php?page=sch-student";
            var timeoutID = setTimeout(function() {
              window.location.href = url;
            }, delay);
            $('#StudentEntryForm').trigger("reset");
            $('#studentform').attr("disabled", "disabled");
          } else {
            // $.fn.notify('error', {'desc': rdata, autoHide: true, clickToHide: true  });
            $(".wpsp-popup-return-data").html(rdata);
            $("#SavingModal").css("display", "none");
            $("#WarningModal").css("display", "block");
            $("#WarningModal").addClass("wpsp-popVisible");
          }
        },
        error: function() {
          $("#SavingModal").css("display", "none");
          $("#WarningModal").css("display", "block");
          $("#WarningModal").addClass("wpsp-popVisible");
          $('#teacherform').removeAttr('disabled');
          //$('#formresponse').html("<div class='alert alert-danger'>Something went wrong!</div>");
          //$('#studentform').removeAttr('disabled');
        },
        complete: function() {
          $('.pnloader').remove();
          $('#studentform').removeAttr('disabled');
        }
      });
    }
  });
  $('#ClassID').change(function() {
    $('#StudentClass').submit();
  });
  $(document).on('click', '.ViewStudent', function(e) {
    e.preventDefault();
    var data = [];
    var sid = $(this).data('id');
    data.push({
      name: 'action',
      value: 'StudentPublicProfile'
    }, {
      name: 'id',
      value: sid
    });
    $.ajax({
      type: "POST",
      url: ajax_url,
      data: data,
      beforeSend: function() {
        //$.fn.notify('loader', {'desc': 'Loading student information'});
      },
      success: function(pdata) {
        $('#ViewModalContent').html(pdata);
        /*$(this).click();*/
      },
      complete: function() {
        $('.pnloader').remove();
      }
    });
  });
  $('.ViewParent').click(function() {
    var data = [];
    var pid = $(this).data('id');
    data.push({
      name: 'action',
      value: 'ParentPublicProfile'
    }, {
      name: 'id',
      value: pid
    });
    $.ajax({
      type: "POST",
      url: ajax_url,
      data: data,
      beforeSend: function() {
        // $.fn.notify('loader', {'desc': 'Loading student information'});
      },
      success: function(pdata) {
        $('#ViewModalContent').html(pdata);
        $('#ViewModal').modal('show');
      },
      complete: function() {
        $('.pnloader').remove();
      }
    });
  });
  $('#StudentEntryForm').submit(function(e) {
    e.preventDefault();
  });
  $('#StudentEditForm').submit(function(e) {
    e.preventDefault();
  });
  $("#selectall").click(function() {
    if ($(this).prop("checked") == true) {
      $(".strowselect").prop('checked', true);
    } else {
      $(".strowselect").prop("checked", false);
    }
  });
  $(".strowselect").click(function() {
    if ($(this).prop("checked") != true) {
      $("#selectall").prop("checked", false);
    }
  });
  $(document).on('click', '#d_teacher', function(e) {
    var cid = $(this).data('id');
    console.log(cid);
    $("#teacherid").val(cid);
    $("#DeleteModal").css("display", "block");
    $("#DeleteModal").attr('data-single-delete', true);
    $("#DeleteModal").attr('data-id', cid);
  });
  $(document).on('click', '.ClassDeleteBt', function(e) {

    var cid = [];
    var singledata = $("#DeleteModal").attr("data-single-delete");
    if (singledata == 'true'){
      cid = $("#DeleteModal").attr("data-id");
      var nn = $('#wps_generate_nonce').val();
      var data = [];
      data.push({
        name: 'action',
        value: 'DeleteStudent'
      }, {
        name: 'sid',
        value: cid
      },{
        name: 'wps_generate_nonce',
        value: nn
      });
      //cid = '0';
      jQuery.post(ajax_url, data, function(cddata) {
        if (cddata == 'success') {
          location.reload();
        } else {
          $(".wpsp-popup-return-data").html('Operation failed.Something went wrong!');
          $("#SavingModal").css("display", "none");
          $("#WarningModal").css("display", "block");
          $("#WarningModal").addClass("wpsp-popVisible");
        }
      });
    }else{

    }
    //$("#overlay").addClass("overlays");

  });


  $('#bulkaction').change(function() {
    var op = $(this).val();
    if (op == 'bulkUsersDelete') {

      var uids = $('input[name^="UID"]').map(function() {
        if ($(this).prop('checked') == true)
          return this.value;
      }).get();

      if (uids.length == 0) {
        $(".wpsp-popup-return-data").html('No user selected!');
        $("#SavingModal").css("display", "none");
        $("#WarningModal").css("display", "block");
        $("#WarningModal").addClass("wpsp-popVisible");
        return false;
      } else {
        $("#DeleteModal").css("display", "block");
        $(document).on('click', '.ClassDeleteBt', function(e) {
          var data = [];
          data.push({
            name: 'action',
            value: 'bulkDelete'
          });
          data.push({
            name: 'UID',
            value: uids
          });
          data.push({
            name: 'type',
            value: 'student'
          });
          jQuery.post(ajax_url, data, function(cddata) {
            if (cddata == 'success') {
              location.reload();
            } else {
              $(".wpsp-popup-return-data").html('Operation failed.Something went wrong!');
              $("#SavingModal").css("display", "none");
              $("#WarningModal").css("display", "block");
              $("#WarningModal").addClass("wpsp-popVisible");
            }
          });
        });
      }
    }
  });


  /* This function is used when viewAttendance button click */
  $(document).on('click', '.viewAttendance', function(e) {
    $(this).removeAttr("href");
    var stid = $(this).attr('data-id');
    var data = [];
    data.push({
      name: 'action',
      value: 'getAttReport'
    });
    data.push({
      name: 'student_id',
      value: stid
    });
    $.ajax({
      type: "POST",
      url: ajax_url,
      data: data,
      beforeSend: function() {
        //   $.fn.notify('loader', {'desc': 'Loading attendance report'});
      },
      success: function(data) {
        $('#ViewModalContent').html(data);
        $('#ViewModal').modal('show');
      },
      complete: function() {
        $('.pnloader').remove();
      }
    });
  });
  /* Add class when student add */
  $(document).on('change', '#selectstudclass', function() {
    if ($(this).val() == 'other') {
      $('#AddModalClass').show();
      $('#AddModalClass').addClass('in');
    }
  });
  $(document).on('click', '#ClassAddClose,.close', function(e) {
    $('#AddModalClass').hide();
    $('#AddModalClass').removeClass('in');
    $('#selectstudclass').prop('selectedIndex', 0);
  });
  /* Classform Validation */
  $("#ClassAddForm").validate({
    rules: {
      Name: {
        required: true,
        minlength: 2,
      },
      ClassTeacherID: {
        number: true
      },
      Sdate: {
        required: true,
      },
      Edate: {
        required: true,
      }
    },
    messages: {
      Name: {
        required: "Please enter classname",
        minlength: "Class must consist of at least 2 characters"
      }
    },
    submitHandler: function(form) {
      var data = $('#ClassAddForm').serializeArray();
      data.push({
        name: 'action',
        value: 'AddClass'
      });
      data.push({
        name: 'add_from',
        value: 'student'
      });
      $.ajax({
        type: "POST",
        url: ajax_url,
        data: data,
        beforeSend: function() {
          $('#ClassAddForm .btn-primary').attr('disabled', true);
          $('#ClassAddForm .formresponse').html("Saving..");
        },
        success: function(rdata) {
          var response = jQuery.parseJSON(rdata);
          if (response.statuscode == 1) {
            $('#ClassAddForm .formresponse').html("<div class='alert alert-success'>" + response.msg + "</div>");
            $('#ClassAddForm').trigger("reset");
            if (response.html != "")
              $(response.html).insertBefore('.class-other');
          } else if (response.statuscode == 0) {
            $('#ClassAddForm .formresponse').html("<div class='alert alert-danger'>" + response.msg + "</div>");
          }
        }
      });
    }
  });
  $('.user-same-error').hide();
  $('.chk-username').blur(function() {
    if ($('#Username').val().toLowerCase() == $('#p_username').val().toLowerCase()) {
      $(this).parent().find('.user-same-error').show();
    } else {
      $('.user-same-error').hide();
    }
  });
  $('.user-email-error').hide();
  $('.chk-email').blur(function() {
    if ($('#Email').val().toLowerCase() == $('#pEmail').val().toLowerCase()) {
      $(this).parent().find('.user-email-error').show();
    } else {
      $('.user-email-error').hide();
    }
  });
  $('#pEmail').blur(function() {
    var parentEmail = $(this).val();
    $('#parent-field-lists').find('input:radio, input:text,input:password, input:file, select').each(function() {
      $(this).prop('disabled', false);
    });
    if (parentEmail != '') {
      if (isEmail(parentEmail)) {
        $('#parent-field-lists').find('input:radio, input:text,input:password, input:file, select').each(function() {
          $(this).prop('disabled', true);
        });
        var data = {
          'action': 'check_parent_info',
          'parentEmail': parentEmail
        };
        jQuery.post(ajax_url, data, function(response) {
          var result = jQuery.parseJSON(response);
          if (result.status == 1) {
            $('#p_firstname').val(result.data['p_fname']);
            $('#p_middlename').val(result.data['p_mname']);
            $('#p_lastname').val(result.data['p_lname']);
            $('#p_edu').val(result.data['p_edu']);
            $('#p_profession').val(result.data['p_profession']);
            $('#p_username').val(result.username);
            $("#p_bloodgroup").val(result.data['p_bloodgrp']);
            $('input[name="p_gender"]').attr('checked', false);
            $('input[name="p_gender"][value="' + result.data['p_gender'] + '"]').attr('checked', true);
          } else if (result.status == 0) {
            $('#parent-field-lists').find('input:radio, input:text,input:password, input:file, select').each(function() {
              $(this).prop('disabled', false);
            });
          }
        });
      } else {
        $('#pEmail').focus();
        $('#parent-field-lists').find('input:radio, input:text,input:password, input:file, select').each(function() {
          $(this).prop('disabled', false);
        });
      }
    }
  });
  /* This function is used for Address,Country,Pincode Same as above Copy for address*/
  $('#sameas').change(function() {
    if ($('#sameas').is(":checked")) {
      $("#permanent_address").val($("#current_address").val());
      $("#permanent_city").val($("#current_city").val());
      $("#permanent_country").val($("#current_country").val());
      $("#permanent_pincode").val($("#current_pincode").val());
    } else {
      $("#permanent_address").val('');
      $("#permanent_city").val('');
      $("#permanent_country").val('');
      $("#permanent_pincode").val('');
    }
  });
  /* This function is used for Test Email Address */
  function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
  }
  /* This function is used for Student/Parent Profile Picture Show Preview */
  function imagePreview(input) {
    if (input.files) {
      var reader = new FileReader();
      if (input.id == 'displaypicture') {
        reader.onload = function(e) {
          $('#img_preview')
            .attr('src', e.target.result)
            .width(112)
            .height(112);
        }
      }
      if (input.id == 'p_displaypicture') {
        reader.onload = function(e) {
          $('#img_preview1')
            .attr('src', e.target.result)
            .width(112)
            .height(112);
        }
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
  /* Custom Tab Js */
  $('#verticalTab').easyResponsiveTabs({
    type: 'vertical',
    width: 'auto',
    fit: true
  });
  if ($(window).width() < 991) { //alert('test');
    $('#verticalTab').find('.wpsp-resp-tab-active').removeClass('wpsp-resp-tab-active');
    $('#verticalTab').find('.wpsp-resp-tab-content-active').removeClass('wpsp-resp-tab-content-active').css("display", "");
  }
  if (jQuery(window).width() < 991) {
    //alert('test');
    if (!$('#verticalTab').find('.wpsp-resp-tab-active').length) {
      //alert('test');
      $('.wpsp-resp-tabs-list .wpsp-resp-tab-item:first-child').click();
    }
  }
});
jQuery(window).resize(function() {
  if (jQuery(window).width() > 991) {
    //alert('test');
    if (!$('#verticalTab').find('.wpsp-resp-tab-active').length) {
      //alert('test');
      $('.wpsp-resp-tabs-list .wpsp-resp-tab-item:first-child').click();
    }
  }
});
