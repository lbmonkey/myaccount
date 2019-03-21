<?php
return array(
	//'配置项'=>'配置值'
    'TMPL_PARSE_STRING'  =>array(
        '__PUBLIC__' => __ROOT__.'/Public', // 更改默认的/Public 替换规则
        '__JS__'     => __ROOT__.'/Public/Admin/js', // 增加新的JS类库路径替换规则
        '__CSS__'=>__ROOT__.'/Public/Admin/css',
        '__IMG__'=>__ROOT__.'/Public/Admin/img',
        '__UPLOADS__'=>__ROOT__.'/Application/Public/Uploads/',
    ),
    'PAGENUM'=>'6',
);
