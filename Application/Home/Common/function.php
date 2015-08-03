<?php
// 获取活动难易程度
function getdiffculty($type) {
	switch ($type) {
		case 1: 
			$res = '简单';
			break;
		case 2: 
			$res = '中等';
			break;
		case 3: 
			$res = '困难';
			break;
	}
	return $res;
}

//获取动态信息
function getfeedinfo($type, $status) {
	$res = '';
	switch ($status) {
		case 1:
			$res = '发布';
			break;
		case 2:
			$res = '修改';
			break;
	}
	if($type == 'Blog') {
		$res .= '日志';
	}elseif($type == 'Activity') {
		$res .= '活动';
	}

	return $res;
}

//获取距离现在的时间
function gettime($time){
	return date('Y-m-d H:i:s', $time);
}
?>