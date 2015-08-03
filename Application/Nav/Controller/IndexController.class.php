<?php
namespace Nav\Controller;
use Think\Controller;
class IndexController extends Controller {
	/**
	 * [导航显示页]
	 * @return [type] [description]
	 */
    public function index() {
        $user = I('get.u');
        if($user){
            $uid = D('user') -> getUid($user);
        }
    	$uid  = $uid ? $uid : 1; 
    	$res  = D('cat') -> getCat($uid);
    	$list = D('href') -> getHref($res);
    	$this -> assign('list', $list);
        $this -> assign('uid', $uid);
        $this -> display();
    }

    /**
     * [登录]
     * @return [type] [description]
     */
    public function load() {
        $post = I('post.');
        $res  = D('user') -> checkLoad($post);
        if($res) {
            $this -> redirect('Nav/Index/index', array('u'=>$post['user']), 0);
        } else {
            $this -> error ('账号或密码有误');
        }        
    }

    /**
     * [退出登录]
     */
    public function Logout() {
        $user = session('user');
        session(NULL);
        $this -> redirect('Nav/Index/index', array('u'=>$user), 0);
    }
}