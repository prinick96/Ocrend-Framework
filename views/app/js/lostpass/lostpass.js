/**
 * Ajax action to api rest
 * 
 * @param {*} e 
*/
function lostpass(e){
  $.ajax({
    type : "POST",
    url : "api/lostpass",
    data : $('#lostpass_form').serialize(),
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
$('#lostpass').click(function(e) {
  e.defaultPrevented;
  lostpass(e);
});
$('#lostpass_form').keypress(function(e) {
  e.defaultPrevented;
    if(e.which == 13) {
        lostpass(e);
    }
});