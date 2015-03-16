<?php

/*创建推送任务*/
function HwCreateTask($ip,$fid,$ftr){
   global $link;
   if(strlen($ip)>30){return false;}else{$node='0x'.strToHex($ip);}
   $dte=date('Y-m-d H:i:s');
   $key=md5($ip.Rndkeys(32).$dte,false);
   $hsql="insert into task_tab(ip,tkey,fid,fpath,dte)values($node,'$key',$fid,'$ftr','$dte')";
   if(HwExec($hsql)){echo "success\x01Create task Success ~!";HwSend($ip,'32303',$key);}else{echo "error\0x1Fail to create task ~!";}
   return true;
}

/*检索文件*/
function HwCheckFile($id){
   global $link;
   $result=mysql_query('select file from file_tab where id='.$id,$link); $f='';
   if($result){$row=@mysql_fetch_array($result); $f=$row[0]; @mysql_free_result($result);}
   return $f;
}

//激活节点动作
function HwSend($host,$port,$tkey){
  set_time_limit(0);  ///设置超时时间
  $socket=socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
  if(false==$socket){echo "error\x01建立socket失败~!";}  ///建立socket

  if(false==($result=socket_connect($socket,$host,$port))){echo "error\x01链接失败~![$host]:$port";}

  $in=HwCrypt_Encode("100\x01$tkey\x01".Rndkeys(18));
  socket_write($socket,$in,strlen($in)); //发送

  usleep(30000); socket_close($socket);
  return 0;
}

?>
