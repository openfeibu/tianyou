<?php

/**
 * ECSHOP 银联在线支付主文件
	插件名称：银联在线支付(UPOP)插件 FOR ECSHOP
	插件版本：1.0
	插件作者：潍坊中脉信息技术部
	插件编码：UTF-8
	适用版本：ECSHOP 2.7.3
	开发时间：2015.11.6
	支持站点：www.wfzmxx.com
	反馈邮箱/QQ账号：3138711793@qq.com
 */
error_reporting(E_ALL);
if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

// 包含配置文件
$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/upop.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'upop_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = '潍坊中脉信息技术部';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.wfzmxx.com';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.0';

    /* 配置信息 */
    $modules[$i]['config'] = array(
		array('name' => 'upop_merid', 'type' => 'text', 'value' => '777290058110097'),
		array('name' => 'upop_sin_cert_pwd', 'type' => 'text', 'value' => '000000'),
		array('name' => 'sdk_front_trans_url','type'=>'text','value'=>'https://101.231.204.80:5000/gateway/api/frontTransReq.do'),
		array('name' => 'sdk_back_trans_url','type'=>'text','value'=>'https://101.231.204.80:5000/gateway/api/backTransReq.do'),
    );

    return;
}

/**
 * 中国银联支付接口类
 */
class UPOP
{
    /**
     * 生成支付代码
     * @param   array   $order  订单信息
     * @param   array   $payment    支付方式信息
     */

    function get_code($order, $payment)
    {

		// 初始化变量
		$lib_path		= ROOT_PATH . 'includes/modules/payment/unionpay/';
		include_once($lib_path . 'common.php');

		$config=array(
		   'sdk_sign_cert_pwd'=>$payment['upop_sin_cert_pwd'],
		   'sdk_front_trans_url'=>$payment['sdk_front_trans_url'],
		   'sdk_back_trans_url'=>$payment['sdk_back_trans_url'],
		);
		
		$unionpay=new unionpay($config);
		
		$params = array(
				//以下信息非特殊情况不需要改动
				'version' => '5.0.0',                 //版本号
				'encoding' => 'utf-8',				  //编码方式
				'certId' => $unionpay->getSignCertId (),	      //证书ID
				'txnType' => '01',				      //交易类型
				'txnSubType' => '01',				  //交易子类
				'bizType' => '000201',				  //业务类型
				'frontUrl' =>  return_url(basename(__FILE__, '.php')),  //前台通知地址
				'backUrl'  =>   return_url(basename(__FILE__, '.php')),	//后台通知地址
				'signMethod' => '01',	              //签名方法
				'channelType' => '08',	              //渠道类型，07-PC，08-手机
				'accessType' => '0',		          //接入类型
				'currencyCode' => '156',	          //交易币种，境内商户固定156
				
				//TODO 以下信息需要填写
				'merId' => $payment['upop_merid'],		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
				'orderId' => $order['order_sn'],	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
				'txnTime' => date("YmdHis",time()),	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
				'txnAmt' => $order['order_amount'] * 100,	//交易金额，单位分，此处默认取demo演示页面传递的参数
			);
			$unionpay->sign ( $params );
			return $unionpay->create_html ( $params);
    }

    /**
     * 响应操作
     */
    function respond()
    {
		// 初始化变量
		$lib_path		= ROOT_PATH . 'includes/modules/payment/unionpay/';
		// 包含库接口文件
		include_once($lib_path . 'common.php');
		
        $payment        = get_payment('upop');
		$config=array(
		   'sdk_sign_cert_pwd'=>$payment['upop_sin_cert_pwd'],
		   'sdk_front_trans_url'=>$payment['sdk_front_trans_url'],
		   'sdk_back_trans_url'=>$payment['sdk_back_trans_url'],
		);
		$unionpay=new unionpay($config);		
	
		$order_sn=$_POST['orderId'];
		$payment_amount=$_POST['settleAmt'];
		
		if($_POST ['respCode']=='00'||$_POST ['respCode']=='A6'){
			
			//检查签名是否正确
			if(!$unionpay->verify($_POST)){
				return false;
			}
			
			// 检查价格是否一致。
			$sql = "SELECT p.order_amount FROM " . $GLOBALS['ecs']->table('pay_log') . " AS p LEFT JOIN " . $GLOBALS['ecs']->table('order_info') . " AS o ON p.order_id = o.order_id WHERE o.order_sn = '"
			. $order_sn . "'";
			$order_amount = $GLOBALS['db']->getOne($sql) * 100;

			if ($order_amount != $payment_amount)
			{
				return false;
			}			
			
			$sql="select log_id from ".$GLOBALS['ecs']->table('order_info')." as o right join ".$GLOBALS['ecs']->table('pay_log')." as p".
			" on o.order_id=p.order_id where o.order_sn='{$order_sn}'";
			$log_id=$GLOBALS['db']->getOne($sql);
			
			if($log_id){
				order_paid($log_id,PS_PAYED);
				return true;
			}else{
				return false;
			}	
		}
		
		return false;
    }
}
?>
