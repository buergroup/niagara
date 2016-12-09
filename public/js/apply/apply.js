$(document).ready(function() {
    $('.passed,.reject').click(function(){
    	if(!$('#content').val()){
    		alert('说点回复内容吧');
    		return false;
    	}
        $.post('/approval/audit', {
        	'status':$(this).attr('node-val'),
        	'orderid':$('#orderid').val(),
        	'level':$('#level').val(),
        	'content':$('#content').val()
        },function(data){
        	if(data.status)
        	 	window.location.href='/apply/show?id='+$('#orderid').val();
            else
            	alert(data.msg);
        },'json');
    }); 
});