<?php
/**
 * Created by PhpStorm.
 * User: gaozhan
 * Date: 2017/05/2 0002
 * Time: 8:45
 */

use Think\Exception;

class Kuaidiniao{
    protected $appkey; //appkey
    protected $EBusinessID; //商户id
    protected $apiPath = 'http://testapi.kdniao.cc:8081/api/EOrderService';

    /**
     * 初始化
     * KuaidiniaoController constructor.
     * @param $EBusinessID 商户ID
     * @param $appkey 商户appkey
     */
    public function __construct($EBusinessID,$appkey)
    {
        if(empty($EBusinessID) || empty($appkey)){
            throw new Exception('数据非法');
        }
        $this->appkey = $appkey;
        $this->EBusinessID = $EBusinessID;
    }



    /**
     * 电子面单
     * @param array $eorder 快递基本信息
     * @param array $sender 发件人信息
     * @param array $receiver 收件人信息
     * @param $commodity 商品信息
     * @return bool|mixed 成功返回结果 失败返回false
     */
    public function eorder(array $eorder,array $sender,array $receiver,$commodity)
    {
        if(count($eorder) < 1 ||count($sender) < 1 ||count($receiver) < 1||count($commodity) < 1 ){
            throw new Exception('数据非法');
        }
        $eorder['Receiver'] = $receiver;
        $eorder['Sender'] = $sender;
        $eorder['Commodity'] = $commodity;
        $result = $this->submitEorder($eorder,1007);
        return $result;
    }

    /**
     * 及时查询接口 每天3000次
     * @param $eorderinfo 查询信息 快递代号 和快递单号
     * @return bool|mixed
     */
    public function timelyQuery($shipperCode,$logisticCode)
    {
        if(empty($shipperCode) || empty($logisticCode)){
            throw new Exception('数据非法');
        }
        $eorderinfo = [
            'ShipperCode'=>$shipperCode,
            'LogisticCode'=>$logisticCode,
        ];
        return $this->submitEorder($eorderinfo,1002);
    }

    /**
     * 物流轨迹接口
     * @param $eorderinfo 查询信息 快递代号 和快递单号
     * @return bool|mixed
     */
    public function track(array $eorder,array $sender,array $receiver,$commodity)
    {
        if(count($eorder) < 1 ||count($sender) < 1 ||count($receiver) < 1||count($commodity) < 1 ){
            throw new Exception('数据非法');
        }
        $eorder['Receiver'] = $receiver;
        $eorder['Sender'] = $sender;
        $eorder['Commodity'] = $commodity;
        return $this->submitEorder($eorder,1008);
    }

    /**
     * 组装数据
     * @param $requestData 要组装的数据 array格式
     * @param $requestType 接口的id号
     * @return bool|mixed
     */
    protected function submitEorder($requestData,$requestType)
    {
        //将数组转成json格式
        $requestData = json_encode($requestData, JSON_UNESCAPED_UNICODE);
        //再次组装数据 接上系统级参数
        $datas = array(
            'EBusinessID' => $this->EBusinessID,
            'RequestType' => $requestType, //接口id号
            'RequestData' => urlencode($requestData) , //编码
            'DataType'=>'2'
        );
        //sign签名
        $datas['DataSign'] = $this->encrypt($requestData, $this->appkey);
        //提交
        $result =  $this->sendPost($datas);
        //json转换
        $result = json_decode($result,true);
        //返回
        if($result['Success']){
            return $result;
        }else{
            throw new Exception($result['Reason']);
        }
    }


    /**
     * 提交数据 curl封装
     * @param $datas
     * @return mixed
     */
    protected function sendPost($datas)
    {
        //对数组进行url格式转换
        $datas = http_build_query( $datas );
        //定义header头
        $this_header = array(
            "application/x-www-form-urlencoded;charset=utf-8"
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiPath);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 兼容本地没有指定curl.cainfo路径的错误
        curl_setopt($ch,CURLOPT_HTTPHEADER,$this_header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        $response = curl_exec($ch);
        if(curl_errno($ch)){
            // 显示报错信息；终止继续执行
            die(curl_error($ch));
        }
        curl_close($ch);
        return $response;
    }
    /**
     * sign
     * @param $data
     * @return string
     */
    public function encrypt($data)
    {
        return urlencode(base64_encode(md5($data.$this->appkey)));
    }
}