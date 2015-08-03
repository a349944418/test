<?php
namespace Home\Controller;

Class SetController extends CommonController {

	public function email(){
		$uid = cookie('cache_id');
		$email = cookie('em');
		
		$mail_verify = $uid.'_'.substr(md5($email),0,5);
		$mail_verify_url = $_SERVER['HTTP_HOST'].U('Login/email_verify','mail_verify='.$mail_verify);
		$this->assign('email',$email);
		$this->assign('emailurl',$emailurl);
		$this->assign('mail_verify_url',$mail_verify_url);
		$mail_con = $this->fetch('Login/emailverifycon');

		//发送邮件
		load('@.function_mail');
		$mail_res = sendmail($email, '[途经网]邮箱验证', $mail_con);
		if($mail_res){
			echo json_encode(1);
		}else{
			echo 2;
		}
	}
}
?>