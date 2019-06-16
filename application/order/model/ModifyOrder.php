<?php
/**
 * Created by PhpStorm.
 * User: yym
 * Date: 2019-03-30
 * Time: 22:06
 */

namespace app\order\model;


use think\Model;
use think\Db;

class ModifyOrder extends Model
{

  public function getNeedUpdateOrders(){
      $needUpdateOrders = Db::name('work_order')
          ->alias('t1')
          ->join('customer_info t2', 't1.customer_id=t2.customer_id')
          ->where('t1.work_order_status', 1)
          ->where('t1.status', 1)
          ->field('t1.work_order_id,t2.customer_name,t2.identity,t1.create_time,t1.description,t1.address,t1.phone,t1.work_order_status')
          ->select();
      return $needUpdateOrders;
  }

  public function updateOrder($orderid, $workerids){
      // 启动事务
      Db::startTrans();
      try {
          Db::name('send_orders')
              ->where('work_order_id', $orderid)
              ->where('batch', 1)
              ->update(['batch' => 0]);
          $data = array();
          foreach ($workerids as $workerid) {
              $data[] = array('work_order_id' => $orderid, 'worker_id' => $workerid, 'work_status' => 0,'batch'=> 1);
          }
          Db::name('send_orders')->insertAll($data);
          // 提交事务
          Db::commit();
          return true;
      } catch (\Exception $e) {
          // 回滚事务
          Db::rollback();
          echo $e;
          return false;
      }
  }
    public function getModifyWorkers($orderid){
        $modifyworkerids=Db::name('send_orders')
            ->where('work_order_id',$orderid)
            ->where('batch',1)
            ->field('worker_id')
            ->select();
        return $modifyworkerids;
    }

}