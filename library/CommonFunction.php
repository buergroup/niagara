<?php
/**
 * Replace audit status to text
 */
defined('AUDIT_AUDITING') or define('AUDIT_AUDITING', 22);
defined('AUDIT_ACCEPTED') or define('AUDIT_ACCEPTED', 23);
defined('AUDIT_REJECTED') or define('AUDIT_REJECTED', 24);
function GetAuditStatus($status){
	$map = array(
		AUDIT_AUDITING => '待审核',
		AUDIT_AUDITING => '已通过',
		AUDIT_REJECTED => '已拒绝',
	);
	if(isset($map[$status])){
		return $map[$status];
	}
	return '未知';
}