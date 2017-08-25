/**
 * Ajax action to api rest
 * 
 * @param {*} e 
*/
function login(e){
  $.ajax({
    type : "POST",
    url : "api/login",
    data : $('#login_form').serialize(),
    success : function(json) {
      console.log(json.success);
      console.log(json.message);
      if(json.success == 1) {
        setTimeout(function(){
            location.reload();
        },1000);
      }
    },
    error : function(xhr, status) {
      console.log('Ha ocurrido un problema.');
    }
  });
}

/**
 * Events
 */
$('#login').click(function(e) {
  e.defaultPrevented;
  login(e);
});
$('#login_form').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        login(e);
    }
});
