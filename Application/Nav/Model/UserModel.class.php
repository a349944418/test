<?php
namespace Nav\Model;
use Think\Model;

Class UserModel extends Model {

	/**
	 * [获取 用户 id]
	 * @param  [type] $user [用户名]
	 * @return [type]       [uid]
	 */
	public function getUid($user) {
		$uid = $this -> where('user="'.$user.'"') -> getField('uid');
		return $uid;
	}

	/**
	 * [登录信息检测]
	 * @param  [array] $post [用户名，密码]
	 * @return [boolen]      [description]
	 */
	public function checkLoad($post) {
		$uid = $this -> where('user="'.$post['user'].'" and pwd="'.md5($post['pwd']).'"') -> getField('uid');
        if($uid){
            SESSION('uid', $uid);
            SESSION('user', $post[user]);
            $res = true;
        } else {
        	$res = false;
        }
        return $res;
	}
}
?>