<?php
session_start();
include('./config/config.php');
include('./common/db.php');



$sql = "select * from `user` where uid=".$_GET['inf'];
$query = mysql_query($sql);
$result = mysql_fetch_array($query);
$_SESSION['common'] = $result['common'];
$_SESSION['uid'] = $result['uid'];


if(!empty($_SERVER['SSL_CLIENT_S_DN_CN'])&&$_SERVER['SSL_CLIENT_S_DN_CN']==$_SESSION['common']){		
	/*$endTime = strtotime($_SERVER['SSL_CLIENT_V_END']);//判断证书是否过期 如果过期将重新生成
	if($endTime<time()){
		echo "你的证书已经过期！";	
	}*/
	echo "欢迎进入";
	
}
else	
	echo "需要安装属于你的证书并卸载其它人证书才能进入 点击<a href='http://localhost/ceshi/down.php?inf=".$result['uid']."'>下载证书</a>，证书安装完完成之后点击<a href='https://localhost'>直接进入</a>";	


?>
