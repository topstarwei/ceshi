<?php
session_start();
include('./config/config.php');
include('./common/db.php');
include('./common/pinyin.class.php');




$uid = $_GET['inf'];
$sqlinf = "select * from `user` where uid=".$uid;
$queryinf = mysql_query($sqlinf);
$resultinf = mysql_fetch_array($queryinf);

$sqlCheck = "select count(id) as count from `ssl_auth` where uid=".$uid; 
$queryCheck = mysql_query($sqlCheck);
$resultCheck = mysql_fetch_array($queryCheck);
//转换为拼音格式
$resultinf['common'] = Pinyin($resultinf['common'],1);

if(!$resultCheck['count']){
	$filename = $resultinf['common'].time().".pfx";
	$sslFilePath = dirname($_SERVER['SCRIPT_FILENAME']).$sslCachePath."/".$filename;

	
	$dn = array(
		 "countryName" => "CN",
		 "stateOrProvinceName" => "BEIJING",
		 "localityName" => "BEIJING",
		 "organizationName" => "anheng",
		 "organizationalUnitName" => "anheng",
		 "commonName" => $resultinf['common'],
		 "emailAddress" => $resultinf['email']
		 );
	 
	$configargs = array('config' => dirname($_SERVER['SCRIPT_FILENAME']).'/ssl/openssl.cnf'); 
	$privkey = openssl_pkey_new($configargs);//根据配置生产私钥
	$csr = openssl_csr_new($dn, $privkey);//根据$dn和私钥产生有公钥的请求证书
	$CA_CERT = dirname($_SERVER['SCRIPT_FILENAME']).'/ssl/'."ca.crt";
	$CA_KEY  = dirname($_SERVER['SCRIPT_FILENAME']).'/ssl/'."ca.key";  
	$cacert = file_get_contents($CA_CERT);
	$caprivkey = file_get_contents($CA_KEY);


	$scrt = openssl_csr_sign($csr, $cacert, $caprivkey,$lifetime,$configargs);//用CA和CA的KEY来给请求证书进行签名
	openssl_pkcs12_export_to_file($scrt, $sslFilePath, $privkey,$pwd);
		//最后产生私钥和公钥证书在一起的格式文件 ，用于浏览器导入（服务器端的server.crt就没有必要用这步）
		//$content = file_get_contents($sslFilePath);
		//$content = stripslashes($content);
		/**
		* 执行记录数据库功能
		*/
		$outtime = $lifetime*86400+time();

		$sql = "insert into `ssl_auth`(`uid`,`pfxcontent`,`outtime`,`create`) values(".$uid.",'".$sslFilePath."','".$outtime."','".time()."')";
		
		$query = mysql_query($sql);
		if($query>0){
			Header("Content-Type: application/x-pkcs12");
			header("Content-Length: ".filesize($sslFilePath));
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename=".$filename);
			header("Content-Transfer-Encoding: binary");
			readfile($sslFilePath);
		}
		else{
			echo "下载失败，请刷新后重试";	
		}
	
	}
	

else{
	$sql = 'select `pfxcontent`,`outtime` from ssl_auth where uid='.$uid;
	$query = mysql_query($sql);
	$result = mysql_fetch_array($query);

	$url = $result['pfxcontent'];

	$namearray = explode('/',$url);
	$n = count($namearray)-1;
	$filenames = $namearray["$n"];

	//如果数据库存在，源文件也存在，将直接进入导出操作，否则先生成再导出
	//同样适用于证书过期操作
	if(!file_exists($url)||$result['outtime']<time()){
		Create::createssl($url,$filenames,$resultinf,$pwd,$lifetime);	
	}	
		
	Header("Content-Type: application/x-pkcs12");
      header("Content-Length: ".filesize($url));
      header("Content-Type: application/force-download");
      header("Content-Disposition: attachment; filename=".$filenames);
      header("Content-Transfer-Encoding: binary");
      readfile($url);	
}
?> 
