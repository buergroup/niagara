create table flow_level
(
    flow_id int(10) unsigned not null comment "流程id",
    level int(5) not null comment "流程审批的层级",
    name varchar(255) not null comment "流程对应层级的名称",
    approver varchar(255) not null comment "流程的审批者",
    watcher varchar(128) not null comment "流程的观察者,不具备审批权限,只有知情权",
    create_time int(11) not null comment "流程的创建时间",
    update_time int(11) not null comment "流程的更新时间",
    primary key (flow_id, level)
);
