$( document ).ready(function() {
 /*
  setInterval(function(){
    $.ajax({ url: "overview.php", type: 'post',
        data: {'action': 'check_stream'}, success: function(data){
      
	   if(data.streamAvailable == true){
		   	$("#streamStatus").addClass("btn-default").removeClass("btn-danger").text('<span class="glyphicon glyphicon glyphicon-ok"></span> Stream available'+data.streamAvailable);
	   }else{
	   	$("#streamStatus").addClass("btn-danger").removeClass("btn-default").text('<span class="glyphicon glyphicon glyphicon-remove"></span> Stream in use'+data.streamAvailable);
	   }
        
    }, dataType: "json"});
}, 5000);
*/
$( ".sortable" ).sortable({ items: "tr", placeholder: "info" });

var delay = (function(){
  var timer = 0;
  return function(callback, ms){
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
  };
})();
$("#configForm").on("keyup",function(){
	 var sdata =$("#configForm").serialize() 
	 delay(function(){
	 $(".spinner").show();
	 $.ajax({
  		url: "overview.php",
		type: 'post',
        data: sdata+"&action=save_config"
	  }).done(function(data,status) {
 		if(data.done == 1) $(".spinner").hide();
	  });}
	  ,2000);
	  
	 });

  $("#logoForm").on("keyup",function(){
	 var sdata =$("#logoForm").serialize() 
	 delay(function(){
	 $(".spinner").show();
	 $.ajax({
  		url: "overview.php",
		type: 'post',
        data: sdata+"&action=save_rename"
	  }).done(function(data,status) {
 		if(data.done == 1) $(".spinner").hide();
	  });}
	  ,2000);
	  
	 });
  $(".updateLocalFiles").click(function(){
	  $(".spinner").show();
	  $.ajax({
  		url: "overview.php",
		type: 'post',
        data: {'action': 'build'}
	  }).done(function(data,status) {
 		if(data.done == 1) $(".spinner").hide();
	  });
  })
 
 $('.navbar-nav li').tooltip();
 
});