<?php
header ( 'Content-type:text/html;charset=utf-8' );
require_once(ROOT_PATH . 'includes/modules/payment/unionpay/log.class.php') ;

// 签名证书路径
$path=$_SERVER['DOCUMENT_ROOT'] . '/includes/modules/payment/unionpay/';

define('SDK_SIGN_CERT_PATH',$path.'certs/700000000000001_acp.pfx');
// 签名证书密码
//define('SDK_SIGN_CERT_PWD','000000');
// 密码加密证书（这条一般用不到的请随便配）
define('SDK_ENCRYPT_CERT_PATH',$path.'certs/acp_test_enc.cer');
// 验签证书路径（请配到文件夹，不要配到具体文件）
define('SDK_VERIFY_CERT_DIR',$path.'certs/');
//日志 目录 
define('SDK_LOG_FILE_PATH',$path.'logs/');
//日志级别
define('SDK_LOG_LEVEL','INFO');

/*
 *中国银联支付类
 */

class unionpay{	
//---------------------------------开始-----------------------------------
private $sdk_sign_cert_pwd; // 签名证书密码
private $sdk_front_trans_url; // 前台请求地址
private $sdk_back_trans_url; // 后台请求地址
private $php_log; //日志对象
//初始化构造函数
public function __CONSTRUCT($config=array()){

	$this->php_log= new PhpLog ( SDK_LOG_FILE_PATH, "PRC", SDK_LOG_LEVEL );
	$this->sdk_sign_cert_pwd=$config['sdk_sign_cert_pwd'];
	$this->sdk_front_trans_url=$config['sdk_front_trans_url'];
	$this->sdk_back_trans_url=$config['sdk_back_trans_url'];
}
/**
 * 字符串转换为 数组
 *
 * @param unknown_type $str        	
 * @return multitype:unknown
 */
public function convertStringToArray($str) {
	$result = array ();
	
	if (! empty ( $str )) {
		$temp = preg_split ( '/&/', $str );
		if (! empty ( $temp )) {
			foreach ( $temp as $key => $val ) {
				$arr = preg_split ( '/=/', $val, 2 );
				if (! empty ( $arr )) {
					$k = $arr ['0'];
					$v = $arr ['1'];
					$result [$k] = $v;
				}
			}
		}
	}
	return $result;
}

/**
 * 压缩文件 对应java deflate
 *
 * @param unknown_type $params        	
 */
public function deflate_file(&$params) {
	foreach ( $_FILES as $file ) {
		$this->php_log->LogInfo ( "---------处理文件---------" );
		if (file_exists ( $file ['tmp_name'] )) {
			$params ['fileName'] = $file ['name'];
			
			$file_content = file_get_contents ( $file ['tmp_name'] );
			$file_content_deflate = gzcompress ( $file_content );
			
			$params ['fileContent'] = base64_encode ( $file_content_deflate );
			$this->php_log->LogInfo ( "压缩后文件内容为>" . base64_encode ( $file_content_deflate ) );
		} else {
			$this->php_log->LogInfo ( ">>>>文件上传失败<<<<<" );
		}
	}
}


/**
 * 构造自动提交表单
 *
 * @param unknown_type $params        	
 * @param unknown_type $action        	
 * @return string
 */
public function create_html($params, $action='') {

	$encodeType = isset ( $params ['encoding'] ) ? $params ['encoding'] : 'UTF-8';
	$html = <<<eot
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset={$encodeType}" />
</head>
<body>
    <form id="pay_form" name="pay_form" action="{$this->sdk_front_trans_url}" method="post">
	
eot;
	foreach ( $params as $key => $value ) {
		$html .= "    <input type=\"hidden\" name=\"{$key}\" id=\"{$key}\" value=\"{$value}\" />\n";
	}
	$html .= <<<eot
    <input type="submit" value="提交">
    </form>
</body>
</html>
eot;
	return $html;
}

/**
 * map转换string
 *
 * @param
 *        	$customerInfo
 */
public function getCustomerInfoStr($customerInfo) {
  if($customerInfo == null || count($customerInfo) == 0 )
  	return "";
	return base64_encode ( "{" . $this->createLinkString ( $customerInfo, false, false ) . "}" );
}

/**
 * map转换string，按新规范加密
 *
 * @param
 *        	$customerInfo
 */
public function getCustomerInfoStrNew($customerInfo) {
  if($customerInfo == null || count($customerInfo) == 0 )
  	return "";
	$encryptedInfo = array();
	foreach ( $customerInfo as $key => $value ) {
		if ($key == 'phoneNo' || $key == 'cvn2' || $key == 'expired' ) {
		//if ($key == 'phoneNo' || $key == 'cvn2' || $key == 'expired' || $key == 'certifTp' || $key == 'certifId') {
			$encryptedInfo [$key] = $customerInfo [$key];
			unset ( $customerInfo [$key] );
		}
	}
	if( count ($encryptedInfo) > 0 ){
		$encryptedInfo = $this->createLinkString ( $encryptedInfo, false, false );
		$encryptedInfo = $this->encryptData ( $encryptedInfo, SDK_ENCRYPT_CERT_PATH );
		$customerInfo ['encryptedInfo'] = $encryptedInfo;
	}
	return base64_encode ( "{" . $this->createLinkString ( $customerInfo, false, false ) . "}" );
}

/**
 * 讲数组转换为string
 *
 * @param $para 数组        	
 * @param $sort 是否需要排序        	
 * @param $encode 是否需要URL编码        	
 * @return string
 */
public function createLinkString($para, $sort, $encode) {
	$linkString = "";
	if ($sort) {
		$para = $this->argSort ( $para );
	}
	while ( list ( $key, $value ) = each ( $para ) ) {
		if ($encode) {
			$value = urlencode ( $value );
		}
		$linkString .= $key . "=" . $value . "&";
	}
	// 去掉最后一个&字符
	$linkString = substr ( $linkString, 0, count ( $linkString ) - 2 );
	
	return $linkString;
}

/**
 * 对数组排序
 *
 * @param $para 排序前的数组
 *        	return 排序后的数组
 */
public function argSort($para) {
	ksort ( $para );
	reset ( $para );
	return $para;
}

/**
 * 后台交易 HttpClient通信
 *
 * @param unknown_type $params        	
 * @param unknown_type $url        	
 * @return mixed
 */
public function post($params, $url, &$errmsg) {
	$opts = $this->createLinkString ( $params, false, true );
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false ); // 不验证证书
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false ); // 不验证HOST
	curl_setopt ( $ch, CURLOPT_SSLVERSION, 3 );
	curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
			'Content-type:application/x-www-form-urlencoded;charset=UTF-8' 
	) );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $opts );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	$html = curl_exec ( $ch );
	if(curl_errno($ch)){
		$errmsg = curl_error($ch);
		curl_close ( $ch );
		return false;
	}
    if( curl_getinfo($ch, CURLINFO_HTTP_CODE) != "200"){
		$errmsg = "http状态=" . curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ( $ch );
		return false;
    }
	curl_close ( $ch );
	return $html;
}

