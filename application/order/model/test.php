<?php
/**
 * Created by PhpStorm.
 * User: yym
 * Date: 2019-03-30
 * Time: 11:01
 */


//$test = new \app\order\model\SendOrder();
$test = SendOrder();
$res=$test->getSendMaxBatch('2');
echo json($res);
//echo "test";
?>