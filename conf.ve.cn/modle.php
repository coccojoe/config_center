<?php
require('hfwinc/session.php');
require('hfwinc/hwfunction.php');
require('hfwinc/hwdata.php');
require('hfwinc/task.php');
chackuser(); //用户检测

$hclss=HGT('class');
switch($hclss){
case 'main':MainInfo();break; //首页信息
case 'node':if(Gants(0)||Gants(4)){nodelist();}else{echo "error-gants\x010";exit(0);}break; //节点列表
case 'chnode':if(Gants(0)||Gants(8)){ChNode();}else{echo "error-gants\x010";exit(0);}break; //修改节点
case 'chfile':if(Gants(0)||Gants(7)){CheConf();}else{echo "error-gants\x010";exit(0);}break; //修改配置文件
case 'logs':  if(Gants(0)){hwlogs();}else{echo "error-gants\x010";exit(0);}break; //日志列表
case 'logsinfo': if(Gants(0)){LogsInfo();}else{echo "error-gants\x010";exit(0);}break; //查看日志列表
case 'confs':if(Gants(0)||Gants(3)){HwConfs();}else{echo "error-gants\x010";exit(0);}break; //配置列表
case 'choptions':if(Gants(0)||Gants(7)){Chopts();}else{echo "error-gants\x010";exit(0);}break; //修改配置项
case 'urmgt':if(Gants(0)){UserAdmin();}else{echo "error-gants\x010";exit(0);}break;//帐号管理页
case 'userinfo':if(Gants(0)){UserInfo();}else{echo "error-gants\x010";exit(0);}break; //用户详细
case 'saveuser':if(Gants(0)){SaveUser();}else{echo "error-gants\x010";exit(0);}break; //保存用户信息
case 'runtask': if(Gants(0)||Gants(5)){RunTask();}else{echo "error-gants\x010";exit(0);}break; //推送任务
case 'chtask': if(Gants(0)||Gants(5)){CheckTaskState();}else{echo "error-gants\x010";exit(0);}break; //任务状态
case 'loginout':session_destroy();echo "success\x01login.php";break; //退出登录
default:CmdType($hclss);break; //进入二级处理
}
//二级
function CmdType($cmds){
  $spin=explode('|',$cmds); $n=count($spin);
  if($n>=2){$id=(int)$spin[1];}else{$id=0;}
  switch($spin[0]){
  case 'nodeoff': //禁用节点
    if(Gants(0)||Gants(8)){
		if($id>0&&$n>2){$hstr=chste($id,0,'node'); echo $hstr."\x02".$spin[2]."\x02<span class='act_off'  onclick='hfwprint(\"nodeon|$id|ste$id\")'>未启用</span>";}
	}else{echo "error-gants\x010";exit(0);}
  break;
  case 'nodeon': //启用节点
	if(Gants(0)||Gants(8)){
		if($id>0&&$n>2){$hstr=chste($id,1,'node'); echo $hstr."\x02".$spin[2]."\x02<span class='act_on'  onclick='hfwprint(\"nodeoff|$id|ste$id\")'>已启用</span>";}
	}else{echo "error-gants\x010";exit(0);}
  break;
  case 'useroff': //禁用用户
	if(Gants(0)){
		if($id>0&&$n>2){$hstr=chste($id,0,'urmgt'); echo $hstr."\x02".$spin[2]."\x02<span class='act_off'  onclick='hfwprint(\"useron|$id|ste$id\")'>未启用</span>";}
	}else{echo "error-gants\x010";exit(0);}
  break;
  case 'useron': //启用用户
	if(Gants(0)){
		if($id>0&&$n>2){$hstr=chste($id,1,'urmgt'); echo $hstr."\x02".$spin[2]."\x02<span class='act_on'  onclick='hfwprint(\"useroff|$id|ste$id\")'>已启用</span>";}
	}else{echo "error-gants\x010";exit(0);}
  break;
  case 'cnfoff': //禁用文件
	if(Gants(0)||Gants(7)){
		if($id>0&&$n>2){$hstr=chste($id,0,'confs'); echo $hstr."\x02".$spin[2]."\x02<span class='act_off'  onclick='hfwprint(\"cnfon|$id|ste$id\")'>已禁用</span>";}
	}else{echo "error-gants\x010";exit(0);}
  break;
  case 'cnfon': //启用文件
	if(Gants(0)||Gants(7)){
		if($id>0&&$n>2){$hstr=chste($id,1,'confs'); echo $hstr."\x02".$spin[2]."\x02<span class='act_on'  onclick='hfwprint(\"cnfoff|$id|ste$id\")'>已启用</span>";}
	}else{echo "error-gants\x010";exit(0);}
  break;
  case 'flst':if($id>0){FileTab($id);}else{echo "error\x01flst-id";}break; //文件列表
  case 'nodeinfo': //节点信息
     if(Gants(0)||Gants(4)){
		 if($id>0){
			  $hstr=HwSel("select id,nme,ip,port,hkey,ste,act,dte,tag from node_tab where id=$id",'nodeinfo'); if(empty($hstr)){echo "error\x01nodeinfo";}else{echo $hstr;}
		  }
	  }else{echo "error-gants\x010";exit(0);}
  break;
  case 'confinfo': //配置文件详细
     if(Gants(0)||Gants(3)){
		  $nodes=HwSel("select id,nme from node_tab where ste=1"); //节点列表
		  if(empty($nodes)){$nodes="error";}
		  if($id>0){
			  $hstr=HwSel("select id,file,node,ste,dte,tag,nme from file_tab where id=$id",'confinfo');
			  if(empty($hstr)){echo "error\x01confinfo";}else{echo $hstr."\x01".$nodes;}
		  }else{echo "confinfo\x01null\x01".$nodes;}
	 }else{echo "error-gants\x010";exit(0);}
  break;
  case 'confopt': //配置选项列表
     if(Gants(0)||Gants(3)){
			if($id>0){
			   $options=HwSel("select id,nkey,var,son from conf_tab where fid=$id order by id desc",'confopt'); //配置选项列表
			   if(empty($options)){$options="error";}else{echo $options."\x01$id\x01".HwSel("select nme,file from file_tab where id=$id");}
			}else{echo 'error-id';}
	}else{echo "error-gants\x010";exit(0);} 
  break;
  case 'deloption': //删除配置项
      if(Gants(0)||Gants(7)){
			 $id=preg_replace('/[^0-9]/','',HGT('info'));
			 if(!empty($id)){
				 if(HwExec("delete from conf_tab where id=$id")){echo "success\x01$id";WebCenterLogs('删除配置项','conf_tab ID='.$id);}else{echo 'error-sql';}
			 }else{echo 'error-id';}
	 }else{echo "error-gants\x010";exit(0);}
  break;
  default:echo "error\x01cmd";break;
  }
}

