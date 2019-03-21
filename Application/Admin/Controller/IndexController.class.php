<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    protected function _initialize(){
        if(!is_login()){
            $this->redirect('Admin/login');
        }
    }
    public function index(){
        $this->display();
    }
    public function head(){
        $this->display();
    }
    public function left(){
        $this->display();
    }
    public function right(){
        $this->assign('time',time());
        $this->display();
    }
    //修改管理员密码
    public function updpass(){
        if(IS_POST){
            if(!I('post.name')&&!I('post.newpassword')&&!I('post.email')){
                $this->error('请输入一个要修改的项！');
            }
            if(I('post.name')){
                $attr['name']=I('post.name');
            }
            if(I('post.email')){
                $attr['email']=I('post.email');
            }
            if(I('post.password')){
                $attr['password']=md5(I('post.password'));
            }
            if(I('post.newpassword')){
                $attr['newpassword']=I('post.newpassword');
            }
            $admin_user=session('admin_user');
            $attr['id']=$admin_user['id'];
            if($attr['password']){
                if($attr['password']==$admin_user['password']){
                    if($attr['newpassword']){
                        $attr['password']=md5($attr['newpassword']);
                    }
                }else{
                    $this->error('输入的旧密码不正确!','updpass');
                    exit();
                }
            }else{
                unset( $attr['password']);//不要密码项
            }
            $admin=M('admin');
            if($admin->save($attr)){
                $ccs=$admin->find($attr['id']);
                session('admin_user',$ccs);
                $this->success('修改成功','right');
            }else{
                $this->error('修改失败，请重试','updpass');
            }

        }else{
            $this->display();
        }
    }
    //展示用户信息
    public function showuser(){
        $User = M('user'); // 实例化User对象
        $count = $User->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(2)
        $show = $Page->show();// 分页显示输出 // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $user_list = $User->order('create_time')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('user_list',$user_list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
    //删除用户
    public function delete_user(){
        $tag=M('user')->delete(I('get.id'));
        if($tag){
            $tag=M('bill')->where('user_id='.I('get.id'))->delete();
            $tag1=M('words')->where('user_id='.I('get.id'))->delete();
            if($tag&&$tag1)
                $this->success('删除成功！');
            else
                $this->error('系统繁忙，稍后重试！');
        }
        else
            $this->error('系统繁忙，稍后重试！');
    }
    //展示类别
    public function showsmall_type(){
        $info['big_type']=I('get.big_type')?I('get.big_type'):1;//默认为收入,应收应付归到收入和支出栏
        $Smalltype = M('smalltype'); // 实例化User对象
        $count = $Smalltype->where('bigtype='.$info['big_type'])->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,4);// 实例化分页类 传入总记录数和每页显示的记录数(2)
        $show = $Page->show();// 分页显示输出 // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $type_list = $Smalltype->where('bigtype='.$info['big_type'])->order('time')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('info',$info);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('type_list',$type_list);
        $this->display();
    }
    //删除类别
    public function delete_type(){
        $tag=M('smalltype')->delete(I('get.id'));
        if($tag)
            $this->success('删除成功！');
        else
            $this->error('系统繁忙，稍后重试！');
    }
    //添加
    public function add_type(){
        if(IS_POST){
            $data=I('post.');
            $data['time']=time();
            $tag=M('smalltype')->add($data);
            if($tag)
                $this->success('添加成功！');
            else
                $this->error('系统繁忙，稍后重试！');
        }else
            $this->display();
    }
    //退出登录
    public function logout(){
        session('admin_user',null);
        $this->redirect('Admin/login');
    }
}