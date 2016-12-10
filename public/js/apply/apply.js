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
    $('.btn-purple').click(function(){
        var summary = $('input[name=summary]').val();
        var content = $('input[name=content]').val();
        if(!summary || !content){
            alert('摘要和描述信息不能为空哈~');
            return false;
        }
    })
});