function logi(){
	if($("#lus").val().length<2){alert("请输入正确的用户名!");return false;}
	if($("#lps").val().length<6){alert("请输入正确的密码!");return false;}
	$.post(PstFile[1],{"class":"login","info":HwCode($("#lus").val()+"\x01"+$("#lps").val())},function(data,status){
		if("success"==status){
				if(""!=data){ var hstr=data.split("\x01");
					switch(hstr[0]){
						case "logined":window.location.href=hstr[1];break; //已经登录
						case "success":window.location.href=hstr[1];break; //登录成功
						case "error-login":$("#lps").val("");alert("用户名或密码错误~!");break;
						case "error-num":alert("协议格式不对~!");break;
						case "error-uid":alert("用户名为空或小于2个字节~!");break;
						case "error-pwd":alert("密码为空或小于6~!");break;
						case "error-act":$("#lus").val("");$("#lps").val("");alert("用户未启用～！");break;
						case "error-sql":alert("查询用户返回错误～！: "+hstr[2]);break; 
						default:alert("未知错误~!: "+data);break;
					}
				}else{alert("数据返回异常: NULL");}
			}else{alert("返回错误~! POST未成功~!");}
	});
}

function hret(){$("#lus").val("");$("#lps").val("");}
