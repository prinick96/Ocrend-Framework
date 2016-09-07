function _ini_lostpass() {
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
}

if(document.getElementById('lostpass')) {
	document.getElementById('lostpass').onclick = function() {
	  _ini_lostpass();
	};
}

if(document.getElementById('lostpass_form')) {
	document.getElementById('lostpass_form').onkeypress = function(e) {
	    if (!e) e = window.event;
	    var keyCode = e.keyCode || e.which;
	    if (keyCode == '13'){
	      _ini_lostpass();
				
				return false;
	    }
	};
}
