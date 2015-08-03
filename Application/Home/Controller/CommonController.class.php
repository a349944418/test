<?php
namespace Home\Controller;
use Think\Controller;
Class CommonController extends Controller {
	
	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}

	/* 默认执行 */
	protected function _initialize(){
		$verify_show = C('VERIFY_SHOW');
		$this->assign('verify_show', $verify_show);

		if(C('WEB_SITE_CLOSE')){
            $this->error('站点已经关闭，请稍后访问~');
        }
	}
	
} 
?>