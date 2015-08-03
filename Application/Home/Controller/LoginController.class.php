<?php
namespace Home\Controller;

Class LoginController extends CommonController {

	//注册
	public function reg(){
		if(cookie('uid')){
			$this->error('您已处于登录状态，请退出后再重新注册');
		}else{
			$this->display();			
		}
	}

	//登陆
	public function login(){
		if(cookie('uid')){
			$this->error('您已处于登录状态，不能重复登录');
		}else{
			$this->display();			
		}
	}

	//退出
	public function logout(){
		unset($_COOKIE['uid']);
		cookie('uid', null);
		cookie('uname', null);
		cookie('headimg', null);
		$this->redirect('Index/index', array(), 0);
	}

	public function loginVerify(){
		if(IS_POST){
			$post = I('post.');
			if(!check_verify($post['verify'])){
				$this->error('验证码输入错误！');
			}
			/* 检测密码 */
			$data['password'] = md5($post['password']);
			$data['email'] = $post['email'];
			$UserLog = M('UserLogin');
			$userinfo = $UserLog->field('id, headimg, username')->where($data)->find();
			if($userinfo){
				cookie('uid', $userinfo['id']);
				cookie('uname', $userinfo['username']);
				if($userinfo['headimg']){
					cookie('headimg', $userinfo['headimg']);
				}else{
					cookie('headimg', 'default_head.jpg');
				}
				$this->redirect('Index/index', array(), 0);
			}else{
				$this->error('邮箱或密码错误');
			}
		}else{
			$this->error('您访问的页面有误');
		}
		
	}

	//注册处理一
	public function register(){
		if(IS_POST){
			$post = I('post.');

			if(!check_verify($post['verify'])){
				$this->error('验证码输入错误！');
			}

			if($post['password'] != $post['password_confirm']){
				$this->error('密码和重复密码不一致！');
			}

			if (!ereg("^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+",$post['email'])) {
				$this->error('邮箱格式不正确');
			}

			$UserLog = M('UserLogin');
			$uid = $UserLog->field('id')->where('email="'.$post['email'].'"')->find();
			if($uid){
				$this->error('您输入的邮箱已被注册!');
			}

			$post['reg_time'] = $post['last_login_time'] = time();
			$post['reg_ip'] = $post['last_login_ip'] = iptolong(get_client_ip());
			$post['password'] = md5($post['password']);
			$post['status'] = 1;
			$UserLog->create($post);
			$uid = $UserLog->add();
			if($uid){
				cookie('cache_id', $uid);
				cookie('em', $post['email']);
				
				$emailurl = preg_replace("/^([a-zA-Z0-9_-])+@(([a-zA-Z0-9_-])+)\.([a-zA-Z0-9_-])+/","http://mail.\\2.com",$post['email']);

				//邮箱验证码
				$mail_verify = $uid.'_'.substr(md5($post['email']),0,5);
				$mail_verify_url = $_SERVER['HTTP_HOST'].U('Login/email_verify','mail_verify='.$mail_verify);
				$this->assign('email',$post['email']);
				$this->assign('emailurl',$emailurl);
				$this->assign('mail_verify_url',$mail_verify_url);
				$mail_con = $this->fetch('emailverifycon');

				//发送邮件
				load('@.function_mail');
				$mail_res = sendmail($post['email'], '[途经网]邮箱验证', $mail_con);
				if($mail_res) {
					$this->display();
				} else {
					$this->error('邮件发送失败，请联系客服进行问题反馈', U('Login/login'));
				}
			}else{
				$this->error('操作失败，请稍后重试！');
			}
		}else{
			$this->error('您访问的页面有误');
		}
	}

	/* 验证码，用于登录和注册 */
	public function verify(){
		$verify = new \Think\Verify();
		$verify->entry(1);
	}

	//邮件验证
	public function email_verify(){
		$mail_verify = I('get.mail_verify');
		if(!$mail_verify){
			$this->error('您访问的页面有误', U('Index/index'));
		}

		$arr = explode('_', $mail_verify);
		$uid = $arr[0];
		$UserLog = M('UserLogin');
		$res = $UserLog->field('email, status')->where('id="'.$uid.'"')->find();
		// if($res['status'] != 1){
		// 	$this->error('您的验证链接已失效', U('Index/index'));
		// }

		$verify = substr(md5($res['email']),0,5);

		// if($verify != $arr[1]){
		// 	$this->error('您的验证链接有误，请重新发送邮件进行验证', U('Index/index'));
		// }
		//验证成功,更新状态
		// $data = array('status'=>2);
		// $saveRes = $UserLog->where('id="'.$uid.'"')->save($data);
		// cookie('uid', $uid);
		// cookie('em', $res['email']);
		// if(!$saveRes){
		// 	$this->error('操作失败，请重新发送邮件进行验证', U('Index/index'));
		// }

		//取省份赋值
		$city = M('city');
		$province = $city->field('areaname, no')->where('topno=0')->select();
		$this->province = $province;

		$this->display();
	}

	//注册处理二
	public function register2(){
		$post = I('post.');
		$uid = cookie('uid');
		//更新头像
		$UserLog = M('UserLogin');		
		if(!$post['headimg']){
			$data['headimg'] = 'default_head.jpg';
		}else{
			$headimg_start = (strrpos($post['headimg'], '/') + 1 );
			$data['headimg'] = substr($post['headimg'], $headimg_start);
		}
		$UserLog -> where('id="'.$uid.'"') -> data($data) -> save();
		$uname = $UserLog->where('id="'.$uid.'"')->getField('username');
		cookie('uname', $uname);
		cookie('headimg', $data['headimg']);
		//新增个人信息
		$post['uid'] = $uid;
		$UserInfo = M('UserInfo');
		$id = $UserInfo -> add($post);
		if($id) {
			$this->redirect('Index/index', array(), 0);
		}else{
			$this->error('服务器出错，请稍后重试');
		}
	}
} 