/*修改节点*/
function ChNode(){
	$hstr=explode("\x01",HGT('info'));
	if(count($hstr)>6){
		$id=preg_replace('/[^0-9]/','',$hstr[0]); if(strlen($id)<1){echo 'cherror-id';exit(0);}
		$nme=$hstr[1]; if(empty($nme)){echo 'cherror-nme';exit(0);}else{$nme='0x'.strToHex($nme);}
		$ip=preg_replace('/[^0-9.]/','',$hstr[2]); if(empty($ip)){echo 'cherror-ip';exit(0);}
		$port=preg_replace('/[^0-9]/','',$hstr[3]); if(empty($port)||(int)$port>65500||(int)$port<1){echo 'cherror-port';exit(0);}
		$keys=$hstr[4]; if(empty($keys)){echo 'cherror-key';exit(0);}else{$keys='0x'.strToHex($keys);}
		$ste=preg_replace('/[^0-9]/','',$hstr[5]); if(strlen($ste)<1){$ste='0';}
		$tag=$hstr[6]; if(strlen($tag)>0){$tag='0x'.strToHex($tag);}else{$tag='\'\'';}
		if((int)$id>0){$hsql="update node_tab set nme=$nme,ip='$ip',port=$port,hkey=$keys,ste=$ste,tag=$tag where id=$id";} //修改节点信息
		else{//添加新节点
			$dte=date('Y-m-d H:i:s'); $hsql="insert into node_tab(nme,ip,port,ste,dte,hkey,tag)values($nme,'$ip',$port,$ste,'$dte',$keys,$tag)";
		}
		if(HwExec($hsql)){echo 'chsuccess';if((int)$id>0){WebCenterLogs('修改节点信息',$hsql);}else{WebCenterLogs('新增节点',$hsql);}}else{echo 'cherror-sql: ';}
	}else{echo "cherror-num";}
}

