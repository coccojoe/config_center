<?php
require('hfwinc/hwconfig.php');

/*执行一条SQL并返回结果*/
function HwSel($hsql,$typ=null){
     global $link; $hstr='';
     if($result=mysql_query($hsql,$link)){
       if(!empty($typ)){$hstr="$typ\x01";} $num=mysql_num_fields($result);
       while($row=mysql_fetch_array($result)){for($i=0;$i<$num;$i++){$hstr.=$row[$i]."\x03";} if($num>0){$hstr[strlen($hstr)-1]="\x02";}else{$hstr.="\x02";}}
       $n=strlen($hstr); if($n>strlen($typ)+1){$hstr=substr($hstr,0,$n-1);} mysql_free_result($result);
     }
   return $hstr;
}
/*返回分页信息*/
function HwPage($tab,$where=null){
	global $link; $num=0;
	$hsql="select count(*) from $tab $where"; //总记录数
	if($result=mysql_query($hsql,$link)){$row=mysql_fetch_array($result);  $num=(int)$row[0];  mysql_free_result($result);}
	if($num>0){$num=$num/(int)PAGE;}else{$num=0;}
	return ceil($num);
}

/*执行SQL返回一个字段值*/
function GetColumn($hsql){global $link; $hstr=''; if($result=mysql_query($hsql,$link)){$row=mysql_fetch_array($result); $hstr=$row[0]; mysql_free_result($result);} return $hstr;}  
/*读配置文件路径*/
function ConFile($id){global $link; $f='';$result=mysql_query('select file from file_tab where id='.$id,$link);if($result){$row=@mysql_fetch_array($result); $f=$row[0]; @mysql_free_result($result);}return $f;}

//产生一组随机数, m>0 返回无符号的随机数，适合用于验证码
function Rndkeys($len,$m=0){
	 if($m>0){$char='xt8C2iwybDvenqo9pcWV0H4BlMO3ghGsKNEdj5kRr7XYLJP6uZamI1UfASFTQz';}else{$char='ZxKDHW7VN=nmE(O}JX1PCd*T530Ssf!Il2r~e@aAp#%w8B[c+uh]j$YR{gb&)iG6yLkM9oQ4F^zUtqv';}
	 $max=strlen($char)-1; PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000); $hash='';
	 while(strlen($hash)<$len){$key=$char[mt_rand(0,$max)]; if(false==strpos($hash,$key)){$hash.=$key;}}
	 return $hash;
}

////验证用户登录状态
function chackuser($act=0){
	global $link;
	if(isset($_SESSION['#LOGIN'])&&isset($_SESSION['#UID'])&&isset($_SESSION['#DTE'])&&isset($_SESSION['#TID'])){
	  if($_SESSION['#LOGIN']!=true||$_SESSION['#UID']==''||$_SESSION['#DTE']==''||$_SESSION['#TID']==''){session_destroy();  echo "error-timeout\x01login.php"; exit(0); /*登录超时*/}else{
		  if(empty($_SESSION['#DTE'])){$_SESSION['#DTE']=date('his');}else{if(date('his')-$_SESSION['#DTE']>60){seluser();/*检测用户信息*/$_SESSION['#DTE']=date('his');}/*间隔 60秒钟对用户信息做一次检测*/}
	  }
   }else{session_destroy(); echo "error-nologin\x01login.php";exit(0);}
}
////检测用户信息是否有所改动
function seluser(){
  global $link; $hsql='select id,ste from user_tab where uid='.$_SESSION['#UID'].' and pwd="'.$_SESSION['#PWD'].'"';
  $result=@mysql_query($hsql,$link);
  if($result!=false){
    if(!($row=mysql_fetch_array($result))||$_SESSION['#TID']!=$row['id']||(int)$row['ste']<1){session_destroy();echo "error-relogin\x01login.php";exit(0);/*用户信息有所变动～！ 请从新登录~!*/}
    @mysql_free_result($result);
  }else{session_destroy();echo "error-login\x01login.php";exit(0);/*登录错误～！ 请从新登录~!*/}
}

//Web端操作日志
function WebCenterLogs($title,$logbody=''){
	global $link; $hsql='insert into fw_log(jet,log,uid,ip,dte)values';
	if(strlen($title)>0){$jet='0x'.strToHex($title);}else{$jet='""';}
	if(strlen($logbody)>0){$log='0x'.strToHex($logbody);}else{$log='0x'.strToHex($_SERVER['HTTP_USER_AGENT']);}
	$ip=$_SERVER['REMOTE_ADDR']; if(isset($_SESSION['#UID'])){$uid=$_SESSION['#UID'];}else{$uid='"node"';}  $dte=date('Y-m-d H:i:s');
	$hsql.="($jet,$log,$uid,'$ip','$dte')";
	HwExec($hsql);
}
//返回操作权
function Gants($g){if((string)$g==$_SESSION['#GRUP']["k_$g"]){return true;}else{return false;}}

function HGT($ht){if(isset($_GET[$ht])){$str=$_GET[$ht];}else{if(isset($_POST[$ht])){$str=$_POST[$ht];}else{$str='';}}return $str;}
function strToHex($string){$hex='';for($i=0;$i<strlen($string);$i++){$htr=dechex(ord($string[$i]));if(strlen($htr)<2){$htr='0'.$htr;}$hex.=$htr;}$hex=strtoupper($hex);return $hex;}
function hcode($str){return $hstr=str_replace('+','%20',urlencode($str));}
function HwExec($hsql){global $link;if(mysql_query($hsql,$link)){return true;}else{return false;}} //成功返回 true，失败返回 false
?>
