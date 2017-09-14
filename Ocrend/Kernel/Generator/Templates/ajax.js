/**
 * Ajax action to api rest
*/
function {{view}}(){
  $.ajax({
    type : "{{method}}",
    url : "api/{{rest}}",
    data : $('#{{view}}_form').serialize(),
    success : function(json) {
      alert(json.success);
      alert(json.message);
      if(json.success == 1) {
        setTimeout(function(){
            location.reload();
        },1000);
      }
    },
    error : function(xhr, status) {
      alert('Ha ocurrido un problema.');
    }
  });
}

/**
 * Events
 *  
 * @param {*} e 
 */
$('#{{view}}').click(function(e) {
  e.defaultPrevented;
  {{view}}();
});
$('#{{view}}_form').keypress(function(e) {
    e.defaultPrevented;
    if(e.which == 13) {
        {{view}}();
    }
});