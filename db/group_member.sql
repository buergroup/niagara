create table group_member
(
     group_id int(10) unsigned not null comment "组id",
     user varchar(255) not null primary key comment "组成员"
);
