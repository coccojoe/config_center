#!/usr/bin/env php
<?php
/*
  创建推送任务 ,测试工具
*/


if($argc>2){
  hfwsocket($argv[1],$argv[2]);
}else{
  echo "Uage: [推送目标IP] [文件编号]\n";
}

//client链接
function hfwsocket($host,$file){
        set_time_limit(0);  ///设置超时时间
        $socket=socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
        if(false==$socket){echo "建立socket失败~!\n";}  ///建立socket

        if(false==($result=socket_connect($socket,'127.0.0.1','80'))){echo "链接失败~!\n";}

        $HwHeader="POST /task.php HTTP/1.0\nHost: conf.ve.cn\nNode: $host\nFile: ".$file."\nConnection: close\n\n";
        socket_write($socket,$HwHeader,strlen($HwHeader)); //发送

        while($out=socket_read($socket,8192)){echo $out;}
        
        echo "\n";
        socket_close($socket);
  return 0;
}

?>
