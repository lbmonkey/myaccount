<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extends Controller {
    public function login(){
        if(IS_POST){
            $where['name']=I('post.name');
            $where['password']=md5(I('post.password'));
            $info=M('admin')->where($where)->find();
            if($info){
                session('admin_user',$info);
                $this->redirect('Index/index');
            }else{
                $this->error('账号或密码错误！');
            }
        }else{
            $this->display();
        }
    }
    public function logout(){
        session('admin_user',null);
        $this->redirect('Admin/login');
    }
}