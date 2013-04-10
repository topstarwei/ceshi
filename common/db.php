<?php
$conn = mysql_connect($dbhost,$dbuser,$dbpwd);
if(!$conn){
	echo "数据库连接失败，请刷新重试";
	return false;
}
mysql_select_db($dbname,$conn);
mysql_query("set names 'utf-8'");
?>
