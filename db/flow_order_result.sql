create table flow_order_result
(
    id int(10) unsigned not null auto_increment primary key comment "审批表id",
    orderid int(10) unsigned not null comment "申请单id",
    `level` int(5) not null comment "流程的具体层级，指执行到了第几步",
    claimer varchar(255) not null comment "申请者",
    status int(5) not null comment "申请单状态",
    audit_info varchar(255) not null comment "审批的内容",
    audit_user varchar(255) not null comment "审批者",
    create_time int(11) not null comment "审批单的创建时间",
    update_time int(11) not null comment "审批单的更新时间",
    key orderid_level (orderid, `level`)
);
