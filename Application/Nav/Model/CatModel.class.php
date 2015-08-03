<?php
namespace Nav\Model;
use Think\Model;

/**
 * 分类模型
 */
class CatModel extends Model {

	/**
	 * [获取分类信息]
	 * @param  [type] $uid [用户id]
	 * @return [type]      [分类信息]
	 */
	public function getCat($uid) {
		$data['uid'] = $uid;
		return $this->where($data)->select();
	}
}

?>