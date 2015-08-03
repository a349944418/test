<?php
namespace Home\Controller;

Class PublicController extends CommonController {

	// 上传图片， 并压缩
	public function upload_photo(){		
		$dir = 'Userhead/';
		$width = $height = 300;
		$config = C('PICTURE_UPLOAD');
		$upload = new \Think\Upload($config);// 实例化上传类
	    $upload->rootPath  = './Uploads/'.$dir; // 设置附件上传根目录
	    $upload->subName  = ''; // 设置附件上传（子）目录
	    // 上传文件 
	    $info   =   $upload->upload();
	    if(!$info) {// 上传错误提示错误信息
	    	$data['error'] = 1;
	    	$data['errormsg'] = $upload->getError();
	        echo json_encode($data);
	    }else{// 上传成功
	    	$pic = './Uploads/'.$dir.$info['photo']['savepath'].$info['photo']['savename'];
	    	$image = new \Think\Image(); 
			$image->open($pic);
			// 生成一个缩放后填充大小300*300的缩略图并保存为thumb.jpg
			$image->thumb($width, $height,\Think\Image::IMAGE_THUMB_FILLED)->save($pic);
			$data['pic'] = __ROOT__.'/Uploads/'.$dir.$info['photo']['savepath'].$info['photo']['savename'];
	    	echo json_encode($data);
	    }
	}

	// 上传活动图片
	public function upload_Activityphoto(){
		$dir = I('post.type');
		$width_pre = 375;
		$height_pre = 250;
		$config = C('PICTURE_UPLOAD');
		$upload = new \Think\Upload($config);// 实例化上传类
	    $upload->rootPath  = './Uploads/'.$dir; // 设置附件上传根目录
	    $upload->subName  = ''; // 设置附件上传（子）目录
	    // 上传文件 
	    $info   =   $upload->upload();
	    if(!$info) {// 上传错误提示错误信息
	    	$data['error'] = 1;
	    	$data['errormsg'] = $upload->getError();
	        echo json_encode($data);
	    }else{// 上传成功
	    	$pic = './Uploads/'.$dir.$info['photo']['savepath'].$info['photo']['savename'];
	    	$image = new \Think\Image(); 
			$image->open($pic);
			
			if($image->height()/($image->width()/$width_pre) >= $height_pre){
				$width = $width_pre;
				$height = $image->height()/($image->width()/$width_pre);
			}else{
				$height = $height_pre;
				$width = $image->width()/($image->height()/$height_pre);
			}
			$image->thumb($width, $height,\Think\Image::IMAGE_THUMB_FILLED)->save($pic);
			if($height>$height_pre){
				$post['x'] = 0;
				$post['y'] = ($height-$height_pre)/2;
			}else{
				$post['x'] = ($width-$width_pre)/2;
				$post['y'] = 0;
			}
			$image->crop($width_pre, $height_pre, $post['x'], $post['y'])->save($pic);
			$data['pic'] = __ROOT__.'/Uploads/'.$dir.$info['photo']['savepath'].$info['photo']['savename'];
	    	echo json_encode($data);
	    }
	}

	// 裁剪图片
	public function jcorp(){
		$res = array('code'=>2, 'msg'=>'');
		if(IS_AJAX){
			$post = I('post.');
			$pic = __ROOT__.'.'.$post['url'];
			$image = new \Think\Image(); 
			$image->open($pic);
			$image->crop($post['w'], $post['h'], $post['x'], $post['y'])->save($pic);
			$image->thumb(100, 100,\Think\Image::IMAGE_THUMB_FILLED)->save($pic);
			$res['code'] = 1;
			$res['data'] = __ROOT__.substr($pic, 1);
			echo json_encode($res);
			die();
		}else{
			$res['msg'] = '访问出错';
			die(json_encode($res));
		}
	}

	//获取某年某月有几天
	function getday(){
		$res = array('code'=>2, 'msg'=>'');
		if(IS_AJAX){
			$y = intval(I('post.y'));
			$m = intval(I('post.m'));
			$d = cal_days_in_month(CAL_GREGORIAN,$m,$y);
			$res['con'] = '<option value="0" >请选择日期</option>';
			for($i = 1; $i<=$d; $i++){
				$res['con'] .= '<option value="'.$i.'" >'.$i.'</option>';
			} 
			die(json_encode($res));
		}else{
			$res['msg'] = '访问出错';
			die(json_encode($res));
		}
	}

	//获取城市
	function getcity(){
		$res = array('code'=>2, 'msg'=>'');
		if(IS_AJAX){
			$p = intval(I('post.p'));
			$city = M('city');
			$res['con'] = $city->field('no, areaname')->where('topno='.$p)->select();
			die(json_encode($res));
		}else{
			$res['msg'] = '访问出错';
			die(json_encode($res));
		}
	}
}

?>