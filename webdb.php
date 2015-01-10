<?php
header("Access-Control-Allow-Origin:*");

if(isset($_GET['op']) && $_GET['op']=='set' && !isset($_REQUEST['k'])){
	echo "<form method=\"post\" action=\"webdb.php\">\r\n";
	echo "<input type=\"text\" name=\"k\" />\r\n";
	echo "<textarea name=\"v\" rows=\"30\" cols=\"100\"></textarea>\r\n";
	echo "<input type=\"submit\" />\r\n";
	echo "</form>";
	exit(0);
}

$db_name = "webdb.db";
//创建数据库文件,文件内容为空
if (!file_exists($db_name)) {
	if (!($fp = fopen($db_name, "w+"))) {
		exit("can't open ".$db_name);
	}
	fclose($fp);
}
//打开数据库文件
if (!($db = sqlite_open($db_name))) {
	exit("sqlite_open fail");
}
if(!isset($_REQUEST['k']) && !isset($_REQUEST['v'])){
	//创建表
	if (!sqlite_query($db, "CREATE TABLE data(k varchar(64) primary key, v varchar(1024))")) {
		exit('sqlite_query fail');
	}
	echo "table created.";
	exit(0);
}
if(isset($_REQUEST['k']) && isset($_REQUEST['v'])){
	//插入或更新数据
	$v = base64_encode($_REQUEST['v']);
	if (!sqlite_query($db, "REPLACE INTO data(k,v) VALUES ('".$_REQUEST['k']."','".$v."')")){
		exit("sqlite_query fail");
	}
	echo "ok";
	exit(0);
}

//查询数据
if(isset($_REQUEST['k']) && !isset($_REQUEST['v'])){
	//把数据检索出来
	if (!($result = sqlite_query($db, "SELECT v FROM data where k='".$_REQUEST['k']."'"))) {
		exit("sqlite_query fail");
	}
	//获取检索数据并显示
	while ($array = sqlite_fetch_array($result)) {
		if(isset($_GET['op']) && $_GET['op']=='set'){
			echo "<form method=\"post\" action=\"webdb.php\"><br/>\r\n";
			echo "key<br/><input type=\"text\" name=\"k\" value=\"".$_REQUEST['k']."\" /><br/>\r\n";
			echo "value<br/><textarea name=\"v\" rows=\"30\" cols=\"100\">".base64_decode($array[0])."</textarea><br/>\r\n";
			echo "<input type=\"submit\" />\r\n";
			echo "</form>";
		}else
			echo base64_decode($array[0]);
		sqlite_close($db);
		exit(0);
	}

	sqlite_close($db);
	exit("not found");
}

sqlite_close($db);
exit("invalid params");
?>
