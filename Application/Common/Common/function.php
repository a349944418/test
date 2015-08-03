<?php
/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}


/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * ip 转 int
 */
function iptolong($ip){
    list($a, $b, $c, $d) = split('\.', $ip);
    return (($a * 256 + $b) * 256 + $c) * 256 + $d;
}

/**
 * int 转 ip
 */
function longtoip($ip){
    $d = $ip%256;
    $a = floor($ip/(256*256*256));
    $b = floor($ip/(256*256)-$a*256);
    $c = floor($ip/256-$a*256*256-$b*256);
    return $a.'.'.$b.'.'.$c.'.'.$d;
}

/**
 * 自用
 */
function p($arr){
	echo '<pre>';
	var_dump($arr);
}

/**
 * 获取星期几
 */
function getweek($time) {
    $weekarray=array("日","一","二","三","四","五","六");
    return '周'.$weekarray[date("w",$time)];
}

?>