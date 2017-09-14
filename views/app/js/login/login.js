/**
 * Ajax action to api rest
*/
function login(){
  $.ajax({
    type : "POST",
    url : "api/login",
    data : $('#login_form').serialize(),
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
$('#login').click(function(e) {
  e.defaultPrevented;
  login();
});
$('#login_form').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        login();
    }
});
