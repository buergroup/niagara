$('.btn-success').click(function(){
	$.post('/user/login', {'username':$('input[name=username]').val(),'password':$('input[name=password]').val(),'autologin':$('input[name=autologin]').val()},function(data){
		if(data.status){
			console.log(data);
			window.location.href=data.forward;
		}else
		alert(data.msg);
	},'json');
})
