<?php
$connect = new Memcached;  //声明一个新的memcached链接
$connect->setOption(Memcached::OPT_COMPRESSION, false); //关闭压缩功能
$connect->setOption(Memcached::OPT_BINARY_PROTOCOL, true); //使用binary二进制协议
$connect->addServer('a74156bcc6964305.m.cnqdalicm9pub001.ocs.aliyuncs.com', 11211); //添加OCS实例地址及端口号
//$connect->setSaslAuthData('a74156bcc6964305', '1Zhouboqi'); //设置OCS帐号密码进行鉴权，如已开启免密码功能，则无需此步骤
$connect->set("hello", "world");
echo 'hello: ',$connect->get("hello");
$connect->quit();
?>