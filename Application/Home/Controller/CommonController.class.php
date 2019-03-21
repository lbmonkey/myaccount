<?php

namespace Home\Controller;

use Think\Controller;

class CommonController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!session('index_user.id')) {
            $this->error('未登录', U('Authen/login'));
        }
    }
    //其他的一些公共方法

    /*
     * 得到导航菜单我的页面组的用户头部信息
     */
    public function getUserInfo()
    {
        $user_info = session('index_user');
        $user_info['age'] = ceil((time() - $user_info['create_time']) / (24 * 60 * 60));//计算注册天数
        $map['user_id'] = $user_info['id'];
        $user_info['billnum'] = M('bill')->where($map)->count();
        return $user_info;
    }
}