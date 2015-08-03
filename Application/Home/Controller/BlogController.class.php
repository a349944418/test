<?php
namespace Home\Controller;

Class BlogController extends CommonController {

	/* 默认执行 */
	protected function _initialize(){
		$operaArr = array('editblog', 'save_blog');
		if(!cookie('uid') && in_array(__ACTION__,$operaArr)){
            $this->redirect('Index/index', array(), 0);
        }
	}

	//日志首页
	public function index() {

		$blogid = (int) I('get.id');
		$blogres1 = $blogres2 = $authinfo1 = $authinfo2 = array();

		if($blogid){
			
			$this->blogid = $blogid;
			$model = M();
			$blog = M('blog');
			$bloginfo = M('bloginfo');
			// 更新点击数
			$blog->where('blogid='.$blogid)->setInc('hits',1);

			// 博客信息
			$blogres1 = $blog->where('blogid='.$blogid)->find();
			$blogres1['dateline'] = date('Y-m-d H:i:s', $blogres1['dateline']);
			$blogres2 = $bloginfo->field('message')->where('blogid='.$blogid)->find();
			$this->res = array_merge($blogres1, $blogres2);

			// 取粉丝信息
			$follow = $model -> table('tj_follow') -> field('uid') -> where('fid='.$blogres1['uid']) -> select();
			foreach($follow as $v){
				$followlist[] = $v['uid']; 
			}
			$this->followCount = count($followlist);
			$this->followflag = in_array(cookie('uid'), $followlist) ? 1 : 0;

			// 作者信息
			$authinfo1 = $model -> table('tj_user_login') -> field('username, headimg') -> where('id='.$blogres1['uid']) -> find();
			$authinfo2 = $model -> table('tj_user_info') -> field('gxqm, province, city') -> where('uid='.$blogres1['uid']) -> find();
			$this->auth = array_merge($authinfo1, $authinfo2);
			$provincename = $model -> table('tj_city') -> where('id='.$authinfo2['province']) -> getField('areaname');
			$cityname = $model -> table('tj_city') -> where('id='.$authinfo2['city']) -> getField('areaname');
			$this->authaddress = $provincename.$cityname ? $provincename.$cityname : '未知';

			// 其他博客
			$otherblog = $blog->field('title, like, blogid')->where('uid='.$blogres1['uid'].' and type=1 and blogid!='.$blogid)->order('dateline desc')->limit(5)->select();
			$this->otherblog = $otherblog;

			//评论信息
			$comment = $model -> table('tj_blogcomment') -> where('blogid='.$blogid) -> order('bcid asc') -> select();
			foreach($comment as $k=>$v){
				$tmp = array();
				$v['dateline'] = date('Y-m-d H:i:s', $v['dateline']);
				$tmp = $model -> table('tj_user_login') -> field('headimg, username') -> where('id='.$v['uid']) -> find();
				$v = array_merge($v, $tmp);
				$commentlist[] = $v;
			}
			$this->commentlist = $commentlist;
			$this->commentCount = count($commentlist);


			$this->display();
		}else{
			$this->error('您访问的页面有误');
		}		
	}

	// 添加评论
	public function addComment(){
		$return['error'] = 1;
		if(!cookie('uid')){
			$return['errormsg'] = '请登陆后再来重试';
			die(json_encode($return));
		}
		if(IS_AJAX){
			$blog = M('blog');
			$auth = $blog -> where('blogid='.I('post.id'))->getField('uid');
			if($auth != I('post.auth')){
				$return['errormsg'] = '您的操作有误，请稍后重试';
				die(json_encode($return));
			}else{
				$data['blogid'] = I('post.id');
				$data['dateline'] = time();
				$data['uid'] = cookie('uid');
				$data['con'] = I('post.con');
				$blogcomment = M('blogcomment');
				$return['bcid'] = $blogcomment -> add($data);
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

	//发布博客
	public function editblog(){
		$blogid = I('get.id') ? I('get.id') : 0;
		if($blogid){
			$bloginfo1 = $bloginfo2 = array();
 			$model = M('');
			$bloginfo1 = $model -> table('tj_blog') -> where('blogid='.$blogid) -> field('uid, title') -> find();
			if($bloginfo1['uid'] == cookie('uid')){
				$bloginfo2 = $model -> table('tj_bloginfo') -> where('blogid='.$blogid) -> field('message') -> find();
				$this->blog = array_merge($bloginfo1, $bloginfo2);
			}
		}		
		$this->blogid = $blogid;
		$this->display();
	}

	//保存博文
	public function save_blog(){
		if(I('post.Submit')){
			$post = I('post.');
			$data['uid'] = $post['uid'] = cookie('uid');
			$post['dateline'] = time();
			$post['ip'] = iptolong(get_client_ip());
			$post['uname'] = cookie('uname');
			$data['message'] = stripcslashes($_POST['editorValue']);
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
				$data['pic'] = substr($img_path, 0, $end).'thumb_'.substr($img_path, $end);
				
				$data['pic_flag'] = 1;
				$image = new \Think\Image(); 
				$image->open('..'.$img_path);

				$image->thumb($x, $y, \Think\Image::IMAGE_THUMB_FILLED)->save('..'.$data['pic']);
			}
			$htmlcon = msubstr(strip_tags($data['message']), 0, 150);
			$feedinfo = array('uid'=>$data['uid'], 'uname'=>$post['uname'], 'type'=>'Blog', 'dateline'=>$post['dateline'], 'title'=> $post['title'], 'pic'=>$data['pic'], 'data'=>$htmlcon);
			$blog = M('blog');
			$bloginfo = M('bloginfo');
			if($post['blogid']){
				$blog->save($post);
				$data['blogid'] = $post['blogid'];
				$bloginfo->save($data);
				$feedinfo['oid'] = $post['blogid'];
				$feedinfo['status'] = 2; 
			}else{
				$data['blogid'] = $blog->add($post);
				if($data['blogid']){
					$bloginfo->add($data);
					$feedinfo['oid'] = $data['blogid']; 
					$feedinfo['status'] = 1;
				}else{
					$this->error('创建失败，请稍后重试');
				}				
			}
			$feed = M('feed');
			$feed -> add($feedinfo);
			$this->redirect('User/index', array(), 0);
		}
		$this->error('您的操作有误');
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
		$blog = M('blog');
		$blogcomment = M('blogcomment');
		$res = $blogcomment -> where('bcid='.$cid) -> field('uid, blogid') -> find();
		$buid = $blog -> where('blogid='.$res['blogid']) -> getField('uid');
		if($uid == $res['uid'] || $uid == $buid){
			$blogcomment -> delete($cid);
			$return['error'] = 0;
		}else{
			$return['errormsg'] = '操作有误';
		}
		$this->ajaxReturn($return);
	}
}
?>