$(document).ready(function() {
    $('#datatable').dataTable();
    $('.btn-effect-ripple').click(function(){
    	$.post('/group/create', {'name':$('#group_name').val()},function(data){
			if(data.status){
				console.log(data);
				window.location.href='/group/list';
			}else
				alert(data.msg);
		},'json');
    })
} );
           
