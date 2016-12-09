jQuery(document).ready(function() {

    // Select2
    //jQuery(".select2").select2({
    //    width: '100%'
    //});
    var buildUserList = function(){
        users = [];
        for(var idx in USERLIST){
            user = USERLIST[idx];
            users.push('<option value="'+user.username+'">'+user.username+'</option>');
        }
        return users.join('');
    }
    var buildLevelGgroup =  function(idx){
        userlist = buildUserList();
        var level = [
            '<div class="flow_level" idx="'+idx+'">',
                '<div class="form-group">',
                   ' <label class="col-md-2 control-label">流程 - '+idx+'</label>',
               ' </div>'+
                '<div class="form-group">',
                   ' <label class="col-md-2 control-label">步骤描述</label>',
                   ' <div class="col-md-10">',
                     '   <input type="text" class="form-control" value="" name="level['+idx+'][desc]"></div>',
                '</div>',
               ' <div class="form-group">',
                 '   <label class="col-md-2 control-label">选择审批人</label>',
                  '  <div class="col-md-10">',
                      '  <select class="select2"  data-placeholder="请选择正确的操作人，支持多个人审批，有一个审批通过即通过." name="level['+idx+'][approver]">',
                       userlist+' </select>',
                 '   </div>',
               ' </div>',
               ' <div class="form-group">',
                  '  <label class="col-md-2 control-label">选择关注人</label>',
                   '<div class="col-md-10">',
                        '<select class="select2"  data-placeholder="支持多个关注人." name="level['+idx+'][watcher]">',
                        userlist+'</select>',
                    '</div>',
                '</div>',
           ' </div>',
        ];
        return level.join('');
    }
    var maxidx = 0;
    var getMaxLevelId = function(){
        var idx = maxidx;
        var list = [];
        $('.flow_level').each(function(){
            var cidx = Number($(this).attr('idx'));
            list.push(cidx);
        })
        for(var idx in list){
            if(list[idx] > maxidx){
                maxidx =list[idx];
            }
        } 
        return maxidx + 1;
    }
    $('.spinner-up').click(function(){
       $('.plus').prev().append(buildLevelGgroup(getMaxLevelId()));
        $('select.select2').select2({
            width: '100%'
        });
    })

});
