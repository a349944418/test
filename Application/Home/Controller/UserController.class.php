<?php
namespace Home\Controller;

Class UserController extends CommonController {

	/* 默认执行 */
	protected function _initialize(){
		if(!cookie('uid')){
            $this->redirect('Index/index', array(), 0);
        }
	}

	// 个人中心
	public function center(){
		$model = M();
		$uid = cookie('uid');
		//动态
		$table = "tj_feed AS a LEFT JOIN tj_follow AS b ON a.uid=b.fid ";
		$feedlist = $model->table($table)->field('a.tf_id')->where('b.uid='.$uid)->order('a.tf_id DESC')->limit(30)->select();
		foreach($feedlist as $v){
			$feedlist_ids[] = $v['tf_id'];
			$feedres = $model->table('tj_feed')->where('tf_id='.$v['tf_id'])->find();
			$feedres1 = $model->table('tj_user_login')->field('username, headimg')->where('id='.$feedres['uid'])->find();
			$feedres['feedstatus'] = getfeedinfo($feedres['type'], $feedres['status'] );
			$feed[] = array_merge($feedres, $feedres1);
		}
		$this->feed = $feed;
		$this->display();
	}

	//发心情
	public function doing(){
		$doid = I('get.id') ? I('get.id') : 0;
		$this->doid = $doid;
		$this->display();
	}

	//保存心情
	public function save_doing(){
		if(I('post.sub')){
			$doid = I('post.doid') ? I('post.doid') : 0;
			$post = I('post.');
			$post['uid'] = cookie('uid');
			$post['dateline'] = time();
			$post['ip'] = iptolong(get_client_ip());
			$post['uname'] = cookie('uname');
			$doing = M('doing');
			if($doid){
				$doing -> save($post);
			}else{
				unset($post['doid']);
				$doing -> add($post);
			}
			$this->redirect('User/index', array(), 0);
		}
		$this->error('您的操作有误');
	}

	

	//发布游记
	public function travelnote(){
		$blogid = I('get.id') ? I('get.id') : 0;
		$this->blogid = $blogid;
		$this->display();
	}

	public function save_travelnote(){
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

			$blog = M('blog');
			$bloginfo = M('bloginfo');
			if($post['blogid']){
				$blog->save($post);
				$data['blogid'] = $post['blogid'];
				$bloginfo->save($data);
			}else{
				$data['blogid'] = $blog->add($post);
				if($data['blogid']){
					$bloginfo->add($data);
				}else{
					$this->error('创建失败，请稍后重试');
				}				
			}
			$this->redirect('User/index', array(), 0);
		}
		$this->error('您的操作有误');
	}

	public function addpeople(){
		echo 123;
	}
} 

?>