/*切换窗口*/
function hfwin(helem,scwin){
	var i=0,fwin=helem.parentNode,chwin=fwin.childNodes;
	for(i=0;i<chwin.length;i++){if("LI"==chwin[i].tagName){chwin[i].className="";}}
	helem.className="active";

	switch(scwin){
		case 0:hfwprint("main");break; //首页
		case 1:UserAdmin(0);break; //用户管理页
		case 2:NodeList(0);break; //节点列表
		case 3:HwConfs(0);break; //配置文件列表
		case 4:HwLogs(0);break; //日志列表
	}
}

/*输出内容*/
function hfwprint(hwclass){
	var modbck=null;var cnstr=$("#middle");
	$.post(PstFile[0],{"class":hwclass},function(data,status){
		if("success"==status){
			if(""!=data){
				FnLst.hide(); var hstr=data.split("\x01");
				switch(hstr[0]){
				case "info":cnstr.html(hstr[1]);break;//首页信息
				case "FnLst":if(hstr.length>2){FnLst.title("["+hstr[2]+"]=>文件列表");} FnLst.show(); FnLst.content(showfilelist(hstr[1],hstr[3])); break;//文件列表
				case "nodeinfo":NodeInfo(hstr[1]);break; //节点信息
				case "confinfo":if(hstr.length>2){ConFileInfo(hstr[1],hstr[2]);}else{ConFileInfo(hstr[1],null);}break; //配置文件详细
				case "success":Succdlg(hstr[1]);break;
				default:PublicReturn(data);break;
				}
			}else{cnstr.html("<font color=red>发生错误~!</font>");}
		}else{cnstr.html("<span color='color:red;'>Some Error:</span><br/>post 通信错误~! 数据发送失败~!");}  //发送失败~!
        });
}

/*成功返回*/
function Succdlg(str){
   var spin=str.split("\x02");
   switch(spin[0]){
   case "node-ste": $("#"+spin[1]).html(spin[2]); break; //节点状态修改成功
   case "userste": $("#"+spin[1]).html(spin[2]); break;//用户状态修改成功
   case "confs-ste":$("#"+spin[1]).html(spin[2]); break; //配置文件状态修改成功
   default:alert("成功返回 但分类未知~!");break;
   }
}

