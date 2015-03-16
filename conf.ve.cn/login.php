<?php
require('hfwinc/session.php');
require('hfwinc/hwfunction.php');
if(isset($_SESSION['#LOGIN'])&&isset($_SESSION['#UID'])&&isset($_SESSION['#DTE'])&&isset($_SESSION['#TID'])){  //用户检测
	 if(true==$_SESSION['#LOGIN']&&$_SESSION['#UID']!=''&&$_SESSION['#DTE']!=''&&$_SESSION['#TID']!=''){header('Location: index.php');exit(0);}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>用户登录</title>
<link href="css/hfwlogin.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="hfw_script/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="hfw_script/hfwrsa.js"></script>
<script type="text/javascript" src="hfw_script/hwfunction.js"></script>
<script type="text/javascript" src="hfw_script/login.js"></script>
</head>
<body>
    <div id="login"><div id="top"></div>
		 <div id="center"><div id="htit"></div>
			  <div id="center_middle">
			       <div id="user"><span style="font-weight:bold;">用 户 </span><input type="text" class="hptxt" name="lus" id="lus" onkeydown="if(event.keyCode==13){$('lps').focus();}" /></div>
				   <div id="password"><span style="font-weight:bold;">密   码 </span><input type="password" class="hptxt" name="lps" id="lps" onkeydown="if(event.keyCode==13){logi();}" /></div>
				   <div id="butrw">
				     <input type=button onclick="logi();" id="lgn" value="登 录" /><input type=button onclick="hret();" id="rest" value="重 置"/>
				   </div>
			  </div>
		 </div><div id="down"></div>
	</div>
</body>
</html>
