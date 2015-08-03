<?php
namespace Home\Controller;

Class FollowController Extends CommonController {

	// 关注
	public function dofollow() {
		$return['error'] = 1;
		if(IS_AJAX){
			$follow = M('follow');
			$data['uid'] = cookie('uid');
			$data['fid'] = I('post.fid');
			$data['ctime'] = time();

			$follow_id = $follow -> where('uid='.$data['uid'].' and fid='.$data['fid']) -> getField('follow_id');
			if(!$follow_id && $data['fid']!=$data['uid']){
				$follow_id = $follow -> add($data);
				if($follow_id){
					$return['error'] = 0;
				}else{
					$return['errormsg'] = '操作失败，请稍后重试';
				}
			}else{
				$return['errormsg'] = '已关注，不能重复关注';
			}
		}else{
			$return['errormsg'] = '您的操作有误';
		}

		$this->ajaxReturn($return);
	}

	// 取消关注
	public function unfollow() {
		$return['error'] = 1;
		if(IS_AJAX){
			$follow = M('follow');
			$data['uid'] = cookie('uid');
			$data['fid'] = I('post.fid');

			$follow_id = $follow -> where('uid='.$data['uid'].' and fid='.$data['fid']) -> getField('follow_id');
			if($follow_id){
				$res = $follow -> delete($follow_id);
				if($res){
					$return['error'] = 0;
				}else{
					$return['errormsg'] = '操作失败，请稍后重试';
				}
			}else{
				$return['errormsg'] = '您的操作有误';
			}
		}else{
			$return['errormsg'] = '您的操作有误';
		}

		$this->ajaxReturn($return);
	}
}
?>