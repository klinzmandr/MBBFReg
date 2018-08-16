// initial setup of jquery function(s) for page
$(document).ready(function () {
  $("button#cap").click(function(event){
    event.preventDefault();
    var inputks = $("#KS").val();
    inputkeystring = inputks.toLowerCase();
    $.post("capcheckjson.php",
      {
        ks: inputkeystring
      },
    function(data, status) {
        if (data.includes('OK')) {
          // alert("data: "+data);
          $("#captbl").hide();
          $("#CRbtn").removeAttr("disabled");
          $("#LIbtn").removeAttr("disabled");
          }
        else {
          // alert("data: "+data);
          $("#KS").val('');
          }
      });  // end $.post logic 
  });
});  // end ready function