/*添加或修改配置文件*/
function CheConf(){
	$hstr=explode("\x01",HGT('info'));
	if(count($hstr)>4){
		$id=preg_replace('/[^0-9]/','',$hstr[0]); if(strlen($id)<1){echo 'cherror-id';exit(0);} //ID编号
		 if(!empty($hstr[1])){$fpath='0x'.strToHex($hstr[1]);}else{echo 'cherror-fpath';exit(0);} //文件路径
		 $node=preg_replace('/[^0-9]/','',$hstr[2]); if(strlen($node)<1){echo 'cherror-node';exit(0);} //所属节点
		 $ste=preg_replace('/[^0-9]/','',$hstr[3]); if(strlen($ste)<1){$ste='0';} //状态
		 if(strlen($hstr[4])<1){$tag="''";}else{$tag='0x'.strToHex($hstr[4]);} //备注
		 if(!empty($hstr[5])){$nme='0x'.strToHex($hstr[5]);}else{$nme='null';}
		 
		 if((int)$id>0){$hsql="update file_tab set nme=$nme,file=$fpath,node=$node,ste=$ste,tag=$tag where id=$id";} //修改配置文件
		 else{ //添加新配置文件
			 $dte=date('Y-m-d H:i:s'); $hsql="insert into file_tab(nme,file,tpe,node,ste,dte,tag)values($nme,$fpath,1,$node,$ste,'$dte',$tag)";
		 }
		 if(HwExec($hsql)){echo 'chsuccess';if((int)$id>0){WebCenterLogs('修改配置文件',$hsql);}else{WebCenterLogs('新增配置文件',$hsql);}}else{echo 'cherror-sql';}
	}else{echo "cherror-num";}
}

/*添加或修改配置项*/
function Chopts(){
	$hstr=explode("\x01",HGT('info'));
	if(count($hstr)>3){
		$id=preg_replace('/[^0-9]/','',$hstr[0]); if(strlen($id)<1){echo 'cherror-id';exit(0);} //ID编号
		if(strlen($hstr[1])<1){$nkey="''";}else{$nkey='0x'.strToHex($hstr[1]);} //所属节点
		if(strlen($hstr[2])<1){$var="''";}else{$var='0x'.strToHex($hstr[2]);} //状态
		$fid=preg_replace('/[^0-9]/','',$hstr[3]); //所属配置文件
		if(empty($hstr[4])){$son='main';}else{$son=$hstr[4];}
		$son='0x'.strToHex($son); //节
		 
		 if((int)$id>0){$hsql="update conf_tab set nkey=$nkey,var=$var,son=$son where id=$id";} //修改配置文件
		 else{ //添加新配置文件
			 if((int)$fid>0){$hsql="insert into conf_tab(fid,nkey,var,act,son)values($fid,$nkey,$var,0,$son)";}else{echo "cherror-fid";exit(0);}
		 }
		 if(HwExec($hsql)){
			 if((int)$id<1){ WebCenterLogs('新增配置项',$hsql);
				 $hsql="select id from conf_tab where fid=$fid and nkey=$nkey and var=$var limit 0,1"; $id=GetColumn($hsql);
				 if(strlen($id)>0){echo "chaddsuccess\x01$id";}else{echo 'cherror-addsql';}
			}else{echo "chsuccess\x01$id";WebCenterLogs('修改配置项',$hsql);}
		}else{echo 'cherror-sql : '.$hsql;}
	}else{echo "cherror-num";}
}

