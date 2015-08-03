<?php
return array(
	'URL_MODEL'	=> 1,
	// 'SHOW_PAGE_TRACE' =>true,

	'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址
    'DB_NAME'               =>  'tujing',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  'a7234738',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'tj_',    // 数据库表前缀

    //邮件配置
	'THINK_EMAIL' => array(
	    'SMTP_HOST'   => 'smtp.163.com', //SMTP服务器
	    'SMTP_PORT'   => '25', //SMTP服务器端口
	    'SMTP_USER'   => '1jingzhongren@163.com', //SMTP服务器用户名
	    'SMTP_PASS'   => '1Zhouboqi', //SMTP服务器密码
	    'FROM_EMAIL'  => '1jingzhongren@163.com', //发件人EMAIL
	    'FROM_NAME'   => '途经', //发件人名称
	    'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
	    'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）
	),

	'SITENAME' => '途经',
);