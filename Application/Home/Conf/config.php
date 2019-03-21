<?php
return array(
	//'配置项'=>'配置值'
    'TMPL_PARSE_STRING'  =>array(
        '__PUBLIC__' => __ROOT__.'/Public', // 更改默认的/Public 替换规则
        '__JS__'     => __ROOT__.'/Public/Home/js', // 增加新的JS类库路径替换规则
        '__CSS__'=>__ROOT__.'/Public/Home/css',
        '__IMG__'=>__ROOT__.'/Public/Home/images',
        '__PIC__'=>__ROOT__.'/Public/Home/picture',
        '__FONTS__'=>__ROOT__.'/Public/Home/fonts',
        '__UPLOADS__'=>__ROOT__.'/Application/Public/Uploads/',
    ),
    'MAIL_SMTP'   => TRUE,

    'MAIL_HOST'   => 'smtp.163.com',   //邮件发送SMTP服务器

    'MAIL_SMTPAUTH' => TRUE,

    'MAIL_USERNAME' => '15775960380@163.com',  //SMTP服务器登陆用户名

    'MAIL_PASSWORD' => 'luogen1997511',    //SMTP服务器登陆密码

    'MAIL_SECURE'   => 'tls',

    'MAIL_CHARSET'  => 'utf-8',

    'MAIL_ISHTML'   => TRUE,

    'MAIL_FROMNAME' => '记账通验证码',

);