/**
 * 打印请求应答
 *
 * @param
 *        	$url
 * @param
 *        	$req
 * @param
 *        	$resp
 */
public function printResult($url, $req, $resp) {
	echo "=============<br>\n";
	echo "地址：" . $url . "<br>\n";
	echo "请求：" . str_replace ( "\n", "\n<br>", htmlentities ( $this->createLinkString ( $req, false, true ) ) ) . "<br>\n";
	echo "应答：" . str_replace ( "\n", "\n<br>", htmlentities ( $resp ) ) . "<br>\n";
	echo "=============<br>\n";
}

/**
 * 签名
 *
 * @param String $params_str
 */
public function sign(&$params) {
	$this->php_log->LogInfo ( '=====签名报文开始======' );
	if(isset($params['signature'])){
		unset($params['signature']);
	}
	// 转换成key=val&串
	$params_str = $this->createLinkString ( $params, true, false );
	$this->php_log->LogInfo ( "签名key=val&...串 >" . $params_str );
	
	$params_sha1x16 = sha1 ( $params_str, FALSE );
	$this->php_log->LogInfo ( "摘要sha1x16 >" . $params_sha1x16 );
	
	// 签名证书路径
	$cert_path = SDK_SIGN_CERT_PATH;
		
	$private_key = $this->getPrivateKey ( $cert_path );
	// 签名
	$sign_falg = openssl_sign ( $params_sha1x16, $signature, $private_key, OPENSSL_ALGO_SHA1 );
	if ($sign_falg) {
		$signature_base64 = base64_encode ( $signature );
		$this->php_log->LogInfo ( "签名串为 >" . $signature_base64 );
		$params ['signature'] = $signature_base64;
	} else {
		$this->php_log->LogInfo ( ">>>>>签名失败<<<<<<<" );
	}
	$this->php_log->LogInfo ( '=====签名报文结束======' );
}

