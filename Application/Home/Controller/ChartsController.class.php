<?php
namespace Home\Controller;
use Think\Controller;
class ChartsController extends CommonController {
   public function index()
   {
       $info['year']=(I('get.year'))?I('get.year'):(date('Y'));
       $info['month']=(I('get.month'))?I('get.month'):(date('m'));
       //该月的开始时间戳和结束时间戳
       $mydate['beginThismonth']=strtotime($info['year'].'-'. $info['month']);
       $day=date('t',$mydate['beginThismonth']);
       $mydate['endThismonth']=$mydate['beginThismonth']+ 86400*$day  - 1;
       $mydate['beginThisyear']=strtotime(date($info['year'].'-01-01'));
       $mydate['endThisyear']=strtotime(date($info['year'].'-12-30 23:59:59'));
       //饼图
       $bill = M('bill');
       $sql='SELECT a_smalltype.name,a_bill.id,a_bill.big_type,a_bill.money,a_bill.played  FROM a_bill INNER JOIN a_smalltype ON a_bill.small_type = a_smalltype.id '.'where create_time >'.$mydate['beginThismonth'].' and create_time <'.$mydate['endThismonth'].' and user_id='.session('index_user.id');
       $result = $bill->query($sql);
       $income = [];//收入
       $outcome = [];//支出
       $account['in']=0;
       $account['out']=0;
       foreach ($result as $value){
           if ($value['big_type'] == 1){//收入
              $income[$value['name']] += $value['money'];
              $account['in'] += $value['money'];
           }
           else if ($value['big_type'] == 2){//支出
               $outcome[$value['name']] += $value['money'];
               $account['out'] += $value['money'];
           }
           else if ($value['big_type'] == 3 && $value['played'] !=0){//应收
               $income[$value['name']] += $value['money'];
               $account['in'] += $value['money'];
           }
           else if ($value['big_type'] == 4 && $value['played'] !=0){//应付
               $outcome[$value['name']] += $value['money'];
               $account['out'] += $value['money'];
           }
       }
        //柱状图
       $sql = 'select a_bill.id,a_bill.big_type,a_bill.money,a_bill.played,FROM_UNIXTIME( create_time, \'%m\' ) as mydate from a_bill where create_time >'.$mydate['beginThisyear'] .' and create_time<='.$mydate['endThisyear'].' and user_id='.session('index_user.id').' order by mydate';
       $result = $bill->query($sql);
       $mydata=array();
       foreach ($result as $value){
           if ($value['big_type'] == 1){//收入
               $mydata[$value['mydate']]['in'] += $value['money'];
           }
           else if ($value['big_type'] == 2){//支出
               $mydata[$value['mydate']]['out'] += $value['money'];
           }
           else if ($value['big_type'] == 3 && $value['played'] !=0){//应收
               $mydata[$value['mydate']]['in'] += $value['money'];
           }
           else if ($value['big_type'] == 4 && $value['played'] !=0){//应付
               $mydata[$value['mydate']]['out'] += $value['money'];
           }
       }
       $this->assign('info',$info);
       $this->assign('account',$account);
       $this->assign('income',$income);
       $this->assign('outcome',$outcome);
       $this->assign('mydata',$mydata);//柱状图
       $this->display('chart');
   }

}