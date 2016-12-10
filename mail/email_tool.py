# -*- coding: utf-8 -*-
#
# email_tool.py
# 
#  Created on: 2013-08-06
#      Author: czm1989@gmail.com
#

import sys
sys.path.append('/usr/local/lib/python2.7/site-packages')
from email.MIMEMultipart import MIMEMultipart
from email.MIMEText import MIMEText
import smtplib
import time
from domob_pyutils.email_tools import SmtpEmailTool

class EmailTool:

	def __init__(self, server, username, password, from_address):
		self.server = server
		self.username = username
		self.password = password
		self.from_address = from_address

	def sendEmail(self, to_address, mail_title, mail_body, retry_count = 3):
		ret = False
		while retry_count > 0:
			try:
				emailTools = SmtpEmailTool(self.server, self.username, self.password)
				emailTools.sendEmail(to_address.strip().split(','), mail_title, mail_body)
				ret = True
				break
			except Exception, e:
				print str(e)
				retry_count -= 1
				if retry_count > 0:
					time.sleep(5)
		return ret

#		msg_root = MIMEMultipart('related')
#		msg_root['Subject'] = mail_title
#		msg_root['From'] = self.from_address
#		msg_root['To'] = to_address
#		msg_root.preamble = 'This is a multi-part message in MIME format.'
#
#		msg_alternative = MIMEMultipart('alternative')
#		msg_root.attach(msg_alternative)
#
#		msg_text = MIMEText(mail_body, 'html', 'utf-8')
#		msg_alternative.attach(msg_text)
#
#		ret = False
#		while retry_count > 0:
#			try:
#				smtp = smtplib.SMTP()
#				smtp.connect(self.server)
#				smtp.login(self.username, self.password)
#				smtp.sendmail(self.from_address, to_address.strip().split(','), msg_root.as_string())
#				ret = True
#				break
#			except Exception, e:
#				print str(e)
#				retry_count -= 1
#				if retry_count > 0:
#					time.sleep(5)
#			finally:
#				try: smtp.quit()
#				except: pass
#		return ret

