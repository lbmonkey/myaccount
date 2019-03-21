<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {
    /**
     * 状态，也就是首页
     */
   public function index(){
       $info['tag']=(I('get.tag'))?I('get.tag'):'date';//默认为按日期
       $info['year']=(I('get.year'))?I('get.year'):(date('Y'));
       $info['month']=(I('get.month'))?I('get.month'):(date('m'));
       $info['big_type']=(I('get.big_type'))?I('get.big_type'):1;//默认为收入

       //该月的开始时间戳和结束时间戳
       $mydate['beginThismonth']=strtotime($info['year'].'-'. $info['month']);
       $day=date('t',$mydate['beginThismonth']);
       $mydate['endThismonth']=$mydate['beginThismonth']+ 86400*$day  - 1;


       //按照日期的
       $sql='SELECT a_smalltype.name,a_bill.*,FROM_UNIXTIME( create_time,\'%d\') as mydate FROM a_bill INNER JOIN a_smalltype ON a_bill.small_type = a_smalltype.id '.'where create_time >'.$mydate['beginThismonth'].' and create_time <'.$mydate['endThismonth'].' and user_id='.session('index_user.id').' order by mydate desc,a_bill.create_time desc';
       $result = M('bill')->query($sql);
       $mydata=array();
       $mydaydata=array();
       $account['in']=0;
       $account['out']=0;
       foreach ($result as $value){
           if ($value['big_type'] == 1){//收入
               $mydata[$value['mydate']][]=$value;
               $mydaydata[$value['mydate']]['create_time']=$value['create_time'];
               $mydaydata[$value['mydate']]['in'] += $value['money'];
               $account['in'] += $value['money'];
               $value['tag']='+';
           }
           else if ($value['big_type'] == 2){//支出
               $mydata[$value['mydate']][]=$value;
               $mydaydata[$value['mydate']]['create_time']=$value['create_time'];
               $mydaydata[$value['mydate']]['out'] += $value['money'];
               $account['out'] += $value['money'];
               $value['tag']='-';
           }
           else if ($value['big_type'] == 3 && $value['played'] !=0){//应收
               $mydata[$value['mydate']][]=$value;
               $mydaydata[$value['mydate']]['create_time']=$value['create_time'];
               $mydaydata[$value['mydate']]['in'] += $value['money'];
               $account['in'] += $value['money'];
               $value['tag']='+';
           }
           else if ($value['big_type'] == 4 && $value['played'] !=0){//应付
               $mydata[$value['mydate']][]=$value;
               $mydaydata[$value['mydate']]['create_time']=$value['create_time'];
               $mydaydata[$value['mydate']]['out'] += $value['money'];
               $account['out'] += $value['money'];
               $value['tag']='-';
           }
       }
      // dump($mydata);
       //dump($mydaydata);

       //按照类型的(只能查本月的)
       $sql='SELECT a_smalltype.name,a_bill.* FROM a_bill INNER JOIN a_smalltype ON a_bill.small_type = a_smalltype.id '.'where create_time >'.$mydate['beginThismonth'].' and create_time <'.$mydate['endThismonth'].' and user_id='.session('index_user.id').' and a_bill.big_type='.$info['big_type'].' order by id desc';
       $type_result = M('bill')->query($sql);
       $this->assign('mytypedata',$type_result);
        //dump($type_result);
       $this->assign('mydata',$mydata);
       $this->assign('mydaydata',$mydaydata);
       $this->assign('info',$info);
       $this->assign('account',$account);
       $this->display();
   }

    public function details(){
        $data=I('get.');
        $bill_info=M('bill')->find($data['id']);
        $bill_info['name']=$data['type'];
        $this->assign('bill_info',$bill_info);
        $this->assign('Userinfo',$this->getUserInfo());
        $this->display();
    }

    public function del_account(){
        if($id=I('get.id')){
            $tag=M('bill')->delete($id);
            if($tag)
                $this->success('删除成功','Index/index');
            else
                $this->error('系统繁忙，稍后重试！');
        }
    }

}