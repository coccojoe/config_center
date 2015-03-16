/*用户管理(列表)*/
function UserAdmin(dats){
	$.post(PstFile[0],{"class":'urmgt',info:dats},function(data,status){
		if("success"==status){if(""!=data){ var str=data.split("\x01");
				switch(str[0]){
					case "users": //成功返回
							var spin=str[1].split("\x02"),  pages=str[2].split("|"), i=0, n=parseInt(pages[0]), m=parseInt(pages[2]);
							var lstr="",tstr='<div class="table"><img src="img/bg-th-left.gif" class="left"/><img src="img/bg-th-right.gif" class="right"/><table class="listing" cellpadding="0" cellspacing="0">\
							<tr onmouseover="$(this.parentNode).find(\'tr\').css(\'background\',\'#D8D8D8\')"><th class="first" width="80" style="text-align:center">编号</th><th width="177">登录名</th><th width="130">真实姓名</th><th>状态</th><th>最后登录</th><th class="last">&nbsp;操&nbsp;作</th></tr>';
							for(i=0;i<spin.length;i++){
								var lg=spin[i].split("\x03");
										lstr+='<tr style="font-weight:bold;"  onmouseover="SelColumn(this);"><td class="first" style="text-align:center;">'+lg[0]+'</td><td>'+lg[1]+'</td><td>'+lg[2]+'</td><td id="ste'+lg[0]+'">';
										if(parseInt(lg[3])>0){lstr+='<span class="act_on"  onclick="hfwprint(\'useroff|'+lg[0]+'|ste'+lg[0]+'\');">已启用</span>';}else{lstr+='<span class="act_off" onclick="hfwprint(\'useron|'+lg[0]+'|ste'+lg[0]+'\');">未启用</span>';}
										lstr+='</td><td>'+lg[4]+'</td><td class="actd"><a onclick="HwUserInfo('+lg[0]+');">详细</a>&nbsp;|&nbsp;<img onclick="DelUser('+lg[0]+');" src="img/hr.gif" title="删除用户"/></td></tr>';
							}
							lstr=tstr+lstr+'</table><div id="reach"  onmouseover="SelColumn(this);"><div id="rhri"></div><div id="rhcn"><span style="float:left;"><a href="javascript:void(0);" onclick="HwUserInfo(0);" style="font-weight:bold;color:blue;line-height:26px;">添加人员</a></span>\
						<span style="float:right;"><strong style="margin-left:15px;margin-right:15px;">总记录数: <font color=green>'+pages[1]+'</font>&nbsp;条</strong><select style="width:100px;font-weight:bold;" id="pagenum"  onchange="UserAdmin(this.value);">';
						for(var i=0;i<n;i++){if(i==m){lstr+='<option value="'+i+'" selected >第&nbsp;'+(i+1)+'&nbsp;页</option>';}else{lstr+='<option value="'+i+'">第&nbsp;'+(i+1)+'&nbsp;页</option>';}}
						lstr+='</select><strong style="margin-left:15px;">总页数: <font color=green>'+pages[0]+'</font> 页<strong>&nbsp;&nbsp;</span>\
						</div><div id="rhle"></div></div>';
						$("#middle").html(lstr);
				break;
			    default:PublicReturn(data);break;
		    }
		}else{$("#middle").html("");alert("发生错误~!");}
    }else{$("#middle").html("");alert("post 通信错误~! 数据发送失败~!");}  //发送失败~!
  });
}