/*用户详细*/
function UserInfo(){
   global $GRUPS;
	$hstr=explode("\x01",HGT('info'));
	$id=preg_replace('/[^0-9]/','',$hstr[0]);
	if((int)$id>0){
		$hstr=HwSel("select id,uid,count(pwd),nme,grup,ste,dte,lte,tag from user_tab where id=$id",'');
		if(empty($hstr)){echo "error-sql";}else{
			$n=count($GRUPS); for($i=0;$i<$n;$i++){$prglst.=$GRUPS[$i]."\x02";}
			if(strlen($prglst)>1){$prglst=substr($prglst,0,strlen($prglst)-1);}
			echo $hstr."\x01".$prglst;
		}
	}else{echo "error-id";}
}
/*保存用户信息*/
function SaveUser(){
	$hstr=explode("\x01",HGT('info'));
	if(count($hstr)>6){
		$id=preg_replace('/[^0-9]/','',$hstr[0]); //ID编号
		if(strlen($hstr[1])>1){$uid='0x'.strToHex($hstr[1]);}else{echo 'error-uid';exit(0);} //用户名(登录名)
		$ste=preg_replace('/[^0-9]/','',$hstr[2]);if(strlen($ste)<1){$ste='0';}else{if((int)$ste>0){$ste='1';}} //状态
		if(strlen($hstr[3])>0){$upwd=md5($hstr[3],false);}else{$upwd='';} //密码
		if(strlen($hstr[4])>0){$nme='0x'.strToHex($hstr[4]);}else{$nme='\'\'';} //用户真名
		$grup=preg_replace('/[^0-9\|]/','',$hstr[5]); //用户权限
		if(strlen($hstr[6])){$tag='0x'.strToHex($hstr[6]);}else{$tag='\'\'';} //备注
		
		if((int)$id>0){
			$hsql="update user_tab set uid=$uid,nme=$nme,ste=$ste,grup='$grup',tag=$tag";if(!empty($upwd)){$hsql.=",pwd='$upwd'";} $hsql.=" where id=$id";
		}else{
			if(strlen($upwd)<32){echo 'error-pwd';exit(0);}
			$hsql="insert into user_tab(uid,pwd,nme,ste,dte,grup,tag)values($uid,'$pwd',$nme,$ste,'".date('Y-m-d H:i:s')."','$grup',$tag)";
		}
		if(HwExec($hsql)){echo 'success';if((int)$id>0){WebCenterLogs('修改用户',$hsql);}else{WebCenterLogs('新增用户',$hsql);}}else{echo 'error-sql';}
	}else{echo 'error-num';}
}


/*配置列表*/
function HwConfs(){$dats=HGT('info');  $pages=(int)$dats*(int)PAGE;  $num=HwPage('file_tab'); $hstr=HwSel("select id,file,node,(select nme from node_tab where id=node) as nodename,ste,nme from file_tab order by id desc limit $pages,".PAGE,'confs'); if(empty($hstr)){echo "error\x01confs";}else{echo $hstr."\x01$num|".PAGE."|$dats\x01".HwSel('select id,nme from node_tab');}}
/*配置文件详细*/
function HwConfList($dats){if($id>0){$hstr=HwSel("select id,nme,ip,port,hkey,ste,act,dte,tag from node_tab where id=$id",'nodeinfo');if(empty($hstr)){echo "error\x01nodeinfo";}else{echo $hstr;}}}
/*节点列表*/
function nodelist(){$dats=HGT('info');  $pages=(int)$dats*(int)PAGE;  $num=HwPage('node_tab'); $hstr=HwSel("select id,nme,ip,ste from node_tab order by id desc limit $pages,".PAGE,'nodeLst'); if(empty($hstr)){echo "error\x01nodelist";}else{echo $hstr."\x01$num|".PAGE."|$dats";}}
/*用户管理*/
function UserAdmin(){$dats=HGT('info'); $pages=(int)$dats*(int)PAGE; $num=HwPage('user_tab'); $hstr=HwSel("select id,uid,nme,ste,lte,tag from user_tab order by id desc limit $pages,".PAGE,'users');if(empty($hstr)){echo "error\x01UserAdmin";}else{echo $hstr."\x01$num|".PAGE."|$dats";}}
/*日志列表*/
function HwLogs(){$dats=HGT('info'); $pages=(int)$dats*(int)PAGE; $num=HwPage('fw_log'); $hstr=HwSel("select id,dte,jet from fw_log order by id desc limit $pages,".PAGE,'logs');if(empty($hstr)){echo "error\x01logs-null";}else{echo $hstr."\x01$num|".PAGE."|$dats";}}

