<?php
/**
 * Created by PhpStorm.
 * User: yym
 * Date: 2019-03-29
 * Time: 16:44
 */
namespace app\order\model;
use think\Model;
use think\Db;

class SendOrder extends Model
{
    /**功能是获取工单状态0和3的数据，0是待处理，3是异常状态
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNeedSendOrders()
    {
        $needSendOrders = Db::name('work_order')
            ->alias('t1')
            ->join('customer_info t2', 't1.customer_id=t2.customer_id')
            ->where('t1.work_order_status', 0)
            ->whereOr('t1.work_order_status', 3)
            ->where('t1.status', 1)
            ->field('t1.work_order_id,t2.customer_name,t2.identity,t1.create_time,t1.description,t1.address,t1.phone,t1.work_order_status')
            ->select();
        return $needSendOrders;
    }

    /**
     * 功能：派单更改order表状态，插入新的派单信息
     * @param $orderid
     * @param $workerids
     * @param $nowbatch
     * @return bool
     */
    public function insertSendOrderAndOrder($orderid, $workerids)
    {
        // 启动事务
        Db::startTrans();
        try {
            Db::name('work_order')
                ->where('work_order_id', $orderid)
                ->update(['work_order_status' => 1]);
            $data = array();
            foreach ($workerids as $workerid) {
                $data[] = array('work_order_id' => $orderid, 'worker_id' => $workerid, 'work_status' => 0,'batch'=> 1);
            }
            Db::name('send_orders')->insertAll($data);
            // 提交事务
            Db::commit();
            return true;
        } catch (Exception $e) {
            // 回滚事务
            Db::rollback();
            echo $e;
        }
    }

    /**
     * 功能：根据工单号获取该工单号在派单表中的最大批次
     * @param $orderid
     */
    public function getSendMaxBatch($orderid)
    {
        $batch = Db::name('send_orders')
            ->where('work_order_id', $orderid)
            ->where('status', 1)
            ->order('batch desc')
            ->limit(1)
            ->select();
        return $batch;
    }
    //更新历史send订单的batch为0
    public function updateHistorySend($orderid){
        $res = Db::name('send_orders')
            ->where('batch',1)
            ->where('work_order_id',$orderid)
            ->update(['batch' => 0]);
        return $res;
    }
}