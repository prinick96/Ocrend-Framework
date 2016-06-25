$('#login').click(function(){

  var error_icon = '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ',
      success_icon = '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ',
      process_icon = '<span class="fa fa-spinner fa-spin" aria-hidden="true"></span> ';

  $('#ajax_login').removeClass('alert-danger');
  $('#ajax_login').removeClass('alert-warning');
  $('#ajax_login').addClass('alert-warning');
  $("#ajax_login").html(process_icon  + 'Iniciando sesión, por favor espere...');
  $('#ajax_login').removeClass('hide');

  $.ajax({
    type : "POST",
    url : "api/login",
    data : $('#login_form').serialize(),
    success : function(json) {
      var obj = jQuery.parseJSON(json);
      if(obj.success == 1) {
        $('#ajax_login').html(success_icon + obj.message);
        $("#ajax_login").removeClass('alert-warning');
        $("#ajax_login").addClass('alert-success');
        setTimeout(function(){
          location.reload();
        },1000);
      } else {
        $('#ajax_login').html(error_icon  + obj.message);
        $("#ajax_login").removeClass('alert-warning');
        $("#ajax_login").addClass('alert-danger');
      }
    },
    error : function() {
      window.alert('#login ERORR');
    }
  });
});