/*节点列表*/
function NodeList(dats){
	$.post(PstFile[0],{"class":'node',"info":dats},function(data,status){
		if("success"==status){
			if(""!=data){ var str=data.split("\x01");
					switch(str[0]){
						case "nodeLst": //成功返回
								var spin=str[1].split("\x02"), pages=str[2].split("|"), i=0, n=parseInt(pages[0]), m=parseInt(pages[2]);
								var lstr="",tstr='<div class="table"><img src="img/bg-th-left.gif" class="left"/><img src="img/bg-th-right.gif" class="right"/><table class="listing" cellpadding="0" cellspacing="0">\
								<tr onmouseover="$(this.parentNode).find(\'tr\').css(\'background\',\'#D8D8D8\')"><th class="first" style="text-align:center;width:23px;">ID</th><th width=230>节点名称</th><th width=130>IP地址</th><th width=60>状态</th><th class="last">操&nbsp;&nbsp;&nbsp;&nbsp;作</th></tr>';
								for(i=0;i<spin.length;i++){
									var lg=spin[i].split("\x03");
									lstr+='<tr onmouseover="SelColumn(this);"><td class="first" >'+lg[0]+'</td><td style="font-weight:bold;color:#873324;">'+lg[1]+'</td><td>'+lg[2]+'</td><td id="ste'+lg[0]+'">';
									if(parseInt(lg[3])>0){lstr+='<span class="act_on"  onclick="hfwprint(\'nodeoff|'+lg[0]+'|ste'+lg[0]+'\')">已启用</span>';}else{lstr+='<span class="act_off" onclick="hfwprint(\'nodeon|'+lg[0]+'|ste'+lg[0]+'\');">未启用</span>';}
									lstr+='</td><td class="actd">\
									<a onclick="hfwprint(\'nodeinfo|'+lg[0]+'\');">节点详细</a>&nbsp;|&nbsp;\
									<a onclick="FnLst.show();hfwprint(\'flst|'+lg[0]+'\');">配置文件</a>&nbsp;|&nbsp;\
									<a onclick="alert(\'wait...\');" alt="推送所有配置文件">推送</a>\
									</td>';
								}
								lstr=tstr+lstr+'</table><div id="reach" onmouseover="SelColumn(this);"><div id="rhri"></div><div id="rhcn"><span style="float:left;"><a href="javascript:void(0);" onclick="NodeInfo(null);" style="font-weight:bold;color:blue;line-height:26px;">添加节点</a></span>\
							<span style="float:right;"><strong style="margin-left:15px;margin-right:15px;">总记录数: <font color=green>'+pages[1]+'</font>&nbsp;条</strong><select style="width:100px;font-weight:bold;" id="pagenum"  onchange="NodeList(this.value);">';
							for(i=0;i<n;i++){if(i==m){lstr+='<option value="'+i+'" selected >第&nbsp;'+(i+1)+'&nbsp;页</option>';}else{lstr+='<option value="'+i+'">第&nbsp;'+(i+1)+'&nbsp;页</option>';}}
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

/*显示文件列表*/
function showfilelist(str,nid){
	var i=0,spin=str.split("\x02");
	var lstr='<table style="line-height:23px;">',hstr="";
	for(i=0;i<spin.length;i++){
		var lg=spin[i].split("\x03");
		if(parseInt(lg[2])>0){
			lstr+='<tr><td style="font-weight:bold;width:200px;">'+lg[3]+'</td><td>'+lg[1]+'</td><td style="width:50px;text-align:center;"><a class="act_on" onclick="CreateTask('+lg[0]+','+nid+');">推送</a></td></tr>';
		}else{
			lstr+='<tr><td style="font-weight:bold;width:200px;">'+lg[3]+'</td><td>'+lg[1]+'</td><td style="width:50px;text-align:center;"><font color=red>已经禁用</font></td></tr>';
		}
	}
	lstr+='<tr><td colspan="2" class="actd" style="text-align:center;"><a>全部推送</a></td></tr></table>';
	return lstr;
}

/*查看节点信息*/
function NodeInfo(str){
	var chdats='<table class="node_info_css">\
	<tr><td class="right">名称</td><td><input type="text" value="" id="nme"/></td></tr>\
	<tr><td class="right">节点IP</td><td><input type="text" value="" id="ip"/>&nbsp;端口号:<input type="text" value="" id="port" style="width:60px;"/></td></tr>\
	<tr><td class="right">节点密钥</td><td><input type="text" value="" id="keys" style="width:300px;"/></td></tr>\
	<tr><td class="right">状态</td><td><select id="ste" style="font-weight:bold;"><option value="0" style="color:red;">禁用</option><option value="1" style="color:green;">启用</option></select></td></tr>\
	<tr><td class="right">签到时间</td><td id="pte"></td></tr>\
	<tr><td class="right">创建时间</td><td id="dte"></td></tr>\
	<tr><td class="right">备注</td><td><textarea id="tag" style="width:300px;height:80px;"></textarea></td></tr>\
	<tr height="50"><td colspan="2" style="text-align:center;"><input type="button" value="提交保存" id="NodeSave"/><span id="dialog_state" style="font-weight:bold;"></span></td></tr>\
	<table/>';

	var HwNinf=$.dialog({id:'HwNinf',lock: true, max: false,height:"400px", width:"500px",title:"节点详细",content:chdats});  //浮动窗口
	
	//选择状态时(触发)
	$("#ste").change(function(){
		 if("0"==$("#ste").val()){$("#ste").css("color","red");}else{$("#ste").css("color","green");}
	});
	
	if(null==str){ //添加节点
		HwNinf.title("添加节点");
		$("#nme").val("");
		$("#ip").val("");
		$("#port").val("");
		$("#keys").val("");
		$("#ste").find("option[value='0']").attr("selected",true); $("#ste").css("color","red");
		$("#pte").html("");
		$("#dte").html("");
		$("#tag").val("");
		$("#NodeSave").attr("onclick","ChangeNode(0);");
	}else{ //查看或修改
		HwNinf.title("查看或修改节点");
		var lg=str.split("\x03");
		if(lg.length>8){
			$("#nme").val(lg[1]);
			$("#ip").val(lg[2]);
			$("#port").val(lg[3]);
			$("#keys").val(lg[4]);
			if(parseInt(lg[5])>0){$("#ste").find("option[value='1']").attr("selected",true);$("#ste").css("color","green");}else{$("#ste").find("option[value='0']").attr("selected",true);$("#ste").css("color","red");}
			$("#pte").html(lg[6]);
			$("#dte").html(lg[7]);
			$("#tag").val(lg[8]);
			$("#NodeSave").attr("onclick","ChangeNode("+lg[0]+");");
		}else{alert(lg.length);}
	}
}

/*保存节点信息*/
function ChangeNode(id){
	var hstr=id.toString()+"\x01"+$("#nme").val();
	hstr+="\x01"+$("#ip").val();
	hstr+="\x01"+$("#port").val();
	hstr+="\x01"+$("#keys").val();
	hstr+="\x01"+$("#ste").val();
	hstr+="\x01"+$("#tag").val();
	
	$.post(PstFile[0],{"class":"chnode","info":hstr},function(data,status){
		if("success"==status){
			if(""!=data){ var hstr=data.split("\x01");
				switch(hstr[0]){
					case "chsuccess":$("#NodeSave").hide();$("#dialog_state").html("<font color=green>保存操作完成～！[3秒后关闭]</font>");$.dialog({id: 'HwNinf'}).time(1,NodeList(0));break;
					case "cherror-id":alert("ID错误~!");break;
					case "cherror-nme":alert("名称不能为空~!");break;
					case "cherror-ip":alert("IP错误或不能为空~!");break;
					case "cherror-keys":alert("Keys错误或不能为空~!");break;
					case "cherror-num":alert("数据不完整~!");break;
					default:PublicReturn(data);break;
				}
			}else{alert("数据返回异常: NULL");}
		}else{alert("返回错误~! POST未成功~!");}
	});
}

/*查看配置文件信息*/
function ConFileInfo(str,ndes){
	var chdats='<table class="node_info_css">\
	<tr><td class="right">别名</td><td><input type="text" value="" id="nme" system="width:100px;"/></td></tr>\
	<tr><td class="right">文件路径</td><td><input type="text" value="" id="fpath" style="width:300px;"/></td></tr>\
	<tr><td class="right">所属节点</td><td><select id="node"></select></tr>\
	<tr><td class="right">状态</td><td><select id="ste" style="font-weight:bold;"><option value="0" style="color:red;">禁用</option><option value="1" style="color:green;">启用</option></select></td></tr>\
	<tr><td class="right">创建时间</td><td id="dte"></td></tr>\
	<tr><td class="right">备注</td><td><textarea id="tag" style="width:300px;height:80px;"></textarea></td></tr>\
	<tr height="50"><td colspan="2" style="text-align:center;"><input type="button" value="提交保存" id="ConfSave"/><span id="dialog_state" style="font-weight:bold;"></span></td></tr>\
	<table/>';

	var HwNinf=$.dialog({id:'HwNinf',lock: true, max: false,height:"400px", width:"500px",title:"配置文件详情",content:chdats});  //浮动窗口
	
	//选择状态时(触发)
	$("#ste").change(function(){
		 if("0"==$("#ste").val()){$("#ste").css("color","red");}else{$("#ste").css("color","green");}
	});
	
	if("error"!=ndes){ var nds=ndes.split("\x02"),options="";
			for(var i=0;i<nds.length;i++){var lg=nds[i].split("\x03"); if(lg.length>1){options+='<option value="'+lg[0]+'">'+lg[1]+'</option>';}}
			$("#node").html('<option value="0">公共</option>'+options);
	 }
	
	if(null==str||'null'==str){ //添加配置文件
		HwNinf.title("添加配置文件");
		$("#fpath").val("");
		$("#node").val("0");
		$("#ste").val("0");  $("#ste").css("color","red");
		$("#tag").val("");
		$("#ConfSave").attr("onclick","SaveConFile(0);");
	}else{ //查看或修改
		HwNinf.title("查看或修改配置文件");
		var lg=str.split("\x03");
		if(lg.length>5){
			$("#fpath").val(lg[1]);
			$("#node").val(lg[2]);
			if(parseInt(lg[3])>0){$("#ste").find("option[value='1']").attr("selected",true);$("#ste").css("color","green");}else{$("#ste").find("option[value='0']").attr("selected",true);$("#ste").css("color","red");}
			$("#dte").html(lg[4]);
			$("#tag").val(lg[5]);
			$("#nme").val(lg[6]);
			$("#ConfSave").attr("onclick","SaveConFile("+lg[0]+");");
		}else{alert(lg.length);}
	}
}

/*保存配置文件*/
function SaveConFile(id){
	var hstr=id.toString()+"\x01"+$("#fpath").val(); //ID， 路径
	hstr+="\x01"+$("#node").val(); //所属节点
	hstr+="\x01"+$("#ste").val(); //配置文件状态
	hstr+="\x01"+$("#tag").val(); //备注
	hstr+="\x01"+$("#nme").val(); //名称或说明
	
   $.post(PstFile[0],{"class":"chfile","info":hstr},function(data,status){
		if("success"==status){
			if(""!=data){ var hstr=data.split("\x01");
				switch(hstr[0]){
					case "chsuccess":$("#ConfSave").hide();$("#dialog_state").html("<font color=green>保存操作完成～！[3秒后关闭]</font>");$.dialog({id: 'HwNinf'}).time(1,HwConfs(0)); break;
					case "cherror-fpath":alert("文件名(路径)不能为空~!");break;
					case "cherror-node":alert("节点不能为空~!");break;
					default:PublicReturn(data);break;
				}
			}else{alert("数据返回异常: NULL");}
		}else{alert("返回错误~! POST未成功~!");}
	});
}

/*配置文件列表*/
function HwConfs(dats){
	$.post(PstFile[0],{"class":'confs',"info":dats},function(data,status){
		if("success"==status){
			if(""!=data){ var str=data.split("\x01"); var i=0; var NodeStr='<option value="0">所有节点</option>'; //alert(str[3]);
				     var ngs=str[3].split("\x02"); for(i=0;i<ngs.length;i++){var ng=ngs[i].split('\x03'); NodeStr+='<option value="'+ng[0]+'">'+ng[1]+'</option>';}
					switch(str[0]){
						case "confs": //成功返回
						        var spin=str[1].split("\x02"), pages=str[2].split("|"), n=parseInt(pages[0]), m=parseInt(pages[2]);
								var lstr="",tstr='<div style="text-align:center;margin-bottom:3px;display:none;"><span><select id="NodeID" style="width:130px;font-weight:bold;color:green;">'+NodeStr+'</select></span></div><div class="table"><img src="img/bg-th-left.gif" class="left"/><img src="img/bg-th-right.gif" class="right"/><table class="listing" cellpadding="0" cellspacing="0">\
								<tr onmouseover="$(this.parentNode).find(\'tr\').css(\'background\',\'#D8D8D8\')"><th class="first" style="text-align:center;width:23px;">ID</th><th width=200>别名</th><th width=300>文件路径</th><th width=100>所属节点</th><th width=60>状态</th><th class="last">操&nbsp;&nbsp;&nbsp;&nbsp;作</th></tr>';
								for(i=0;i<spin.length;i++){
									var lg=spin[i].split("\x03");
									if(parseInt(lg[2])<1){lg[3]="公共";}
									lstr+='<tr onmouseover="SelColumn(this);"><td class="first">'+lg[0]+'</td>\
									<td style="font-weight:bold;color:#873324;">'+lg[5]+'</td>\
									<td><input type="text" value="'+lg[1]+'" style="width:100%;background-color:transparent;border:0px;" readonly /></td><td style="font-weight:bold;">'+lg[3]+'</td><td id="ste'+lg[0]+'">';
									if(parseInt(lg[4])>0){lstr+='<span class="act_on"  onclick="hfwprint(\'cnfoff|'+lg[0]+'|ste'+lg[0]+'\')">已启用</span>';}else{lstr+='<span class="act_off" onclick="hfwprint(\'cnfon|'+lg[0]+'|ste'+lg[0]+'\');">未启用</span>';}
									lstr+='</td><td class="actd">\
									<a onclick="hfwprint(\'confinfo|'+lg[0]+'\');">详细</a>&nbsp;|&nbsp;\
									<a onclick="HwConfig('+lg[0]+');">配置项</a>&nbsp;|\
									</td>';
								}
							lstr=tstr+lstr+'</table><div id="reach" onmouseout="SelColumn(this);"><div id="rhri"></div><div id="rhcn"><span style="float:left;"><a href="javascript:void(0);" onclick="hfwprint(\'confinfo|0\');" style="font-weight:bold;color:blue;line-height:26px;">添加文件</a></span>\
							<span style="float:right;"><strong style="margin-left:15px;margin-right:15px;">总记录数: <font color=green>'+pages[1]+'</font>&nbsp;条</strong><select style="width:100px;font-weight:bold;" id="pagenum"  onchange="HwConfs(this.value)">';
					       for(i=0;i<n;i++){if(i==m){lstr+='<option value="'+i+'" selected >第&nbsp;'+(i+1)+'&nbsp;页</option>';}else{lstr+='<option value="'+i+'">第&nbsp;'+(i+1)+'&nbsp;页</option>';}}
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

/*显示配置项*/
function HwConfig(id){
	$.post(PstFile[0],{"class":"confopt|"+id.toString()},function(data,status){
		if("success"==status){
			if(""!=data){ var hstr=data.split("\x01");
				switch(hstr[0]){
					case "confopt": //成功返回
					    var i=0,n=0,Fstr=hstr[3].split("\x03"),pth=null,Ftype=""; 
						var lstr='',tstr='',dstr='<div class="cnfopt"></div><input type="hidden" id="Fid" value="'+hstr[2]+'" /><p id="ButBars"  style="margin-bottom:0px;text-align:center;"></p>'; 
						var HwNinf=$.dialog({id:'HwNinf',lock: true, max: false,height:"650px", width:"830px",title:'<font color=#8A2BE2>'+Fstr[0]+"</font>[ <font color=#872657>"+Fstr[1]+"</font> ]",content:dstr});  //浮动窗口
						
						pth=Fstr[1].split("\/");
						 Ftype="."+pth[pth.length-1].split(".")[1];
						switch(Ftype){
							case ".ini":
									tstr='<table style="position:absolute;margin-left:1px;"><tr><th style="width:45px;border-left:0px;">编号</th><th width=85 >Key</th><th width=500 >值</th><th width=61 >节项</th><th width=80 class="actd" style="border-right:0px;"><a id="addoptbut">添加项</a></th></tr>\
									<tr id="newopts"><td>0</td><td><input type="text" id="newnkey" value="" /></td><td><input type="text" id="newvark" value="" /></td><td><input type="text" id="newsson" value="" style="font-weight:bold;width:51px;"/></td><td class="actd" ><img  src="img/icn_rig.png" title="保存添加" style="margin-left:23px;" onclick="HwSaveConf(0);"/></td></tr>\
									</table><table id="conftabs"><tr><th style="width:45px;border-left:1px solid #FAEBD7;">编号</th><th width=85 >Key</th><th width=61 >值</th><th>节</th><th style="width:80px; border-right:1px solid #FAEBD7;">操作</th></tr></table>';
									$(".cnfopt").html(tstr); var spin=hstr[1].split("\x02");
									for(i=0;i<spin.length;i++){  var lg=spin[i].split("\x03");
										if(lg.length>2){
											lstr='<tr id="cnfid['+lg[0]+']"><td align=center>'+i+'</td>\
											<td><input type="text" id="nkey['+lg[0]+']" value="'+lg[1]+'" style="width:80px;font-weight:bold;" alt="'+encodeURIComponent(lg[1])+'" onBlur="ConfonBlur(this);"/></td>\
											<td><input type="text" id="vark['+lg[0]+']" value=""  style="width:500px;"  alt="'+encodeURIComponent(lg[2])+'" onBlur="ConfonBlur(this);"/></td>\
											<td><input type="text" id="sson['+lg[0]+']" style="font-weight:bold;width:60px;" value="'+encodeURIComponent(lg[3])+'"/></td>\
											<td class="actd" style="text-align:center;"><img onclick="HwSaveConf('+lg[0]+');"  src="img/save-icon.gif" title="保存修改"/>&nbsp;|&nbsp;<img onclick="DelConfOption('+lg[0]+');" src="img/hr.gif" title="删除该项"/></td></tr>';
											
											$("#conftabs").append(lstr);   $("#nkey\\["+lg[0]+"\\]").val(lg[1]);    $("#vark\\["+lg[0]+"\\]").val(lg[2]);   $("#sson\\["+lg[0]+"\\]").val(lg[3]);
										}
									}
									//点击"添加项"时候的动作
									$("#addoptbut").click(function(){
										if($("#newopts").is(":hidden")){$("#newopts").show();$("#addoptbut").html("添加项↑");}else{$("#newopts").hide();$("#addoptbut").html("添加项");}
									});
									//回车触发添加动作
									$("#newvark").keydown(function(e){if(e.keyCode==13){HwSaveConf(0);}});
								break;
								default: //默认
									tstr='<textarea id="code" style="width:99%;height:99%;"></textarea>';
									$(".cnfopt").html(tstr); var spin=hstr[1].split("\x02");
									var lg=spin[0].split("\x03"); $("#code").text(lg[2]);
									var CodeObject={lineNumbers: true,matchBrackets: true,indentUnit: 4,indentWithTabs: true};
									CodeEditor=CodeMirror.fromTextArea(document.getElementById("code"),CodeObject);
									CodeEditor.setSize($(".cnfopt").width(),$(".cnfopt").height());
									$(".cnfopt").css("border-top","1px solid #872657");
									$("#ButBars").html('<input type="button" value="保存修改" onclick="HwConfSave('+lg[0]+');"/>');
								break;
							}
				break;
				default:PublicReturn(data);break;
		   }
	    }else{alert("数据返回异常: NULL");}
     }else{alert("返回错误~! POST未成功~!");}
	});
}
/*配置项编辑框失去焦点时触发*/
function ConfonBlur(obj){
	if(decodeURIComponent(obj.alt)!=obj.value){obj.style.border="1px solid red";}else{obj.style.border="0px";}
}

/*保存配置项*/
function HwSaveConf(id){
	var hstr="";
	if(0!=id){hstr=id.toString()+"\x01"+$("#nkey\\["+id.toString()+"\\]").val()+"\x01"+$("#vark\\["+id.toString()+"\\]").val()+"\x01\x01"+$("#sson\\["+id.toString()+"\\]").val();}else{hstr="0\x01"+$("#newnkey").val()+"\x01"+$("#newvark").val()+"\x01"+$("#Fid").val()+"\x01"+$("#newsson").val();}

	$.post(PstFile[0],{"class":"choptions","info":hstr},function(data,status){
		if("success"==status){
			if(""!=data){ var hstr=data.split("\x01");
				switch(hstr[0]){
					case "chaddsuccess": //添加完成
					    //$("#newopts").hide();$("#addoptbut").html("添加项"); //收起
					    var hstr='<tr id="cnfid['+hstr[1]+']"><td  align=center>'+($("#conftabs tr").length-1).toString()+'</td>\
					    <td><input type="text" id="nkey['+hstr[1]+']" value="'+$("#newnkey").val()+'" style="width:80px;"/></td>\
					    <td><input type="text" id="vark['+hstr[1]+']" value="'+$("#newvark").val()+'"  style="width:500px;"/></td>\
					    <td><input type="text" id="sson['+hstr[1]+']" style="font-weight:bold;width:60px;" value="'+$("#newsson").val()+'"/></td>\
					    <td class="actd" style="text-align:center;"><img onclick="HwSaveConf('+hstr[1]+');"  src="img/save-icon.gif" title="保存修改"/>&nbsp;|&nbsp;<img onclick="DelConfOption('+hstr[1]+');" src="img/hr.gif" title="删除该项"></td></tr>';
					    $("#conftabs").append(hstr);
					    $("#newnkey").val(""); $("#newvark").val(""); $("#newnkey").focus(); $(".cnfopt").scrollTop($(".cnfopt").height().toString());
					break;
					case "cherror-addsql":alert("添加操作失败~!");break;
					case "chsuccess":  //修改完成
							var nkey=$('#nkey\\['+hstr[1]+'\\]'),vark=$('#vark\\['+hstr[1]+'\\]');
							nkey.attr("alt",nkey.val());
							vark.attr("alt",vark.val());
							nkey.css("border","0px");
							vark.css("border","0px");
							$.dialog({icon:"success.gif",title:"修改配置项返回",lock: true,time:2,content:'保存操作完成~!',cancelVal:'关闭',cancel:true});
					break;
					case "cherror-id":alert("ID错误~!");break;
					case "cherror-fid":alert("从属节点ID错误~!");break;
					case "cherror-num":alert("数据不完整~!");break;
					default:PublicReturn(data);break;
				}
			}else{alert("数据返回异常: NULL");}
		}else{alert("返回错误~! POST未成功~!");}
	});
}

/*保存配置文件(code)*/
function HwConfSave(id){
	if(!arguments[0]){id=0;}
	var hstr=""; if(0!=id){hstr=id.toString()+"\x01\x01"+CodeEditor.getValue()+"\x01\x01";}else{hstr="0\x01\x01"+CodeEditor.getValue()+"\x01"+$("#Fid").val()+"\x01";}

	$.post(PstFile[0],{"class":"choptions","info":hstr},function(data,status){
		if("success"==status){
			if(""!=data){ var hstr=data.split("\x01");
				switch(hstr[0]){
					case "chaddsuccess":alert("添加完成~!");break;
					case "chsuccess":alert("保存完成~!");break;
					default:PublicReturn(data);break;
				}
			}else{alert("数据返回异常: NULL");}
		}else{alert("返回错误~! POST未成功~!");}
	});
}

/*删除配置项*/
function DelConfOption(id){
	
	if(confirm('你确定要删除['+$("#nkey\\["+id+"\\]").val()+']')){
		$.post(PstFile[0],{"class":'deloption',"info":id},function(data,status){
			if("success"==status){ var hstr=data.split("\x01");
				switch(hstr[0]){
					case "success":$("#cnfid\\["+hstr[1]+"\\]").remove();break;
					case "error-sql":alert("执行SQL时发生错误~!");break;
					case "error-id":alert("ID错误~!");break;
					case "error":alert("发生错误~!");break;
					default:PublicReturn(data);break;
				}
			}else{alert("POST返回错误~!");}
		});
    }
}

/*推送任务*/
function CreateTask(fid,nid){
	var ids=fid+"\x01"+nid;
	$.post(PstFile[0],{"class":'runtask',"info":ids},function(data,status){
			if("success"==status){ var hstr=data.split("\x01");
				switch(hstr[0]){
					case "success":CheckTaskState(hstr[1]);break;
					case "error-id":alert("ID错误~!");break;
					case "error":alert("发生错误~!: "+hstr[2]);break;
					default:PublicReturn(data);break;
				}
			}else{alert("POST返回错误~!");}
		});
}

/*检查任务状态*/
function CheckTaskState(tkey){
	$.post(PstFile[0],{"class":'chtask',"info":tkey},function(data,status){
		if("success"==status){ var hstr=data.split("\x01");
			switch(hstr[0]){
				case "success":alert("推送成功~!");break;
				case "error":alert("推送失败:\n"+hstr[1]);break;
				case "wait":setTimeout("CheckTaskState('"+tkey+"');",1000);break;
				case "keyerr":alert("错误的KEY~!");break;
				case "chkerror":alert("检索错误~!");break;
				default:PublicReturn(data);break;
			}
		}
	});
}
