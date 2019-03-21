<?php
namespace Home\Controller;
use Think\Controller;
class AccountController extends CommonController {
   public function index(){
       if(IS_GET){
           $smalltype=M('smalltype');
           $in_list=$smalltype->where("bigtype=1")->select();
           $out_list=$smalltype->where("bigtype=2")->select();
           $this->assign('Userinfo',$this->getUserInfo());
           $this->assign('inlist',$in_list);
           $this->assign('outlist',$out_list);
           $this->display();
       }else{
           $data=I('post.');
           $data['create_time']=time();
           $data['end_time']=strtotime($data['end_time']);
           $data['user_id']=session('index_user.id');
           $info=M('bill')->add($data);
           if($info){
               $this->redirect('Index/index');
           }
           else
               $this->error('记账失败，请再次编写!');
       }
   }

}