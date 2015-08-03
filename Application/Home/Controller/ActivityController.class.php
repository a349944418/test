<?php
namespace Home\Controller;
Class ActivityController Extends CommonController {
	/* 默认执行 */
	protected function _initialize(){
		$operaArr = array('edit', 'save', 'order');
		if(!cookie('uid') && in_array(ACTION_NAME,$operaArr)){
            $this->redirect('Login/login', array(), 0);
        }
	}

	function index(){
		$id = (int) I('get.id');
		if(!$id){
			$this->error('您的访问有误');
		}else{
			$model = M('');
			$activityinfo = $activityinfo1 = array();
			//活动信息
			$model->table('tj_activity')->where('aid='.$id)->setInc('hits',1);
			$activityinfo = $model->table('tj_activity')->where('aid='.$id)->find();
			$activityinfo1 = $model->table('tj_activityinfo')->field('message, active_pic')->where('aid='.$id)->find();
			$activityinfo['type'] = $model->table('tj_activity_type')->where('atid='.$activityinfo['atid'])->getField('name');
			$activityinfo = array_merge($activityinfo, $activityinfo1);
			$activityinfo['difficulty'] = getdiffculty($activityinfo['difficulty']);
			$activityinfo['properties'] = $activityinfo['properties'] == 1 ? 'AA' : '商业';

			$this->activity = $activityinfo;

			// 作者信息
			$authinfo1 = $model -> table('tj_user_login') -> field('username, headimg') -> where('id='.$activityinfo['uid']) -> find();
			$authinfo2 = $model -> table('tj_user_info') -> field('gxqm, province, city') -> where('uid='.$activityinfo['uid']) -> find();
			$this->auth = array_merge($authinfo1, $authinfo2);
			$provincename = $model -> table('tj_city') -> where('id='.$authinfo2['province']) -> getField('areaname');
			$cityname = $model -> table('tj_city') -> where('id='.$authinfo2['city']) -> getField('areaname');
			$this->authaddress = $provincename.$cityname ? $provincename.$cityname : '未知';

			//评论信息
			$comment = $model -> table('tj_activitycomment') -> where('aid='.$id) -> order('acid asc') -> select();
			foreach($comment as $k=>$v){
				$tmp = array();
				$v['dateline'] = date('Y-m-d H:i:s', $v['dateline']);
				$tmp = $model -> table('tj_user_login') -> field('headimg, username') -> where('id='.$v['uid']) -> find();
				$v = array_merge($v, $tmp);
				$commentlist[] = $v;
			}
			$this->commentlist = $commentlist;
			$this->commentCount = count($commentlist);
			
		}

		$this->display();
	}

	// 发布活动
	public function edit(){
		$aid = I('get.id') ? I('get.id') : 0;
		$model = M();
		if($aid){
			$res = $model->table('tj_activity as a left join tj_activityinfo as b on a.aid=b.aid')->where('a.aid='.$aid)->find();
			$time = date('Y/m/d H:i', $res['starttime']).' - '.date('Y/m/d H:i', $res['endtime']);
			$deadline = date('Y/m/d H:i', $res['deadline']);
			$this->res = $res;
		}else{
			$time = date('Y/m/d', strtotime('+1 day')).' 8:00 - '.date('Y/m/d', strtotime('+3 day')).' 23:59';
			$deadline = date('Y/m/d').' 23:59';
		}
		
		$act_type_list = $model->table('tj_activity_type')->where('status=1')->select();		

		$this->aid = $aid;
		$this->time = $time;
		$this->deadline = $deadline;
		$this->act_type_list = $act_type_list;
		$this->display();
	}

	//保存活动
	public function save(){
		if(I('post.Submit')){
			$post = I('post.');
			$timeArr = explode(' - ', $post['time']);
			$data['uid'] = $post['uid'] = cookie('uid');
			$post['dateline'] = time();
			$post['ip'] = iptolong(get_client_ip());
			$post['uname'] = cookie('uname');
			$post['deadline'] = strtotime($post['deadline']);
			$post['starttime'] = strtotime(trim($timeArr[0]));
			$post['endtime'] = strtotime(trim($timeArr[1]));
			$post['day'] = ceil(($post['endtime']-$post['starttime'])/86400);
			$data['message'] = stripcslashes($_POST['editorValue']);
			if(!$post['active_pic']){
				preg_match_all("/<img.*?>/im",$data['message'],$ereg);
				$img=$ereg[0][0];//图片
				$p="#src=('|\")(.*)('|\")#isU";//正则表达式
				preg_match_all ($p, $img, $img1);
				$img_path =$img1[2][0];//获取第一张图片路径
				if(!$img_path){
					$post['pic_flag'] = 0;
				}else{
					$post['pic_flag'] = 1;
					$info = getimagesize(I('server.DOCUMENT_ROOT').$img_path);
					if($info[1]>$info[0]){
						$y = 140;
						$x = ceil(140*$info[0]/$info[1]);
					}else{
						$x = 140;
						$y = ceil(140*$info[1]/$info[0]);
					}
					$end = strrpos($img_path, '/')+1;
					$data['active_pic'] = substr($img_path, 0, $end).'thumb_'.substr($img_path, $end);
					
					$image = new \Think\Image(); 
					$image->open('..'.$img_path);

					$image->thumb($x, $y, \Think\Image::IMAGE_THUMB_FILLED)->save('..'.$data['active_pic']);
				}
			}else{
				$data['active_pic'] = $post['active_pic'];
				$post['pic_flag'] = 1;
			}
			$htmlcon = msubstr(strip_tags($data['message']), 0, 150);
			$feedinfo = array('uid'=>$data['uid'], 'uname'=>$post['uname'], 'type'=>'Activity', 'dateline'=>$post['dateline'], 'title'=> $post['title'], 'pic'=>$data['active_pic'], 'data'=>$htmlcon);
			$activity = M('activity');
			$activityinfo = M('activityinfo');
			if($post['aid']){
				$activity->save($post);
				$data['aid'] = $post['aid'];
				$activityinfo->save($data);
				$feedinfo['oid'] = $post['aid'];
				$feedinfo['status'] = 2; 
			}else{
				$data['aid'] = $activity->add($post);
				if($data['aid']){
					$activityinfo->add($data);
					$feedinfo['oid'] = $data['aid']; 
					$feedinfo['status'] = 1;
				}else{
					$this->error('创建失败，请稍后重试');
				}				
			}
			$feed = M('feed');
			$feed -> add($feedinfo);
			$id = $post['aid'] ? $post['aid'] : $data['aid'];
			$this->redirect('Activity/index', array('id'=>$id), 0);
		}
		$this->error('您的操作有误');
	}

	// 添加评论
	public function addComment(){
		$return['error'] = 1;
		if(!cookie('uid')){
			$return['errormsg'] = '请登陆后再来重试';
			die(json_encode($return));
		}
		if(IS_AJAX){
			$Activity = M('Activity');
			$auth = $Activity -> where('aid='.I('post.id'))->getField('uid');
			if($auth != I('post.auth')){
				$return['errormsg'] = '您的操作有误，请稍后重试';
				die(json_encode($return));
			}else{
				$data['aid'] = I('post.id');
				$data['dateline'] = time();
				$data['uid'] = cookie('uid');
				$data['con'] = I('post.con');
				$Activitycomment = M('Activitycomment');
				$return['acid'] = $Activitycomment -> add($data);
				$return['error'] = 0;
				$return['url'] = U('User/index', array('id'=>cookie('uid')));
				$return['uname'] = cookie('uname');
				$return['pic'] = __ROOT__.'/Uploads/Userhead/'.cookie('headimg');
				die(json_encode($return));
			}
		}else{
			$return['errormsg'] = '您的操作有误，请稍后重试';
			die(json_encode($return));
		}
	}

	// 删除博客评论
	public function cdel(){
		$return['error'] = 1;
		if(!cookie('uid')){
			$return['errormsg'] = '请登陆后再来重试';
			die(json_encode($return));
		}
		$cid = I('post.cid');
		$uid = cookie('uid');
		$Activity = M('Activity');
		$Activitycomment = M('Activitycomment');
		$res = $Activitycomment -> where('acid='.$cid) -> field('uid, aid') -> find();
		$buid = $Activity -> where('aid='.$res['aid']) -> getField('uid');
		if($uid == $res['uid'] || $uid == $buid){
			$Activitycomment -> delete($cid);
			$return['error'] = 0;
		}else{
			$return['errormsg'] = '操作有误';
		}
		$this->ajaxReturn($return);
	}

	//活动报名
	public function order(){
		$id = I('get.id');
		if(!$id){
			$this->error('您的访问有误');
		}else{
			$model = M();
			$activity = $model->table('tj_activity')->where('aid='.$id)->find();
			if(!$activity){
				$this->error('您的访问有误');
			}else{
				$member = $model->table('tj_user_member')->field('umid, name, phone, idcard')->where('uid='.cookie('uid'))->select();
				$this->activity = $activity;
				$this->member = $member;
			}
		}

		$this->display();
	}

	//添加用户
	public function addmem(){
		$return = array();
		if(!IS_AJAX && !cookie('uid')){
			$return['error'] = 1;
			$return['errormsg'] = '请登录后再来访问';
		}else{
			$return['con'] = $this->fetch();
		}
		$this->ajaxReturn($return);
	}

	//保存用户
	public function savemem(){
		$return = array('error'=>1);
		if(!IS_AJAX && !cookie('uid')){
			$return['errormsg'] = '请登录后再来访问';
		}else{
			$userMem = M('userMember');
			$post['idcard'] = I('post.pno');
			$post['uid'] = cookie('uid');
			$umid = $userMem -> where('uid='.$post['uid'].' and idcard="'.$post['idcard'].'"') -> getField('umid');
			if($umid){
				$return['errormsg'] = '该身份证号用户已保存过，不能重复创建';
			}else{
				$post['sex'] = I('post.psex');
				$post['name'] = I('post.pname');
				$post['phone'] = I('post.pmobile');
				$post['birth'] = I('post.pbirth');
				$umid = $userMem->add($post);
				if(!$umid){
					$return['errormsg'] = '创建失败，请稍后重试';
				}else{
					$return['error'] = 0;
					$return['umid'] = $umid;
					$return['name'] = $post['name'];
					$return['phone'] = $post['phone'];
					$return['pno'] = $post['idcard'];
				}
			}
		}
		$this->ajaxReturn($return);
	}
}
?>