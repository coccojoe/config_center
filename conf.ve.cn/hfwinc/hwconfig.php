<?php
  $db_host='127.0.0.1';  ///数据库服务器地址
  $db_user='veconfuser';       ///用户名
  $db_pass='bxf1m8gHUv9Di';       ///密码
  $db_db='veconfer';        ///数据库
  $link=mysql_connect($db_host,$db_user,$db_pass);
  if($link==FALSE){echo "error\x01数据库连接失败~!";exit(0);}
  $db=mysql_select_db($db_db);
  mysql_set_charset('utf8');   ///设置字符集
?>
