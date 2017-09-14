/**
 * Ajax action to api rest
*/
function register(){
  $.ajax({
    type : "POST",
    url : "api/register",
    data : $('#register_form').serialize(),
    success : function(json) {
      alert(json.success);
      alert(json.message);
      if(json.success == 1) {
        setTimeout(function(){
            location.reload();
        },1000);
      }
    },
    error : function(/*xhr, status*/) {
      alert('Ha ocurrido un problema.');
    }
  });
}

/**
 * Events
 */
$('#register').click(function(e) {
  e.defaultPrevented;
  register();
});
$('#register_form').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        register();
    }
});
