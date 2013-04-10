<?php
include('./config/config.php');
include('./common/db.php');

$conn = mysql_connect($dbhost,$dbuser,$dbpwd);
if(!$conn){
	echo "数据库连接失败，请刷新重试";
	return false;
}
mysql_select_db($dbname,$conn);
$data = dirname($_SERVER['SCRIPT_FILENAME'])."/ssl_auth.sql";
$content=file_get_contents($data);
mysql_query("set names 'utf-8'");
$query = mysql_query($content);
if($query>0){
	echo "数据库操作成功！";
}
else{
	echo "数据库操作失败，请刷新后重试！";
}
?>
