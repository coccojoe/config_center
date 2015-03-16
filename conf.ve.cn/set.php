<?php
/*
   接收并处理节点请求
*/
require('hfwinc/hwfunction.php');
 
  $VeData=$_SERVER['HTTP_VEDATA'];  /*动作与数据*/
  $key=$_SERVER['HTTP_VEKEY'];       /*动作代码-任务key*/
  $hash=$_SERVER['HTTP_VEHASH'];     /*节点标识*/
  $version=$_SERVER['HTTP_VEVER'];   /*版本号*/
  
   $hdat=''; $veState='0'; $veVer='2.0';
  
 if(!empty($key)&&!empty($hash)){
	  if(strlen($VeData)<2){exit(0);}
	  $VeArrs=explode("\x01",HwCrypt_Decode($VeData));
	   switch($VeArrs[0]){
		   case 'get':$hdat=ResFile();break; //读文件
		   case 'filesum':NewSum($VeArrs[1]);break; /*文件校验码*/
		   case 'state':StateMsg($VeArrs[1]);break; /*状态信息*/
		   default:break;
	   }
}else{$veState=HwCrypt_Encode('key-error');} //空key

header('Content-type: text/plain'); /*数据格式*/
header('veProc: '.HwCrypt_Encode("$veVer\x01$veState"));/*版本号,状态*/

echo $hdat;


/*状态信息*/
function StateMsg($msgs){
	$str=explode("\x02",$msgs);
	$ste=0;
	if(count($str)>3){
		switch($str[0]){
			case 'success': $ste=1;break;/*成功返回*/
			case 'serror': $ste=-1;break;/*错误返回*/
	    }
	    if(0!=$ste){
			if(strlen($str[3])>1){$mstr='0x'.strToHex($str[3]);}else{$mstr='""';}
			$hsql='update task_tab set msg='.$mstr.",ste=$ste where id=".$str[1].' and tkey="'.$str[2].'"';
			if(!HwExec($hsql)){WebCenterLogs('['.$_SERVER['REMOTE_ADDR'].']任务状态更新-错误',$hsql);}
	    }else{WebCenterLogs('['.$_SERVER['REMOTE_ADDR'].']节点状态码-错误',"未知返回状态:\n".$str[0]);}
	}else{WebCenterLogs('['.$_SERVER['REMOTE_ADDR'].']节点状态码-',strToHex($msgs));}
}

/*更新文件ID*/
function NewSum($str){
	$hstr=explode("\x02",$str);
	if(0<(int)$hstr[0]){
		$hsql='update file_tab set sum="'.$hstr[1].'" where id='.$hstr[0];
		if(!HwExec($hsql)){WebCenterLogs('['.$_SERVER['REMOTE_ADDR'].']执行文件MD5更新-错误',$hsql);}
	}
}

/*返回配置文件内容*/
function ResFile(){
	   global $key,$hash,$version,$veState,$veVer;
	   $HwFile=''; $hdat='';
		if(CheckHost($hash)){
			$tar=CheckTask($key); /*检索任务*/
			if(strlen($tar)>3){
						$tarray=explode("\x01",$tar);
						$HwFile=explode("\x01",HwCrypt_Decode($tarray[2])); /*文件路径*/
						 if(empty($HwFile[1])){$HwFile[1]='0';} /*空的md5值*/
						$hdat=VeFileType(strtolower(substr($HwFile[0],strlen($HwFile[0])-4,4)),$tarray[1]);
			}else{$veState=HwCrypt_Encode('Task is empty');} /*空的任务*/
		}else{$veState=HwCrypt_Encode('Node Error');} /*错误的节点*/

	  $velen=strlen($hdat);
	  header('VeFile: '.HwCrypt_Encode($velen."\x01".$HwFile[1]."\x01".$tarray[1]."\x01".$HwFile[0]."\x01".$tarray[0])); /*文件长度,MD5值,文件ID,文件名,任务ID*/
	  WebCenterLogs('['.$_SERVER['REMOTE_ADDR'].']领取任务',"任务KEY: $key\n节点标识: $hash\n客户端版本: $version\n文件名: ".$HwFile[0].'\n');
	  return $hdat;
}

/*检索节点合法性*/
function CheckHost($key){
  global $link; $Ret=false;
  $hsql='select count(*) from node_tab where ste=1 and ip="'.$_SERVER['REMOTE_ADDR'].'" and hkey="'.$key.'"';
  $result=mysql_query($hsql,$link);
  if($result){$row=mysql_fetch_array($result); if((int)$row[0]>0){$Ret=true;} @mysql_free_result($result);}
  return $Ret;
}
/*检索任务*/
function CheckTask($tkey){
  global $link; $Ret='';
  $hsql='select id,fid,fpath from task_tab where ip="'.$_SERVER['REMOTE_ADDR'].'" and tkey="'.$tkey.'"';
  $result=mysql_query($hsql,$link);
  if($result){
	 if(mysql_num_rows($result)>0){$row=mysql_fetch_array($result); $Ret=$row['id']."\x01".$row['fid']."\x01".$row['fpath'];  @mysql_free_result($result);}else{WebCenterLogs('['.$_SERVER['REMOTE_ADDR'].']任务不存在',"任务KEY: $tkey\nSQL: \n$hsql");return false;}
  }else{WebCenterLogs('['.$_SERVER['REMOTE_ADDR'].']任务检索失败',"任务KEY: $tkey\nSQL: \n$hsql");return false;}
  return $Ret;
}

/*检索配置节*/
function CheckNode($id){
	global $link; $Ret=array('main'=>'main');
	$result=mysql_query("select son from conf_tab where fid=$id group by son",$link);
	if($result){
		while($row=mysql_fetch_array($result)){$Ret[$row['son']]=$row['son'];}
		mysql_free_result($result);
	}
}

function hexToStr($hex){$string='';for($i=0;$i<strlen($hex)-1;$i+=2)$string.=chr(hexdec($hex[$i].$hex[$i+1]));return $string;}

/*生产文件格式*/
function VeFileType($type,$Fid){
	global $link,$veState;
	$result=mysql_query('select id,fid,nkey,var,act,tpy,son from conf_tab where fid='.$Fid,$link);
	if($result){
		switch($type){
			case '.ini': /*输出ini格式文件*/
			    $hdat='';
			    while($row=@mysql_fetch_array($result)){
					if(strlen($row['nkey'])>0){$hdat.=$row['nkey']." = \"".$row['var']."\"\n";}
				}
			break;
			default: /*默认输出ini文件*/
			    $hdat='';  $Nodes=array('main'=>'');
				while($row=@mysql_fetch_array($result)){
					if(strlen($row['nkey'])>0){$Nodes[$row['son']].=$row['nkey'].' = "'.$row['var']."\"\n";}
					else{$Nodes[$row['son']].=$row['var']."\n";} //如果没有key则直接输出，类似.htaccess正阳的文件
				}
				while ($RetRow=current($Nodes)){
					if('main'!=key($Nodes)){$hdat.='['.key($Nodes)."]\n";} //默认项节
					$hdat.=$RetRow;  next($Nodes);
			   }
	        break;
		}
		@mysql_free_result($result);
		/*清除任务*/
		//if(!HwExec('delete from task_tab where id='.$Fid)){/*$hdat=HwCrypt_Encode('delete fail ~!');*/}else{/*$hdat=HwCrypt_Encode('Delete yes ~!');*/}
	}else{$veState=HwCrypt_Encode('Check conf error');}
	return $hdat;
}

?>
