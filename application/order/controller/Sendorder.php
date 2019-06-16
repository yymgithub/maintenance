<?php
namespace app\order\controller;

use think\Controller;
use think\Db;

class Sendorder extends Controller
{

    private $appid = "wx1d3765eb45497a18";
    private $secret = "GItWuaH7sUjv6hteNu4bgo3imwO1YiGa0QosDxOj2xY";

    public function index()
    {
        if (!session('?user')){
            $this->error("使用系统前请先登录！！！");
            $this->redirect('/login/login/index');
        }
        $send_order= \app\order\model\Searchorder::fuzsearch('',14);
        $this->assign('sendorders',$send_order);

        //提示信息
        $count = Db::table('work_order')->where('work_order_status',0)->count();
        $info = db('work_order')->alias('wo')
            ->where('wo.work_order_status',0)
            ->join('customer_info ci','ci.customer_id = wo.customer_id')
            ->field('wo.description,ci.customer_name,wo.create_time,wo.work_order_id')
            ->select();
        $this->assign('count',$count);
        $this->assign('info',$info);

        return $this->fetch();
    }

    public function send(){
        $worderids = $_POST['worderids'];
        $orderId = $_POST['orderid'];
        $orderStatus = $_POST['orderstatus'];
        $status = $_POST['status'];
        $model = model('SendOrder');
        $model1 = model('ModifyOrder');

        if($orderStatus == 3){
            $hisworkers = $model1->getModifyWorkers($orderId);
            $res3 = $model->updateHistorySend($orderId);
            $ids1 = $this->objectToArray($hisworkers);
            $str1 = $this->parsrWorkers($ids1);
            $code3 = $this->urlSend($str1,3);
        }
        $res2 = $model->insertSendOrderAndOrder($orderId,$worderids);
        if($res2){
            //企业发送消息
            $idss = $this->parsrWorkers($worderids);
            $code = $this->urlSend($idss,1);
            if($status == 1){
                $this->success($idss,'sendorder/index');
            }
            elseif ($status == 2){
                $this->success('详情派单成功',url('orderinformation/index',['id' => $orderId]));
            }
            elseif ($status == 3){
                $this->success('查询派单成功','searchorder/index');
            }
        }
        else{
            $this->error('系统错误');
        }
    }

    public function search(){
        $param = input('post.');
        $keyword = $param['keyword'];
        $status = $param['status'];
        $send_order= \app\order\model\Searchorder::fuzsearch($keyword,$status);
        $this->assign('sendorders',$send_order);

        //提示信息
        $count = Db::table('work_order')->where('work_order_status',0)->count();
        $info = db('work_order')->alias('wo')
            ->where('wo.work_order_status',0)
            ->join('customer_info ci','ci.customer_id = wo.customer_id')
            ->field('wo.description,ci.customer_name,wo.create_time,wo.work_order_id')
            ->select();
        $this->assign('count',$count);
        $this->assign('info',$info);

        return $this->fetch('index');
    }

    //插入数据库和企业微信发送消息
    public function insertAndSend(){

    }
    //处理模态框传递维修工信息为企业微信需要格式
    public function parsrWorkers($worderids){
        $ids = array();
        foreach($worderids as $workerid){
            array_push($ids,$workerid);
        }
        $idss = implode('|',$ids);
        return $idss;
    }

    //给企业微信端发送消息
    public function urlSend($wokeridss,$ch){
        if($ch==1){
            $data = array(
                "touser" => $wokeridss,
                "msgtype" => "text",
                "agentid" => 1000011,
                "text" =>array("content" => "您有新的订单请注意查收"),
                "safe" => 0
            );
        }
        elseif ($ch==2){
            $data = array(
                "touser" => $wokeridss,
                "msgtype" => "text",
                "agentid" => 1000011,
                "text" =>array("content" => "您有订单已被改派"),
                "safe" => 0
            );
        }
        else{
            $data = array(
                "touser" => $wokeridss,
                "msgtype" => "text",
                "agentid" => 1000011,
                "text" =>array("content" => "您之前处理的异常订单已被重新发派"),
                "safe" => 0
            );
        }
        $token = $this->getToken();
        $url ='https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token='.$token;
        list($returnCode, $returnContent) = $this->http_post_json($url, json_encode($data));
        return $returnCode;
    }

    function getToken(){
        return $this->checkAccessToken($this->appid,$this->secret);
    }


    //获取access_token，若过期则重新申请
    function checkAccessToken($appid,$appsecret){
        $condition = array('appid'=>$appid,'appsecret'=>$appsecret);
        $access_token_set=DB('wxtoken')->where($condition)->find();//获取数据

        if($access_token_set){
            //检查是否超时，超时了重新获取
            if($access_token_set['AccessExpires']>time()){
                //未超时，直接返回access_token
                return $access_token_set['access_token'];
            }else{
                //已超时，重新获取
                $url_get='https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid='.$appid.'&corpsecret='.$appsecret;
                $json= $this->https_request($url_get);
                //var_dump($json);
                $access_token=$json['access_token'];
                $AccessExpires=time()+intval($json['expires_in']);
                $data['access_token']=$access_token;
                $data['AccessExpires']=$AccessExpires;
                $result = DB('wxtoken')->where($condition)->update($data);//更新数据
                if($result){
                    return $access_token;
                }else{
                    return $access_token;
                }
            }
        }else{
            echo "appid或appsecret不正确";
            return false;
        }
    }

    function https_request($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $out = curl_exec($ch);
        curl_close($ch);
        return  json_decode($out,true);
    }

    function send_post($url, $post_data) {

        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }

    function http_post_json($url, $jsonStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonStr)
            )
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return array($httpCode, $response);
    }
    //将数据库的派单表修改状态的workers取出装换为数组
    public function objectToArray($needmodifyworkers){
        $ids1 = array();
        foreach ($needmodifyworkers as $worker){
            array_push($ids1,$worker['worker_id']);
        }
        return $ids1;
    }
}