/*用户详细信息*/
function HwUserInfo(id){
	var chdats='<table class="adms_info_css">\
	<tr><td class="right">登录名</td><td><input type="text" id="uid" style="margin-right:30px;"/><select id="ste" style="font-weight:bold;"><option value="0" style="color:red;">禁用</option><option value="1" style="color:green;">启用</option></select></td></tr>\
	<tr><td class="right">登录密码</td><td><input type="hidden" value="" id="upwdrw"/><input type="password" id="upwd"/></td></tr>\
	<tr><td class="right">真名</td><td><input type="text" id="unme"/></td></tr>\
	<tr><td class="right" >权限</td><td><div id="grup"  onselectstart="return false;"></div></td></tr>\
	<tr><td class="right">最后登录</td><td id="lte"></td></tr><tr><td class="right">创建时间</td><td id="dte"></td></tr>\
	<tr><td class="right">备注</td><td><textarea id="tag" style="width:300px;height:80px;"></textarea></td></tr>\
	<tr height="50"><td colspan="2" style="text-align:center;"><input type="button" value="提交保存" id="UserSaveBut"/><span id="dialog_state" style="font-weight:bold;"></span></td></tr>\
	<table/>';

	var HwNinf=$.dialog({id:'HwNinf',lock: true, max: false,height:"400px", width:"500px",title:"用户详细",content:chdats});  //浮动窗口
	
	//密码输入框失去焦点(触发)
	$("#upwd").blur(function () {
		if($("#upwd").val()!=$("#upwdrw").val()){$("#upwd").css("border","1px solid red");}else{if($("#upwd").val().length>1){$("#upwd").css("border","1px solid green");}}
      });
	
	//状态选项发生改变(触发)
	$("#ste").change(function(){
		if("0"==$("#ste").val()){$("#ste").css("color","red");}else{$("#ste").css("color","green");}
	});
	
	if(0==id){ //添加用户
		HwNinf.title("添加配置文件");
		$("#uid").val("");
		$("#upwd").val(""); $("#upwdrw").val("");
		$("#upwd").css("border","1px solid red");
		$("#ste").val("0"); $("#ste").css("color","red");
		$("#dte").html("");
		$("#lte").html("");
		$("#tag").val("");
		$("#UserSaveBut").attr("onclick","SaveUser(0);");
		
	}else{ //查看或修改
		HwNinf.title("查看或修改用户信息");
		$.post(PstFile[0],{"class":"userinfo","info":id},function(data,status){
			if("success"==status){
				if(""!=data){ var hstr=data.split("\x01");
					var lg=hstr[0].split("\x03");
					var gants=hstr[1].split("\x02");
					 if(lg.length>5){
							$("#uid").val(lg[1]);
							if(parseInt(lg[2])>0){$("#upwdrw").val("********");}else{$("#upwdrw").val("");} $("#upwd").val($("#upwdrw").val());
							$("#unme").val(lg[3]);
							
							for(var i=0;i<gants.length;i++){
								var gnt=gants[i].split("\x03");
								var chkbox='<div><input type="checkbox" id="cbox['+gnt[0]+']" value="'+gnt[0]+'"/><a onclick="CheckStatus('+gnt[0]+');">'+gnt[1]+'</a></div>';
								$("#grup").append(chkbox);
							}

							 var grup=lg[4].split("|");  for(var i=0;i<grup.length;i++){$("#cbox\\["+grup[i]+"\\]").attr("checked","true");}

							$("#ste").val(lg[5]); if(parseInt(lg[5])>0){$("#ste").css("color","green");}else{$("#ste").css("color","red");}
							$("#dte").html(lg[6]);
							$("#lte").html(lg[7]);
							$("#tag").val(lg[8]);
							$("#UserSaveBut").attr("onclick","SaveUser("+lg[0]+");");
					 }else{
						 switch(lg[0]){
						    case "error-sql":alert("查询返回错误~!");break;
						    case "error-id":alert("用户ID错误~!");break;
						    default:alert("未知错误~!: ".data);break;
					     }
					}
				}else{alert("数据返回异常: NULL");}
			}else{alert("返回错误~! POST未成功~!");}
	    });
	}
}

/*保存用户信息*/
function SaveUser(id){
	 var hstr=id.toString()+"\x01"+$("#uid").val()+"\x01"+$("#ste").val();
	 if($("#upwd").val()!=$("#upwdrw").val()){hstr+="\x01"+$("#upwd").val();}else{hstr+="\x01";}
	 hstr+="\x01"+$("#unme").val();
	//用户权限
	var hnode=$("#grup").find($("input")); var gntr="";
	for(var i=0;i<hnode.length;i++){if(hnode[i].checked){gntr+=hnode[i].value+"|";}}
	if(gntr.length>1){gntr=gntr.substring(0,gntr.length-1);}
	
	hstr+="\x01"+gntr+"\x01"+$("#tag").val();
	
	$.post(PstFile[0],{"class":"saveuser","info":hstr},function(data,status){
			if("success"==status){
				if(""!=data){ var hstr=data.split("\x01");
					switch(hstr[0]){
						case "success":/*alert("保存操作完成~!");*/$("#UserSaveBut").hide();$("#dialog_state").html("<font color=green>保存操作完成~!</font>");$.dialog.list.HwNinf.time(2,UserAdmin(0));break;
						case "error-id":alert("ID错误~!");break;
						case "error-uid":alert("用户名错误~!");break;
						case "error-pwd":alert("错误~!密码为空~!");break;
						case "error-num":alert("POST数据格式错误~!");break;
						case "error-sql":alert("保存失败～！SQL返回失败～！");break;
						default:alert("未知错误~!");break;
					}
				}else{alert("数据返回异常: NULL");}
			}else{alert("返回错误~! POST未成功~!");}
	});
	
}

