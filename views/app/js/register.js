$('#register').click(function(){

  var error_icon = '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ',
      success_icon = '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ',
      process_icon = '<span class="fa fa-spinner fa-spin" aria-hidden="true"></span> ';

  $('#ajax_register').removeClass('alert-danger');
  $('#ajax_register').removeClass('alert-warning');
  $('#ajax_register').addClass('alert-warning');
  $("#ajax_register").html(process_icon  + 'Procesando información, por favor espere...');
  $('#ajax_register').removeClass('hide');

  $.ajax({
    type : "POST",
    url : "api/register",
    data : $('#register_form').serialize(),
    success : function(json) {
      var obj = jQuery.parseJSON(json);
      if(obj.success == 1) {
        $('#ajax_register').html(success_icon + obj.message);
        $("#ajax_register").removeClass('alert-warning');
        $("#ajax_register").addClass('alert-success');
        setTimeout(function(){
          location.reload();
        },1000);
      } else {
        $('#ajax_register').html(error_icon  + obj.message);
        $("#ajax_register").removeClass('alert-warning');
        $("#ajax_register").addClass('alert-danger');
      }
    },
    error : function() {
      window.alert('#register ERORR');
    }
  });
});
