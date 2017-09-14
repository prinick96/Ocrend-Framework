/**
 * Ajax action to api rest
*/
function lostpass(){
  $.ajax({
    type : "POST",
    url : "api/lostpass",
    data : $('#lostpass_form').serialize(),
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
$('#lostpass').click(function(e) {
  e.defaultPrevented;
  lostpass();
});
$('#lostpass_form').keypress(function(e) {
  e.defaultPrevented;
    if(e.which == 13) {
        lostpass();
    }
});