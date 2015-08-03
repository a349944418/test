<?php
namespace Nav\Model;
use Think\Model;

/**
 * 获取分类内容（链接）
 */
Class HrefModel extends Model {

	/**
	 * [获取分类内容]
	 * @param  [type] $cid [分类id,可为数组]
	 * @return [type]      [description]
	 */
	public function getHref($cid) {
		if(is_array($cid)) {
			$res = $cid;
			foreach ($cid as $k=>$v){
				$res[$k]['child'] = $this -> where('cid=' . $v[cid]) -> select();
			}
		}else {
			$res = $this -> where('cid=' . $cid) -> select();
		}

		return $res;
	}
}