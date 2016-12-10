#########################################################################
# Author: WangXiaolu
# Created Time: Fri 09 Dec 2016 01:23:30 PM CST
# File Name: send_mail.sh
# Description:邮件发送模块
 #########################################################################
#!/bin/bash

PYTHON_BIN='python'
SEND_MAIL_BIN=send_mail.py
MYSQL_BIN='mysql -hdbm.office.domob-inc.cn -udomob -pdomob niagara'
DATA_DIR=data
FLOW_INFO_FILE=flow_info.data
FLOW_LEVEL_FILE=flow_level.data
FLOW_ORDER_FILE=flow_order.data
FLOW_ORDER_RESULT_FILE=flow_order_result.data


PRE_ID=0

while [ 1 ]
do
    $MYSQL_BIN -e "select flow_id, level, approver, watcher from flow_level" >$DATA_DIR/$FLOW_LEVEL_FILE
    $MYSQL_BIN -e "select flow_id, name from flow_info" >$DATA_DIR/$FLOW_INFO_FILE
    $MYSQL_BIN -e "select * from flow_order" >$DATA_DIR/$FLOW_ORDER_FILE
    $MYSQL_BIN -e "select * from flow_order_result where id>$PRE_ID" >$DATA_DIR/$FLOW_ORDER_RESULT_FILE
    sed -i '1d' $DATA_DIR/$FLOW_ORDER_RESULT_FILE
    echo "begin, pre_id = $PRE_ID"
    PRE_ID=`awk -F '\t' 'BEGIN{id='$PRE_ID'}{if($1>id){id=$1}}END{print id}' $DATA_DIR/$FLOW_ORDER_RESULT_FILE`
    echo "end, pre_id = $PRE_ID"
    if [ -s $DATA_DIR/$FLOW_ORDER_RESULT_FILE ]
    then
        $PYTHON_BIN $SEND_MAIL_BIN
        if [ $? -eq 0 ]
        then
            echo `date` "send mail done"
        else
            echo `date` "send mail failed"
        fi
    fi
    sleep 5
done
