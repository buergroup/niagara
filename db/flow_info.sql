create table flow_info
(
    flow_id int(10) unsigned not null primary key comment "流程id",
    name varchar(255) not null comment "流程的名称",
    status int(5) not null comment "流程的状态",
    `desc` varchar(255) not null comment "流程的具体描述",
    creator varchar(255) not null comment "流程的创建者",
    create_time int(11) not null comment "流程的创建时间",
    update_time int(11) not null comment "流程的更新时间"
);
