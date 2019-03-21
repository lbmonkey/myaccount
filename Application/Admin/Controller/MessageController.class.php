<?php
namespace Admin\Controller;
use Think\Controller;
class MessageController extends Controller {
    protected function _initialize(){
        if(!is_login()){
            $this->redirect('Admin/login');
        }
    }

    //展示用户留言
    public function show_words(){
        $Words = M('words'); // 实例化User对象
        $count = $Words->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(2)
        $show = $Page->show();// 分页显示输出 // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $words_list = $Words->order('time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('words_list',$words_list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display();

    }
    //删除用户留言
    public function delete_words(){
        $tag=M('words')->delete(I('get.id'));
        if($tag){
            $this->success('删除成功！');
        }
        else
            $this->error('系统繁忙，稍后重试！');
    }
    //用户留言详情
    public function words_details(){
        if($id=I('get.id')){
            $info=M('words')->join('a_user ON a_words.user_id=a_user.id')->where('a_words.id='.$id)->find();
            $this->assign('info',$info);
            $this->display();
        }
    }

    //编辑软件信息与使用帮助
    public function edit_help(){
        if(IS_POST){
            $data=I('post.');
            $data['id']=1;
            $tag=M('sysmessage')->save($data);
            if($tag)
                $this->success('编辑成功！');
            else
                $this->error('系统繁忙，稍后重试！');
        }else{
            $info=M('sysmessage')->find(1);
            $this->assign('info',$info);
            $this->display();
        }
    }

}