/*日志列表*/
function HwLogs(dats){
	$.post(PstFile[0],{"class":'logs',"info":dats},function(data,status){
	if("success"==status){if(""!=data){ var str=data.split("\x01");
			switch(str[0]){
				case "logs": //成功返回
						var spin=str[1].split("\x02"), pages=str[2].split("|"), i=0, n=parseInt(pages[0]), m=parseInt(pages[2]);
						var lstr="",tstr='<div class="table"><img src="img/bg-th-left.gif" class="left"/><img src="img/bg-th-right.gif" class="right"/><table class="listing" cellpadding="0" cellspacing="0">\
						<tr onmouseover="$(this.parentNode).find(\'tr\').css(\'background\',\'#D8D8D8\')"><th class="first" style="text-align:center;width:23px;">ID</th><th width=180>时间</th><th width=500>事件</th><th class="last">操作</th></tr>';
						for(i=0;i<spin.length;i++){ var lg=spin[i].split("\x03");
							if(lg.length>2){lstr+='<tr onmouseover="SelColumn(this);"><td class="first" style="font-weight:bold;">'+lg[0]+'</td><td>'+lg[1]+'</td><td>'+lg[2]+'</td><td class="actd"><a onclick="LogsInfo('+lg[0]+');">详细</a></td></tr>';}
						}
						lstr=tstr+lstr+'</table><div id="reach"  onmouseover="SelColumn(this);"><div id="rhri"></div><div id="rhcn">\
					<span style="float:right;"><strong style="margin-left:15px;margin-right:15px;">总记录数: <font color=green>'+pages[1]+'</font>&nbsp;条</strong><select style="width:100px;font-weight:bold;" id="pagenum"  onchange="HwLogs(this.value);">';
					for(var i=0;i<n;i++){if(i==m){lstr+='<option value="'+i+'" selected >第&nbsp;'+(i+1)+'&nbsp;页</option>';}else{lstr+='<option value="'+i+'">第&nbsp;'+(i+1)+'&nbsp;页</option>';}}
					lstr+='</select><strong style="margin-left:15px;">总页数: <font color=green>'+pages[0]+'</font> 页<strong>&nbsp;&nbsp;</span></div><div id="rhle"></div></div>';
					$("#middle").html(lstr);
				break;
			    default:PublicReturn(data);break;
		    }
		}else{$("#middle").html("");alert("发生错误~!");}
    }else{$("#middle").html("");alert("post 通信错误~! 数据发送失败~!");}  //发送失败~!
  });
}
/*查看日志详细*/
function LogsInfo(id){
	var chdats='<table class="logs_info_css">\
	<tr><td class="right">操作说明</td><td id="jet" style="width:380px;"></td></tr>\
	<tr><td class="right">操作用户</td><td id="uid"></tr>\
	<tr><td class="right">IP地址</td><td id="ipaddr"></td></tr>\
	<tr><td class="right">操作时间</td><td id="dte"></td></tr>\
	<tr><td colspan=2 ><span style="line-height:20px;font-weight:bold;">详细信息</span><br/><textarea id="info" style="width:100%;height:160px;" readonly ></textarea></td></tr>\
	<tr height="50"><td colspan="2" style="text-align:center;"><input type="button" value="关闭返回" id="CloseDlg"/></td></tr>\
	<table/>';
	
	var HwNinf=$.dialog({id:'HwNinf',lock: true, max: false,height:"400px", width:"500px",title:"日志详情",content:chdats});  //浮动窗口
	
	$.post(PstFile[0],{"class":'logsinfo',"info":id},function(data,status){
		 if("success"==status){
			 if(""!=data){ var Rets=data.split("\x01");
						switch(Rets[0]){
							case "logsinfo": //成功返回
								var hstr=Rets[1].split("\x03");
								if(hstr.length>4){
									$("#jet").html(hstr[0]);
									$("#uid").html(hstr[1]);
									$("#ipaddr").html(hstr[2]);
									$("#dte").html(hstr[3]);
									$("#info").val(hstr[4]);
								}
							break;
							default:PublicReturn(data);break;
						}
					}else{alert("数据返回异常: NULL");}
		 }else{alert("返回错误~! POST未成功~!");}
	});
}

/*选中与取消选中*/
function CheckStatus(id){
	if($("#cbox\\["+id+"\\]")[0].checked==true){$("#cbox\\["+id+"\\]")[0].checked=false;}else{$("#cbox\\["+id+"\\]")[0].checked=true;}
}
