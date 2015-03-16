<?php
require('hfwinc/session.php');
require('hfwinc/rsa.php');
require('hfwinc/hwfunction.php');

/*用户登录检测*/
if(!isset($_SESSION['#LOGIN'])||!isset($_SESSION['#UID'])||!isset($_SESSION['#DTE'])||!isset($_SESSION['#TID'])){
    $hstr=explode("\x01",trim(Priv_decrypt(HGT('info'),TRUE))); if(count($hstr)<2){echo 'error-num';exit(0);} //格式不对
    if(strlen($hstr[0])>1){$uid='0x'.strToHex($hstr[0]);}else{echo 'error-uid';exit(0);}  //用户名为空或小于2个字节
    if(strlen($hstr[1])>5){$pwd=md5($hstr[1],false);}else{echo 'error-pwd';exit(0);}  //密码为空或小于6
    
    $hsql="select id,nme,ste,grup from user_tab where uid=$uid and pwd='$pwd'";  $result=@mysql_query($hsql,$link);
   if($result!=false){
		 if(($row=@mysql_fetch_array($result))){
			  if((int)$row['ste']<1){echo "error-act";exit(0);}  ///用户没有启用
			  $_SESSION['#UID']=$uid;
			  $_SESSION['#DTE']=date('his');
			  $_SESSION['#TID']=$row['id'];
			  $_SESSION['#NME']=$row['nme'];  //真实姓名
			  $_SESSION['#GRUP']=UserGants($row['grup']);  //权限组
			  $_SESSION['#PWD']=$pwd;
			  $_SESSION['#LOGIN']=true;
			  HwExec('update user_tab set lte="'.date('Y-m-d H:i:s').'",ip="'.$_SERVER['REMOTE_ADDR'].'" where id='.$row['id']);
			  WebCenterLogs('用户登录');
			  echo "success\x01index.php";exit(0);  ///登录成功
		 }else{echo "error-login\x01login.php";exit(0);}
		 mysql_free_result($result);
   }else{echo "error-sql\x01login.php";exit(0);}
}else{echo "logined\x01index.php";exit(0);}  //用户已经登录

/*用户权限组*/
function UserGants($str){
	$Arry=array(); $arr=explode('|',$str); $n=count($arr);
	for($i=0;$i<$n;$i++){$Arry['k_'.$arr[$i]]=$arr[$i];}
    return $Arry;
}

?>
