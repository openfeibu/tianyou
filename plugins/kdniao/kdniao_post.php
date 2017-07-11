<?php
header("content-type:text/html;charset=utf-8");

$getcom = trim($_POST["com"]);
$getNu = trim($_POST["nu"]);
include_once("kdniao_config.php");

//调用获取物流轨迹
//-------------------------------------------------------------

$logisticResult = orderTracesSubByJson($postcom, $getNu);
echo $logisticResult;

//-------------------------------------------------------------

/**
 * Json方式  物流信息订阅
 */
function orderTracesSubByJson($shipperCode, $logisticCode){
	/*
	$requestData="{'OrderCode': 'ShipperCode',".
			   "'ShipperCode':'SF',".
			   "'LogisticCode':'236113059440',".
			   "'PayType':1,".
			   "'ExpType':1,".
			   "'IsNotice':0,".
			   "'Cost':1.0,".
			   "'OtherCost':1.0,".
			   "'Sender':".
			   "{".
			   "'Company':'LV','Name':'Taylor','Mobile':'15018442396','ProvinceName':'上海','CityName':'上海','ExpAreaName':'青浦区','Address':'明珠路73号'},".
			   "'Receiver':".
			   "{".
			   "'Company':'GCCUI','Name':'Yann','Mobile':'15018442396','ProvinceName':'北京','CityName':'北京','ExpAreaName':'朝阳区','Address':'三里屯街道雅秀大厦'},".
			   "'Commodity':".
			   "[{".
			   "'GoodsName':'鞋子','Goodsquantity':1,'GoodsWeight':1.0}],".
			   "'Weight':1.0,".
			   "'Quantity':1,".
			   "'Volume':0.0,".
			   "'Remark':'小心轻放'}";*/
	$requestData= "{\"OrderCode\":\"\",\"ShipperCode\":\"".$shipperCode."\",\"LogisticCode\":\"".$logisticCode."\"}";

	$datas = array(
        'EBusinessID' => EBusinessID,
        'RequestType' => '1002',
        'RequestData' => urlencode($requestData) ,
        'DataType' => '2',
    );
    $datas['DataSign'] = encrypt($requestData, AppKey);
	$result=sendPost(ReqURL, $datas);

	//根据公司业务处理返回的信息......

	return $result;
}

/**
 *  post提交数据
 * @param  string $url 请求Url
 * @param  array $datas 提交的数据
 * @return url响应返回的html
 */
function sendPost($url, $datas) {
    $temps = array();
    foreach ($datas as $key => $value) {
        $temps[] = sprintf('%s=%s', $key, $value);
    }
    $post_data = implode('&', $temps);
    $url_info = parse_url($url);
	if(empty($url_info['port']))
	{
		$url_info['port']=80;
	}
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader.= "Host:" . $url_info['host'] . "\r\n";
    $httpheader.= "Content-Type:application/x-www-form-urlencoded;charset=utf-8\r\n";
    $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
    $httpheader.= "Connection:close\r\n\r\n";
    $httpheader.= $post_data;
    $fd = fsockopen($url_info['host'], $url_info['port']);
    fwrite($fd, $httpheader);
    $gets = "";
	$headerFlag = true;
	while (!feof($fd)) {
		if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
			break;
		}
	}
    while (!feof($fd)) {
		$gets.= fread($fd, 128);
    }
    fclose($fd);

    return $gets;
}

/**
 * 电商Sign签名生成
 * @param data 内容
 * @param appkey Appkey
 * @return DataSign签名
 */
function encrypt($data, $appkey) {
    return urlencode(base64_encode(md5($data.$appkey)));
}

function characet($data){
  if( !empty($data) ){
    $fileType = mb_detect_encoding($data , array('UTF-8','GBK','LATIN1','BIG5')) ;
    if( $fileType != 'UTF-8'){
      $data = mb_convert_encoding($data ,'utf-8' , $fileType);
    }
  }
  return $data;
}

?>
