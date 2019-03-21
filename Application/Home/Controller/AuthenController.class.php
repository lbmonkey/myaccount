<?php
namespace Home\Controller;
use Think\Controller;
class AuthenController extends Controller {
    /*
     * 用户登录
     */
   public function login(){
       if(IS_GET){
           $this->display();
       }else{
           $where['nickname']=I('post.nickname');
           $where['password']=md5(I('post.password'));
           $info=M('user')->where($where)->find();
           if($info){
               session('index_user',$info);
               $this->redirect('Index/index');
           }else{
               $this->error('账号或密码错误！');
           }
       }

   }
   /*
    * 注销
    */
   public function logout(){
       session('index_user',null);
       $this->redirect('login');
   }

   /*
    * 用户注册
    */
   public function regist(){
       if(IS_GET){
           $this->display();
       }else{
            $data=I('post.');
            $data['password']=md5($data['password']);
            $data['create_time']=time();
            $info=M('user')->add($data);
            if($info){
                $data['last_time']=0;
                $data['id']=$info;//$info为主键id
                $data['usercard']=0;//$info为主键id
                session('index_user',$data);
                $this->success('注册成功',U('Index/index'));
            }
            else
                $this->error('注册失败');
       }
   }
   public function reset_password(){
       if(IS_POST){
            $data=I('post.');
            if($data['code']==session('reset_info.code')){
                $mydata['id']=session('reset_info.id');
                $mydata['password']=md5($data['password']);
                if(M('user')->save($mydata)){
                    $this->success('重置成功!','login');
                }
            }else{
                $this->error('验证码不正确!');
            }
       }else{
           $this->display('reset');
       }
   }

   /**
    * 发送邮件
    */
   public function sendEmail(){
       if(IS_POST){
           $data['email']=I('post.email');
           $user_info=M('user')->field('id')->where($data)->find();
           $data['id']=$user_info['id'];
           if($data['id']){
               //获取随机验证码
               $data['code']=rand_captcha();
               if(sendMail($data['email'],'记账通重置密码验证码','尊敬的用户您好！记账通验证码为：'.$data['code'].'。2分钟后失效！')){
                   session('reset_info',$data);
                   $info['msg']='请查收邮件！';
               }else{
                   $info['msg']='邮箱不存在！';
               }
           }else{
               $info['msg']='该用户邮箱不存在！';
           }
           $this->ajaxReturn($info);
       }
   }
   public function test(){

       dump(session('reset_info'));
   }
}