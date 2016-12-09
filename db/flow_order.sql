create table flow_order
(
    orderid int(10) unsigned not null primary key comment "申请单id",
    claimer varchar(255) not null comment "申请者",
    flow_id int(5) not null comment "要申请的流程id",
    summary varchar(255) not null comment "申请单标题",
    content varchar(255) not null comment "申请单的具体内容",
    create_time int(11) not null comment "申请单的创建时间",
    update_time int(11) not null comment "申请单的更新时间"
);
