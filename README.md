# kuaidiniao
###快递鸟api接口封装 面单/及时查询/物流跟踪
```

$kd = new \Kuaidiniao('appid', 'appkey');
   $eorder = [
            'ShipperCode' => 'EMS',
            'OrderCode' => '01265771003871',
            'PayType' => '1',
            'ExpType' => '1',
            'IsReturnPrintTemplate'=>1
        ];
   $sender = [
            'Name' => '王八李',
            'Mobile' => '13888888888',
            'ProvinceName' => '李先生',
            'CityName' => '深圳市',
            'ExpAreaName' => '福田区',
            'Address' => '详细地址',

        ];
   $receiver = [
            'Name' => '李王八',
            'Mobile' => '18888888888',
            'ProvinceName' => '王八先生',
            'CityName' => '湖北省',
            'ExpAreaName' => '仙桃市',
            'Address' => '详细地址',
        ];
    $commodity[]=  [
            'GoodsName' => '商品名称',
         ];
         
$result = $kd->eorder($eorder, $sender, $receiver, $commodity);  //下单
$eorder['LogisticCode'] = 123456789;   //物流跟踪需要上面的数据和快递单号
$result = $kd->track($eorder, $sender, $receiver, $commodity); //物流跟踪
$result = $kd->timelyQuery('SF', 789134737040); //及时查询
```
