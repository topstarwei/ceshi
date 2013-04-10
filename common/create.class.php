<?php
/**
* ssl证书操作类
*/
class Create extends Action{
	function __construct(){
		
	}
	/**
	* 生成证书
	*/
	function createssl($sslFilePath,$filename,$resultinf,$pwd,$lifetime){
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
            $outtime = $lifetime*86400+time();
		$sql = "update `ssl_auth` set `pfxcontent`='".$sslFilePath."',`outtime`='".$outtime."',`create`='".time()."' where `uid`=".$resultinf['uid'];
		$query = mysql_query($sql);		
	}
}
?>