/**
 * 验签
 *
 * @param String $params_str        	
 * @param String $signature_str        	
 */
public function verify($params) {
	// 公钥
	$public_key = $this->getPulbicKeyByCertId ( $params ['certId'] );	
	// 签名串
	$signature_str = $params ['signature'];
	unset ( $params ['signature'] );
	$params_str = $this->createLinkString ( $params, true, false );
	$this->php_log->LogInfo ( '报文去[signature] key=val&串>' . $params_str );
	$signature = base64_decode ( $signature_str );

	$params_sha1x16 = sha1 ( $params_str, FALSE );
	$this->php_log->LogInfo ( '摘要shax16>' . $params_sha1x16 );	
	$isSuccess = openssl_verify ( $params_sha1x16, $signature,$public_key, OPENSSL_ALGO_SHA1 );
	$this->php_log->LogInfo ( $isSuccess ? '验签成功' : '验签失败' );
	return $isSuccess;
}

/**
 * 根据证书ID 加载 证书
 *
 * @param unknown_type $certId        	
 * @return string NULL
 */
public function getPulbicKeyByCertId($certId) {
	$this->php_log->LogInfo ( '报文返回的证书ID>' . $certId );
	// 证书目录
	$cert_dir = SDK_VERIFY_CERT_DIR;
	$this->php_log->LogInfo ( '验证签名证书目录 :>' . $cert_dir );
	$handle = opendir ( $cert_dir );
	if ($handle) {
		while ( $file = readdir ( $handle ) ) {
			clearstatcache ();
			$filePath = $cert_dir . '/' . $file;
			if (is_file ( $filePath )) {
				if (pathinfo ( $file, PATHINFO_EXTENSION ) == 'cer') {
					if ($this->getCertIdByCerPath ( $filePath ) == $certId) {
						closedir ( $handle );
						$this->php_log->LogInfo ( '加载验签证书成功' );
						return $this->getPublicKey ( $filePath );
					}
				}
			}
		}
		$this->php_log->LogInfo ( '没有找到证书ID为[' . $certId . ']的证书' );
	} else {
		$this->php_log->LogInfo ( '证书目录 ' . $cert_dir . '不正确' );
	}
	closedir ( $handle );
	return null;
}

/**
 * 取证书ID(.pfx)
 *
 * @return unknown
 */
public function getCertId($cert_path) {
	$pkcs12certdata = file_get_contents ( $cert_path );

	openssl_pkcs12_read ( $pkcs12certdata, $certs, $this->sdk_sign_cert_pwd );
	$x509data = $certs ['cert'];
	openssl_x509_read ( $x509data );
	$certdata = openssl_x509_parse ( $x509data );
	$cert_id = $certdata ['serialNumber'];
	return $cert_id;
}

/**
 * 取证书ID(.cer)
 *
 * @param unknown_type $cert_path        	
 */
public function getCertIdByCerPath($cert_path) {
	$x509data = file_get_contents ( $cert_path );
	openssl_x509_read ( $x509data );
	$certdata = openssl_x509_parse ( $x509data );
	$cert_id = $certdata ['serialNumber'];
	return $cert_id;
}

/**
 * 签名证书ID
 *
 * @return unknown
 */
public function getSignCertId() {
	// 签名证书路径
	return $this->getCertId ( SDK_SIGN_CERT_PATH );
}
public function getEncryptCertId() {
	// 签名证书路径
	return $this->getCertIdByCerPath ( SDK_ENCRYPT_CERT_PATH );
}

/**
 * 取证书公钥 -验签
 *
 * @return string
 */
public function getPublicKey($cert_path) {
	return file_get_contents ( $cert_path );
}
/**
 * 返回(签名)证书私钥 -
 *
 * @return unknown
 */
public function getPrivateKey($cert_path) {
	$pkcs12 = file_get_contents ( $cert_path );
	openssl_pkcs12_read ( $pkcs12, $certs, $this->sdk_sign_cert_pwd );
	return $certs ['pkey'];
}

