var PstFile=new Array("modle.php","checkuser.php");
var CodeEditor=null;

/*全局处理*/
function PublicReturn(dat){
	var cum=dat.split("\x01");
	switch(cum[0]){
		case "error-sql":alert("执行SQL返回错误!");break; /*执行SQL返回错误!*/
		case "error-nologin":alert("未登录~!");window.location.href(cum[1]);break; /*未登录*/
		case "error-timeout":alert("登录超时~!");window.location.href(cum[1]);break; /*登录超时*/
		case "error-relogin":alert("用户信息发生改变~!");window.location.href(cum[1]);break; /*用户信息发生改变*/
		case "error-login":alert("帐号异常~!");window.location.href(cum[1]);break; /*帐号异常*/
		case "error-gants":alert(" 操作终止，用户权限不足~!");break;
		case "error":Errordlg(cum[1]);break;
		default:alert("错误~!未知返回~!: "+dat);break;
	}
}

/*错误处理过程*/
function Errordlg(str){
   var spin=str.split("\x02");
   switch(spin[0]){
   case "sql":alert("SQL执行错误~!");break;
   case "cmd":alert("通信协议错误~! 严重的～！灰常灰常严重～！");break;
   case "flst-id":alert("ID错误~!");break;
   default:alert("未知错误:\n"+str);break;
   }
}

/*退出登录*/
function LoginOut(){
	$.dialog({icon:"prompt.gif",title:"退出登录",lock: true,content:'你确定要退出吗~!',ok: function(){
			$.post(PstFile[0],{"class":"loginout","info":null},function(data,status){
				if("success"==status){
					if(""!=data){ var Rets=data.split("\x01");
						switch(Rets[0]){
							case "success":location.href=Rets[1];break;
							default:alert("未知错误~!");break;
						}
					}else{alert("数据返回异常: NULL");}
				}else{alert("返回错误~! POST未成功~!");}
			});
    return false;
    },cancelVal:'关闭',cancel:true});
}

/*鼠标所在行*/
function SelColumn(obj){
	if('reach'!=$(obj).attr("id")){
		$(obj.parentNode).children("tr").css("background","#D8D8D8");
	    obj.style.background="cornsilk";
     }else{
		$(obj.parentNode).find(".listing").find("tr").css("background","#D8D8D8");
	 }
}
