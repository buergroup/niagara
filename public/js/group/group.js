$(document).ready(function() {
    $('#datatable').dataTable();
    $('.addgroup').click(function(){
    	$.post('/group/create', {'name':$('#group_name').val()},function(data){
			if(data.status){
				console.log(data);
				window.location.href='/group/list';
			}else
				alert(data.msg);
		},'json');
    });
    $('select.select2').select2({
        width: '100%'
    });
    var getSelectLabel = function(){
        var data = [];
        $('.select2-search-choice').find('div').each(function(){
            data.push($(this).html());
        })
        return data.join(",");
    }
    $('.adduser').click(function(){
        $.post('/group/adduser', {'user':getSelectLabel(),'group_id':$('#groupid').val()},function(data){
            if(data.status){
                console.log('/group/show?id='+$('#groupid').val());
                window.location.href='/group/show?id='+$('#groupid').val();
            }else
                alert(data.msg);
        },'json');
    });

} );
           