/**
 * 加密数据
 * @param string $data数据
 * @param string $cert_path 证书配置路径
 * @return unknown
 */
public function encryptData($data, $cert_path=SDK_ENCRYPT_CERT_PATH) {
	$public_key = $this->getPublicKey ( $cert_path );
	openssl_public_encrypt ( $data, $crypted, $public_key );
	return base64_encode ( $crypted );
}


/**
 * 解密数据
 * @param string $data数据
 * @param string $cert_path 证书配置路径
 * @return unknown
 */
public function decryptData($data, $cert_path=SDK_SIGN_CERT_PATH) {
	$data = base64_decode ( $data );
	$private_key = $this->getPrivateKey ( $cert_path );
	openssl_private_decrypt ( $data, $crypted, $private_key );
	return $crypted;
}

/**
 * Author: gu_yongkang
 * data: 20110510
 * 密码转PIN
 * Enter description here ...
 * @param $spin
 */
public function  Pin2PinBlock( &$sPin )
{
	//	$sPin = "123456";
	$iTemp = 1;
	$sPinLen = strlen($sPin);
	$sBuf = array();
	//密码域大于10位
	$sBuf[0]=intval($sPinLen, 10);

	if($sPinLen % 2 ==0)
	{
		for ($i=0; $i<$sPinLen;)
		{
			$tBuf = substr($sPin, $i, 2);
			$sBuf[$iTemp] = intval($tBuf, 16);
			unset($tBuf);
			if ($i == ($sPinLen - 2))
			{
				if ($iTemp < 7)
				{
					$t = 0;
					for ($t=($iTemp+1); $t<8; $t++)
					{
					$sBuf[$t] = 0xff;
					}
					}
				}
				$iTemp++;
				$i = $i + 2;	//linshi
		}
		}
		else
		{
		for ($i=0; $i<$sPinLen;)
		{
		if ($i ==($sPinLen-1))
		{
		$mBuf = substr($sPin, $i, 1) . "f";
		$sBuf[$iTemp] = intval($mBuf, 16);
		unset($mBuf);
		if (($iTemp)<7)
		{
		$t = 0;
		for ($t=($iTemp+1); $t<8; $t++)
		{
		$sBuf[$t] = 0xff;
		}
		}
		}
			else
			{
			$tBuf = substr($sPin, $i, 2);
			$sBuf[$iTemp] = intval($tBuf, 16);
			unset($tBuf);
			}
			$iTemp++;
			$i = $i + 2;
			}
			}
			return $sBuf;
		}
		/**
	 * Author: gu_yongkang
	 * data: 20110510
	 * Enter description here ...
	 * @param $sPan
	 */
	 public function FormatPan(&$sPan)
	 {
		$iPanLen = strlen($sPan);
		$iTemp = $iPanLen - 13;
			$sBuf = array();
			$sBuf[0] = 0x00;
			$sBuf[1] = 0x00;
			for ($i=2; $i<8; $i++)
			{
			$tBuf = substr($sPan, $iTemp, 2);
			$sBuf[$i] = intval($tBuf, 16);
			$iTemp = $iTemp + 2;
			}
			return $sBuf;
	}

	public function Pin2PinBlockWithCardNO(&$sPin, &$sCardNO)
	{
		$sPinBuf = $this->Pin2PinBlock($sPin);
		$iCardLen = strlen($sCardNO);
		//		$this->php_log->LogInfo("CardNO length : " . $iCardLen);
		if ($iCardLen <= 10)
		{
		return (1);
		}
		elseif ($iCardLen==11){
		$sCardNO = "00" . $sCardNO;
		}
			elseif ($iCardLen==12){
			$sCardNO = "0" . $sCardNO;
			}
			$sPanBuf = $this->FormatPan($sCardNO);
			$sBuf = array();

			for ($i=0; $i<8; $i++)
			{
				$sBuf[$i] = vsprintf("%c", ($sPinBuf[$i] ^ $sPanBuf[$i]));
			}
			unset($sPinBuf);
			unset($sPanBuf);
			//		return $sBuf;
			$sOutput = implode("", $sBuf);	//数组转换为字符串
			return $sOutput;
	}
}
?>