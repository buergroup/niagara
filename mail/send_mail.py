#!/usr/local/bin/python
# coding=utf-8
import sys
import os
import md5
import md5
import time
import datetime

import ConfigParser
from email_tool import EmailTool


class flow_order:
    def __init__(self, claimer, flow_id, summary, content, status, create_time, update_time):
        self.claimer = claimer
        self.flow_id = flow_id
        self.summary = summary
        self.content = content
        self.status = status
        self.create_time = create_time
        self.update_time = update_time

class flow_level:
    def __init__(self, approver, watcher):
        self.approver = approver
        self.watcher = watcher

class flow_order_result:
    def __init__(self, orderid, level, claimer, status, audit_info, audit_user, create_time, update_time):
        self.orderid = orderid
        self.level = level
        self.claimer = claimer
        self.status = status
        self.audit_info = audit_info
        self.audit_user = audit_user
        self.create_time = create_time
        self.update_time = update_time

class send_mail:
    def __init__(self):
        mail_cfg_path = './conf/mail.conf'
        self.config = ConfigParser.ConfigParser()
        self.config.read(mail_cfg_path)

        self.mail_html = ''
        self.flow_info = {}
        self.flow_level = {}
        self.flow_order = {}

        self.out_html_dir='./html'
        os.system('mkdir '+self.out_html_dir+' -p')

    def load_flow_info(self):
        flow_info_file = self.config.get("email", "flowInfo");
        fd = open(flow_info_file)
        for line in fd:
            line = line.strip()
            l = line.split('\t')
            if len(l) != 2:
                print "input error", l
                continue
            self.flow_info[l[0]] = l[1]
        fd.close()
        return 0

    def load_flow_level(self):
        flow_level_file = self.config.get("email", "flowLevel");
        fd = open(flow_level_file)
        for line in fd:
            line = line.strip()
            l = line.split('\t')
            if len(l) != 4:
                print "input error", l
                continue
            key = l[0] + '\t' + l[1]
            self.flow_level[key] = flow_level(l[2], l[3])
        fd.close()
        return 0

    def load_flow_order(self):
        flow_order_file = self.config.get("email", "flowOrder");
        fd = open(flow_order_file)
        for line in fd:
            line = line.strip()
            l = line.split('\t')
            if len(l) != 8:
                print "input error", l
                continue
            orderid = l[0]
            self.flow_order[orderid] = flow_order(l[1], l[2], l[3], l[4], l[5], l[6], l[7])
        fd.close()
        return 0

    def load_flow_order_result(self):
        flow_order_result_file = self.config.get("email", "flowOrderResult");
        fd = open(flow_order_result_file)
        for line in fd:
            line = line.strip()
            l = line.split('\t')
            if len(l) != 9:
                print "input error", l
                continue

            orderInfo = ""
            orderid = l[1]
            if orderid in self.flow_order:
                orderInfo = self.flow_order[orderid]
            else:
                print "can't find orderid", orderid
                continue

            flowName = ""
            flowId = orderInfo.flow_id
            if flowId in self.flow_info:
                flowName = self.flow_info[flowId]
            else:
                print "can't find flowid", flowId
                continue

            approver = ""
            flowid_level = flowId + '\t' + l[2]
            if flowid_level in self.flow_level:
                approver = self.flow_level[flowid_level].approver
            else:
                print "can't find flowid_level", orderid_level
                continue
            orderRstInfo = flow_order_result(l[1], l[2], l[3], l[4], l[5], l[6], l[7], l[8])
            self.mail_html = self.add_reshtml_head(orderInfo.summary)
            self.mail_html += self.ps_line
            self.mail_html += self.table_head
            self.mail_html += self.add_reshtml_table(orderInfo, orderRstInfo)
            self.mail_html += self.add_reshtml_tail()
            sm.send(self.mail_html, flowName, approver, orderInfo.claimer)

        fd.close()
        return 0


    def run(self):
        if self.load_flow_info() < 0:
            print "load flow_info error"
            return -1
        if self.load_flow_level() < 0:
            print "load flow_level error"
            return -1
        if self.load_flow_order() < 0:
            print "load flow_order error"
            return -1

        if self.load_flow_order_result() < 0:
            print "load flow_order_result error"
            return -1

        return 0


    def add_reshtml_head(self, summary):
        style='''
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style> 
<!--
.list {padding: 0px; margin: 0px; border-spacing: 0px; border-collapse: collapse; width: 100%; border:1px solid #d2d6d9;}
.list th {background-color: #8EB31A; color: #fff; font-size: 14px; font-weight: bold; line-height: 20px; padding-top: 4px; border:1px solid #d2d6d9;}
.list td {border-top: 1px solid #d2d6d9; padding-top: 1px; font-size: 12px; color: #000; height: 20px; text-align: center; border-right:1px solid #8eb31a;}
.list .odd td {background-color: #FFF;}
.list .even td {background-color: #F0F9D6;}
.footer {border-top: 2px solid #8EB31A;}
span.n {color: #000000}
span.r {color: #FF0000}
span.g {color: #00AA00}
div.comment {font-size: 12px; color: #000; }
span.ct {font-weight:bold; color: #0000CC}
.list th.hover {rsor: pointer; background-color: #666;}
.list th.sorted {ckground-color: #000;}
-->
</style>
</head>
'''
        self.ps_line='''
<br><br>
'''
        title='''<h3>[Niagara]''' + summary + '''</h3>\n'''
        self.table_head = "<table border=0 width=100% class=\"list\">\n"
        self.splite_line="</table><br/>\n<table border=0 width=100% class=\"list\">\n"

        self.cstyle=("even","odd")

        head="<html>"+style+"<body>"+title
        return head+'\n'

    def add_reshtml_tail(self):
        return '<br><br><br>这是一封系统自动发送的邮件，请勿回复。<br>-------------------------------------------------</table>\n如有问题请联系buer@domob.cn\n</body>\n</html>'

    def add_th_title(self, title):
        return "<tr><th colspan='16'>"+title+"</th></tr>"

    def add_reshtml_table(self, orderInfo, orderRstInfo):
        claimer = orderInfo.claimer
        content = orderInfo.content
        level = orderRstInfo.level
        status = orderRstInfo.status
        status_rst = "未知"
        if int(status) == 22:
            status_rst = "待审核"
        elif int(status) == 23:
            status_rst = "审核通过"
        elif int(status) == 24:
            status_rst = "审核拒绝"
        audit_info = orderRstInfo.audit_info
        audit_user = orderRstInfo.audit_user
        tmp_html=""
        tmp_html += "申请者：" + claimer + "<br>" 
        tmp_html += "申请内容：" + content + "<br>" 
        tmp_html += "审核链接：http://niagara.moxz.cn/apply/show?id=" + orderRstInfo.orderid + "<br>" 
        tmp_html += "审核者：" + audit_user + "<br>" 
        tmp_html += "审核状态：" + status_rst + "<br>" 
        tmp_html += "审核信息：" + audit_info + "<br>" 

        return tmp_html

    def send(self, html, flowName, approver, claimer):
        try:
            mailServer=self.config.get('email', 'server')
            mailUserName=self.config.get('email', 'user')
            mailPassword=self.config.get('email', 'password')
            mailFrom=self.config.get('email', 'fromAdd')
            #toAdd=self.config.get('email', 'toAdd')
            emailTools = EmailTool(mailServer, mailUserName, mailPassword, mailFrom)
            subject='[Niagara]--' + flowName + '-- 审批单'
            print 'send mail to', approver, claimer
            emailTools.sendEmail(approver + ',' + claimer, subject, html)

        except Exception,why:
            print "send error",why
            return -1
        return 0


if __name__ == '__main__':
    sm=send_mail()
    if sm.run() < 0 :
        sys.exit(-1)
    sys.exit(0)
