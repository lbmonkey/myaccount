<?php
namespace Home\Controller;
use Think\Controller;
class BillController extends CommonController {
    /**
     * 账单
     */
   public function index(){
       //获取本月起始时间戳和结束时间戳
       $year=(I('get.year'))?I('get.year'):(date('Y'));
       $mydate['thisYear']=strtotime(date($year.'-01-01'));

       $map['user_id']=array('eq',session('index_user.id'));
       $map['create_time']=array('gt',$mydate['thisYear']);
       $account['in']=0;//总收入
       $account['out']=0;//总支出
       $account['year']=$year;//年限

       $sql = 'select *,FROM_UNIXTIME( create_time, \'%m\' ) as mydate from a_bill where create_time >'.$mydate['thisYear'] .' and user_id='.session('index_user.id').' order by mydate';
       $result = M('bill')->query($sql);
       $mydata=array();
       foreach ($result as $value){
           if ($value['big_type'] == 1){//收入
               $mydata[$value['mydate']]['in'] += $value['money'];
               $account['in'] += $value['money'];
           }
           else if ($value['big_type'] == 2){//支出
               $mydata[$value['mydate']]['out'] += $value['money'];
               $account['out'] += $value['money'];
           }
           else if ($value['big_type'] == 3 && $value['played'] !=0){//应收
               $mydata[$value['mydate']]['in'] += $value['money'];
               $account['in'] += $value['money'];
           }
           else if ($value['big_type'] == 4 && $value['played'] !=0){//应付
               $mydata[$value['mydate']]['out'] += $value['money'];
               $account['out'] += $value['money'];
           }
       }
       $this->assign('mydata',$mydata);
       $this->assign('myaccount',$account);
       $this->display('bill');
   }

}