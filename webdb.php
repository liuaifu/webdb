<?php
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
if(!isset($_GET['k']) && !isset($_GET['v'])){
	//创建表
	if (!sqlite_query($db, "CREATE TABLE data(k varchar(64) primary key, v varchar(1024))")) {
		exit('sqlite_query fail');
	}
	echo "table created.";
	exit(0);
}
if(isset($_GET['k']) && isset($_GET['v'])){
	//插入或更新数据
	if (!sqlite_query($db, "REPLACE INTO data(k,v) VALUES ('".$_GET['k']."','".$_GET['v']."')")){
		exit("sqlite_query fail");
	}
	echo "ok";
	exit(0);
}

//查询数据
if(isset($_GET['k']) && !isset($_GET['v'])){
	//把数据检索出来
	if (!($result = sqlite_query($db, "SELECT v FROM data where k='".$_GET['k']."'"))) {
		exit("sqlite_query fail");
	}
	//获取检索数据并显示
	while ($array = sqlite_fetch_array($result)) {
		echo $array[0];
		sqlite_close($db);
		exit(0);
	}

	sqlite_close($db);
	exit("not found");
}

sqlite_close($db);
exit("invalid params");
?>
