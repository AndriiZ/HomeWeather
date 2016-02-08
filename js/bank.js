(function(window, document){

function requestData()
{
    $.ajax({
    url: 'ub.php',
    datatype: "json",
    success: function(data)
    {
       $.each(data.accounts, function(i, item) {
   		 alert(item);
       });
       setInterval(requestData, 5 * 60 *  1000);		
    }
    });
}
$(document).ready(function() {
	requestData();
  });
})(window, document)
