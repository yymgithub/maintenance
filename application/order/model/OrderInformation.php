<?php
/**
 * Created by PhpStorm.
 * User: yym
 * Date: 2019-03-30
 * Time: 13:07
 */

namespace app\order\model;


use think\Db;
use think\Model;

class OrderInformation extends Model
{
    /**
     * 功能：获取一个订单的全部信息
     * @param $orderid $nowbatch
     */
    public function getOrderInformation($orderid,$nowbatch){
        // 启动事务
        Db::startTrans();
        try{


            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            echo $e;
        }
    }

    public function getCustomerAndBaseByOrderId($orderid)
    {
        $baseorders = Db::name('work_order')
            ->alias('t1')
            ->join('customer_info t2', 't1.customer_id=t2.customer_id')
            ->join('area t3', 't1.area_id=t3.area_id')
            ->where('t1.work_order_id', $orderid)
            ->where('t1.status', 1)
            ->where('t2.status', 1)
            ->field('t1.work_order_id,t2.customer_name,t2.identity,t1.create_time,t1.description,t1.address,t1.phone,t1.work_order_status,t1.no_person_status,t1.customer_images,t3.area_description')
            ->select();
        return $baseorders;
    }
    /**
     * 功能：获取相应order的派单信息
     */
    public function getSendOrderByOrderId($orderid,$nowbatch){
        $sendInformation = Db::name('send_orders')
            ->alias('t1')
            ->join('worker_info t2', 't1.worker_id=t2.worker_id')
            ->where('t1.status',1)
            ->where('t2.status',1)
            ->where('work_order_id',$orderid)
            ->where('batch',$nowbatch)
            ->field('t1.work_status,t1.worker_id,t2.worker_name')
            ->select();
        return $sendInformation;
    }

    /**
     * 功能：获取工单对应的保修类别信息
     * @param $orderid
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRepairTypesByOrderId($orderid){
        $repairTypes = Db::name('repair_types_record')
            ->alias('t1')
            ->join('repair_types t2', 't1.record_type_id=t2.type_id')
            ->where('t1.work_order_id', $orderid)
            ->where('t1.status', 1)
            ->where('t2.status', 1)
            ->field('t1.work_order_id,t2.type_description')
            ->select();
        return $repairTypes;
    }

    /**
     * 功能：获取工单对应的维修结果信息
     * @param $orderid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRepairResultsByOrderId($orderid){
        $repairResults = Db::name('repair_result')
            ->where('work_order_id',$orderid)
            ->where('status',1)
            ->order('repair_status')
            ->select();
        return $repairResults;
    }

    /**
     * 查询该工单对应异常信息，指取最新一条或0条
     * @param $orderid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getExceptionByOrderId($orderid){
        $exresults = Db::name('exception_result')
            ->where('work_order_id',$orderid)
            ->where('status',1)
            ->order('create_time desc')
            ->limit(1)
            ->select();
        return $exresults;
    }

    /**
     * 获取对应工单评价表里的评价信息
     * @param $orderid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getEvaluateResultsByOrderId($orderid){
        $evaluateResults = Db::name('evaluate_result')
            ->where('work_order_id',$orderid)
            ->where('status',1)
            ->order('evaluate_staus')
            ->select();
        return $evaluateResults;
    }
}