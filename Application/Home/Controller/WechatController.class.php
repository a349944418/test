<?php
namespace Home\Controller;

/**
 * 微信公共号接口验证
 */
Class WechatController extends CommonController {

	private $token = 'tujing';
	private $signature;
	private $timestamp;
	private $nonce;

	public function valid() {
        $echoStr = I('get.echostr');
        $this->signature = I('get.signature');
        $this->timestamp = I('get.timestamp');
        $this->nonce = I('get.nonce');

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    /**
     * 检测微信回调
     */
    private function checkSignature() {
        
		$tmpArr = array($this->token, $this->timestamp, $this->nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $this->signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>