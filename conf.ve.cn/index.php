<?php
require('hfwinc/session.php');
require('hfwinc/hwfunction.php');
if(isset($_SESSION['#LOGIN'])&&isset($_SESSION['#UID'])&&isset($_SESSION['#DTE'])&&isset($_SESSION['#TID'])){  //用户检测
	 if($_SESSION['#LOGIN']!=true||$_SESSION['#UID']==''||$_SESSION['#DTE']==''||$_SESSION['#TID']==''){header('Location: login.php');exit(0);}
}else{header('Location: login.php');exit(0);}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head><title>config</title><meta http-equiv="content-type" content="text/html; charset=UTF-8"/><style media="all" type="text/css">@import "css/all.css";</style>
   <link rel="stylesheet" type="text/css" href="css/type.css"/>
   <script type="text/javascript" src="hfw_script/jquery-1.8.3.min.js"></script>
   <script type="text/javascript" src="hfw_script/lhgdialog.min.js"></script>
   <script type="text/javascript" src="hfw_script/hwfunction.js"></script>
   <script type="text/javascript" src="hfw_script/hfwrsa.js"></script>
   
<link rel="stylesheet" href="hfw_script/CodeMirror/lib/codemirror.css">
<script src="hfw_script/CodeMirror/lib/codemirror.js"></script>
<script src="hfw_script/CodeMirror/addon/edit/matchbrackets.js"></script>
<!--<script src="hfw_script/CodeMirror/mode/clike/clike.js"></script>-->
<!--<script src="hfw_script/CodeMirror/mode/xml/xml.js"></script>-->
<script src="hfw_script/CodeMirror/mode/nginx/nginx.js"></script>
<!--<script src="hfw_script/CodeMirror/mode/php/php.js"></script>-->

   <?php
	   if(Gants(0)){echo '<script type="text/javascript" src="hfw_script/admfunc.js"></script>';}
	?>
   <script type="text/javascript" src="hfw_script/hfwindex.js"></script>
</head>
<body>
<div id="main">
	<div id="header">
		<p>当前用户:  <?php echo $_SESSION['#NME'];?></p>
         <ul id="top-navigation">
			<li class="active" onclick="hfwin(this,0);"><span><span>主页</span></span></li>
			<?php
			    if(Gants(0)||Gants(4)){echo '<li onclick="hfwin(this,2);"><span><span>节点列表</span></span></li>';}
			    if(Gants(0)||Gants(3)){echo '<li onclick="hfwin(this,3);"><span><span>配置列表</span></span></li>';}
			    if(Gants(0)||Gants(2)){echo '<li onclick="hfwin(this,4);"><span><span>日志列表</span></span></li>';}
			    if(Gants(0)){echo '<li onclick="hfwin(this,1);"><span><span>用户管理</span></span></li>';}
			?>
			<li onclick="LoginOut();"><span><span>退&nbsp;出</span></span></li>
		</ul>
	</div>
	
<div id="middle"></div>
<div id="footer"></div>
<script language="javascript">
   var FnLst=$.dialog({
     id: 'filelist',
     max: false,
//     cancel: false,
     title:'文件列表',
//     left:'1%',
//     top:'30%',
     close: function(){FnLst.hide();return false;},
     content:'Test'
   });

 FnLst.hide(); 
   //FnLst.content('tttt');
</script>
</body>
</html>
