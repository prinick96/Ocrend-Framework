/* #lostpass es el ID del botón que acciona este código. */
/* #ajax_lostpass es el ID del DIV que muestra resultados y proceso de carga. */
/* #lostpass_form es el ID del formulario del cual se recogen todos los datos. */

$('#lostpass').click(function(){

	var error_icon = '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ',
		success_icon = '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ',
		process_icon = '<span class="fa fa-spinner fa-spin" aria-hidden="true"></span> ';

	$('#ajax_lostpass').removeClass('alert-danger');
	$('#ajax_lostpass').removeClass('alert-warning');
	$('#ajax_lostpass').addClass('alert-warning');
	$('#ajax_lostpass').html(process_icon  + 'Procesando, el proceso puede tardar si estás en localhost y puede tardar en llegar el email...');
	$('#ajax_lostpass').removeClass('hide');

	$.ajax({
		type : "POST",
		url : "api/lostpass",
		data : $('#lostpass_form').serialize(),
		success : function(json) {
			var obj = jQuery.parseJSON(json);
			if(obj.success == 1) {
				$('#ajax_lostpass').html(success_icon + obj.message);
				$('#ajax_lostpass').removeClass('alert-warning');
				$('#ajax_lostpass').addClass('alert-success');
				setTimeout(function(){
					location.reload();
				},3000);
			} else {
				$('#ajax_lostpass').html(error_icon  + obj.message);
				$('#ajax_lostpass').removeClass('alert-warning');
				$('#ajax_lostpass').addClass('alert-danger');
			}
		},
		error : function() {
			window.alert('#lostpass ERORR');
		}
	});
});