/*查看日志详细*/
function LogsInfo(){
	$id=preg_replace('/[^0-9]/','',HGT('info'));
	if((int)$id>0){
		$hstr=HwSel("select jet,uid,ip,dte,log from fw_log where id=$id",'logsinfo');
		if(empty($hstr)){echo "error-sql\x010";}else{echo $hstr;}
	}else{echo "error-id\x010";}
}

/*文件列表*/
function FileTab($id){
  global $link;
  $result=mysql_query("select id,file,ste,nme from file_tab where node=$id or node=0",$link);
  if($result){
    $hstr="FnLst\x01";
    while($row=mysql_fetch_array($result)){
       $fn=explode('/',$row['file']);
       $hstr.=$row['id']."\x03".$fn[count($fn)-1]."\x03".$row['ste']."\x03".$row['nme']."\x02";
    }
    $n=strlen($hstr); if($n>6){$hstr=substr($hstr,0,$n-1);}
    $nme=HwSel("select nme from node_tab where id=$id",NULL);
    echo $hstr."\x01".$nme."\x01$id";
    mysql_free_result($result);
  }
}
/*任务推送*/
function RunTask(){
	$hstr=explode("\x01",HGT('info'));
	 $fid=preg_replace('/[^0-9]/','',$hstr[0]); $node=preg_replace('/[^0-9]/','',$hstr[1]);
	 if(!empty($node)){$node=GetColumn("select ip from node_tab where id=$node");}else{echo 'error-node: '.$hstr[1];exit();}
	 if(!empty($node)&&!empty($fid)){
        $ftr=HwCheckFile($fid);
        if(!empty($ftr)){HwCreateTask($node,$fid,HwCrypt_Encode($ftr));}
     }else{echo 'error-idornode';}
}
/*任务状态检查*/
function CheckTaskState(){
	global $link;
	$tkey=HGT('info'); if(strlen($tkey)>30){$tkey='0x'.strToHex($tkey);}else{echo "keyerr\x01";exit();}
	$hsql='select ste,msg from task_tab where tkey='.$tkey;
	if($result=mysql_query($hsql,$link)){ $row=mysql_fetch_array($result);
		switch((int)$row['ste']){
			case 0:echo "wait\x01";break;
			case 1:HwExec('delete from task_tab where tkey='.$tkey); echo "success\x01";break;
			case -1:echo "error\x01".$row['msg'];break;
			default:echo "unknowerror\x01";break;
		}
		mysql_free_result($result);
    }else{echo "chkerror\x01";}
}

//修改状态
function chste($id,$act,$class){
  $restr='';
  switch($class){
  case 'urmgt':if(HwExec("update user_tab set ste=$act where id=$id")){$restr="success\x01userste";WebCenterLogs('修改用户状态');}else{$restr="error\x01userste";}break; //用户状态修改
  case 'node':if(HwExec("update node_tab set ste=$act where id=$id")){$restr="success\x01node-ste";WebCenterLogs('修改节点状态');}else{$restr="error\x01node-ste";}break; //节点状态修改
  case 'confs':if(HwExec("update file_tab set ste=$act where id=$id")){$restr="success\x01confs-ste";WebCenterLogs('修改配置文件状态');}else{$restr="error\x01confs-ste";}break; //配置文件状态修改
  default:$restr="error\x01cmd";break; //花生错误
  }
  return $restr;
}

//首页信息
function MainInfo(){
  $test='<textarea style="border:1px solid green;width:300px;height:60px;" id="showtxt"></textarea><br/><input type="text" value="" id="testval"/><input type="button" value="编码测试"  onclick="document.getElementById(\'showtxt\').innerHTML=HwCode(document.getElementById(\'testval\').value);"/>';
  $hstr="info\x01这是首页~!<br/>$test";
  echo $hstr;
}

?>
