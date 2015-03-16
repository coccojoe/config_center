#!/usr/bin/env php
<?php
/*
  PHP_Socket通信 H*F*W 2011,测试工具
*/


if($argc>2){
  hfwsocket($argv[1],$argv[2]);
}else{
  echo "Uage: [host] [port]\n";
}

//client链接
function hfwsocket($host,$port){
	set_time_limit(0);  ///设置超时时间
	$socket=socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
        if(false==$socket){echo "建立socket失败~!\n";}  ///建立socket
	
        if(false==($result=socket_connect($socket,$host,$port))){echo "链接失败~!\n";}
        
        $in=HwCrypt_Encode("100\x01OK\x01test");
        socket_write($socket,$in,strlen($in)); //发送
        
        sleep(1); socket_close($socket);
  return 0;	
}

?>
