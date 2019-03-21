<?php
namespace Home\Controller;
use Think\Controller;
class MyselfController extends CommonController {
    /**
     * 我的--主页面
     */
   public function index(){
       $map['user_id']=array('eq',session('index_user.id'));
       $map['played']=array('eq',0);
       $map['end_time']=array('neq',0);
       $count=M('bill')->where($map)->count();
       $this->assign('reminder_num',$count);
       $this->assign('Userinfo',$this->getUserInfo());
       $this->display();
   }

   /**
    * 打卡
    */
    public function punchcard(){
        if(IS_POST){
            $user_info=session('index_user');
            //调试查出的错误
            //$tag1=ceil((time()-$user_info['last_time'])/(24*60*60));//上次签到距今多少天

            //上次签到时间小于今天的起始时间戳就可以签
            $todaytime=strtotime(date('Y-m-d'));
            if($todaytime > $user_info['last_time']){
                $info['id']=$user_info['id'];
                $user_info['usercard']=$info['usercard']=$user_info['usercard']+1;
                $user_info['last_time']=$info['last_time']=time();
                $tag2=M('user')->save($info);
                if($tag2){
                    $info['msg']='ok';
                    session('index_user',$user_info);
                }
                else
                    $info['msg']='error';
            }else{
                $info['msg']='played';//今天已经打卡
            }
            $this->ajaxReturn($info);
        }
    }

   /**
    * 使用帮助
    */
    public function help(){
        $info=M('sysmessage')->find();
        $this->assign('info',$info);
        $this->assign('Userinfo',$this->getUserInfo());
        $this->display('myself_help');
    }

   /**
    * 修改资料
    */
    public function modifyData(){
        if(IS_GET){
            $this->assign('Userinfo',$this->getUserInfo());
            $this->display('myself_upd');
        }else{
            $mycount=0;//计数用于判断是否有改变值，未改变就加1，如果总数为5就全部未改变
            $data=I('post.');
            $have=$_FILES['userpho'];//文件
            $user_info=session('index_user');
            $info['id']=$user_info['id'];
            if($have['name']){
                //有文件上传
                $path=$this->uploadpho();
                $user_info['userpho']=$info['userpho']=$path;
                session('mycount',$path);
            }else
                $mycount += 1;
            ($data['nickname'] != $user_info['nickname']) ? ($user_info['nickname']=$info['nickname']=$data['nickname']) : $mycount += 1;
            ($data['email'] != $user_info['email']) ? ($user_info['email']=$info['email']=$data['email']) : $mycount += 1;
            ($data['phone'] != $user_info['phone']) ? ($user_info['phone']=$info['phone']=$data['phone']) : $mycount += 1;
            //旧密码是否与原来一致
            (md5($data['oldpassword']) == $user_info['password']) ? ($user_info['password']=$info['password'] = md5($data['password'])) :$mycount += 1;
            if($mycount == 5){//没有任何修改的东西
                session('mycount',$mycount);
                if($data['oldpassword'] && md5($data['oldpassword']) != $user_info['password']){
                    $this->error('旧密码不正确');
                }else
                    $this->error('无修改的内容');
            }else{
                $tag=M('user')->save($info);
                if ($tag){
                    session('index_user',$user_info);
                    $this->success('资料修改成功');
                }
                else
                    $this->error('系统繁忙，请稍后重试');
            }

        }
    }
    //上传图片
    public function uploadpho(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类
        $upload->rootPath=      APP_PATH.'/Public/'; // 设置附件上传目录    // 上传单个文件
        $upload->savePath  =      './Uploads/'; // 设置附件上传目录    // 上传单个文件
        // 上传文件
        $info   =   $upload->uploadOne($_FILES['userpho']);//源码采用相对路径，必须用绝对路径
        $info['savepath']=explode('.',$info['savepath']);
        $info['savepath']=$info['savepath'][1];
        $filename = APP_PATH.'/Public'.$info['savepath'].$info['savename'];
        session('file',$info);
        $exts = $info['ext'];
        if($filename){
            //制作缩略图
            $image = new \Think\Image();
            $image->open($filename); // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
            //生成缩略图的地址
            $path1='a'.time().'.'.$exts;
            $path=APP_PATH.'Public'.$info['savepath'].$path1;
            $type=$image->thumb(150, 150,2)->save($path);
            if($type){
                unlink($filename);
                return date("Y-m-d", time()).'/'.$path1;
            }else{
                $this->error('上传图片失败');exit();
            }
        }else{
            $this->error('上传图片失败');exit();
        }
    }
   /**
    * 收支提醒
    */
    public function remender(){
        $this->assign('Userinfo',$this->getUserInfo());
        $map2['user_id']=$map1['user_id']=$map['user_id']= array('eq',session('index_user.id'));//$map1提前的应收   $map2提前的应付  $map今日提醒
        $map1['big_type']= array('eq',3);
        $map2['big_type']= array('eq',4);
        $map['big_type']=array('in','3,4');
        $map['played']=$map1['played']=$map2['played']=array('eq',0);
        //$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));//今天的起始时间戳
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $map['end_time']=array('lt',$endToday);//小于今天结束
        $map1['end_time']=array('gt',$endToday);//大于今天结束
        $map2['end_time']=array('gt',$endToday);//大于今天结束
        $bill=M('bill');
        $list=$bill->join('a_smalltype ON a_bill.small_type = a_smalltype.id')->where($map)->field('a_smalltype.name,a_bill.*')->select();
        $list1=$bill->join('a_smalltype ON a_bill.small_type = a_smalltype.id')->where($map1)->field('a_smalltype.name,a_bill.*')->select();
        $list2=$bill->join('a_smalltype ON a_bill.small_type = a_smalltype.id')->where($map2)->field('a_smalltype.name,a_bill.*')->select();
        $this->assign('list',$list);
        $this->assign('list1',$list1);
        $this->assign('list2',$list2);
        $this->display('myself_remender');
    }

    /**
     * 应收应付详情
     */
    public function details(){
        if($id=I('get.bill_id')){
            $bill_info=M('bill')->join('a_smalltype ON a_bill.small_type = a_smalltype.id')->field('a_smalltype.name,a_bill.*')->where('a_bill.id='.$id)->find();
            $this->assign('bill_info',$bill_info);
            $this->assign('Userinfo',$this->getUserInfo());
            $this->display();
        }

    }

    /**
     * 已经结算
     */
    public function played(){
        if($id=I('get.bill_id')){
            $data['id']=$id;
            $data['played']=1;
            if( M('bill')->save($data)){
                $this->success('结算成功','remender');
            }else
                $this->error('系统繁忙，请稍后重试');
        }else
            $this->error('操作有误');
    }
    /**
     * 用户反馈
     */
    public function addWords(){
        if(IS_POST){
            $data=I('post.');
            $data['user_id']=session('index_user.id');
            $data['time']=time();
            $tag=M('words')->add($data);
            if($tag)
                $this->success('留言成功');
            else
                $this->error('系统繁忙，稍后重试！');
        }else{
            $this->assign('Userinfo',$this->getUserInfo());
            $this->display('myself_addwords');
        }
    }
    //删除收支提醒里面的记账信息
    public function del_account(){
        if($id=I('get.id')){
            $tag=M('bill')->delete($id);
            if($tag)
                $this->success('删除成功','index');
            else
                $this->error('系统繁忙，稍后重试！');
        }
    }


}