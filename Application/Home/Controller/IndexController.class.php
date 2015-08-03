<?php
namespace Home\Controller;

class IndexController extends CommonController {

    public function index(){
    	$model = M();
    	// 活动
    	$thistime = time();
    	$activity = $model->table('tj_activity AS a LEFT JOIN tj_activityinfo AS b ON a.aid=b.aid')->field('a.uid, b.active_pic, b.message, a.title, a.aid, a.hits, a.people, a.starttime')->where('b.active_pic != "" and a.deadline > '.$thistime)->order('a.hits desc')->limit(6)->select();
    	foreach($activity as $k => $v){
    		$activity[$k]['headimg'] = $model->table('tj_user_login')->where('id='.$v['uid'])->getField('headimg');
    		$activity[$k]['starttime'] = date('Y-m-d', $v['starttime']);
    	}
    	$this->activity = $activity;

    	// 游记
    	$travelNote = $model->table('tj_blog AS a LEFT JOIN tj_bloginfo AS b ON a.blogid=b.blogid')->field('a.uid, b.pic, b.message, a.title, a.blogid')->where('a.pic_flag = 1')->order('a.hits desc')->limit(3)->select();
    	foreach($travelNote as $k => $v){
    		$travelNote[$k]['message'] = msubstr(strip_tags($v['message']), 0, 150);
    		$travelNote[$k]['pic'] = str_replace('thumb_', '', $v['pic']);
    		$travelNote[$k]['headimg'] = $model->table('tj_user_login')->where('id='.$v['uid'])->getField('headimg');
    	}
    	$this->travelNote = $travelNote;
    	$this->display();
    }
}
?>