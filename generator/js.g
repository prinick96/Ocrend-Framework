$('#{{view}}').click(function(e){

  /* START Prevención de doble clic */
  e.preventDefault();
  /* END Prevención de doble clic */

  var error_icon = '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ',
      success_icon = '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ',
      process_icon = '<span class="fa fa-spinner fa-spin" aria-hidden="true"></span> ';

  $('#ajax_{{view}}').removeClass('alert-danger');
  $('#ajax_{{view}}').removeClass('alert-warning');
  $('#ajax_{{view}}').addClass('alert-warning');
  $("#ajax_{{view}}").html(process_icon  + 'Procesando por favor espere...');
  $('#ajax_{{view}}').removeClass('hide');

  $.ajax({
    type : "{{method}}",
    url : "api/{{api_rest}}",
    data : $('#{{view}}_form').serialize(),
    success : function(json) {
      var obj = jQuery.parseJSON(json);
      if(obj.success == 1) {
        $('#ajax_{{view}}').html(success_icon + obj.message);
        $("#ajax_{{view}}").removeClass('alert-warning');
        $("#ajax_{{view}}").addClass('alert-success');
        setTimeout(function(){
          location.reload();
        },1000);
      } else {
        $('#ajax_{{view}}').html(error_icon  + obj.message);
        $("#ajax_{{view}}").removeClass('alert-warning');
        $("#ajax_{{view}}").addClass('alert-danger');
      }
    },
    error : function() {
      window.alert('#{{view}} ERORR');
    }
  });
});
