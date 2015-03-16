#!/usr/bin/env php
<?php
  $link=mysql_connect('localhost','veconfuser','bxf1m8gHUv9Di');
  if($link==FALSE){echo '数据库连接失败~!';}
  $db=mysql_select_db('veconfer');
  mysql_set_charset('utf8');   ///设置字符集


   
  $filename='.htaccess';
  $handle=fopen($filename,'r'); //打开文件
  
  while(!feof($handle)){
    $line=fgets($handle);
    
    $hsql='insert into conf_tab(fid,var,act)values(1,0x'.strToHex($line).',0)';
    
    HwInsert($hsql);
  }
  fclose($handle);

  function HwInsert($hsql){
    global $link;
    if(strlen($hsql)>10){
      $result=mysql_query($hsql,$link);
      if(!$result){echo "error-sql\n";}else{echo "\033[1;33m->OK!\033[0m\n";}
    }
  }

function strToHex($string){$hex='';for($i=0;$i<strlen($string);$i++){$htr=dechex(ord($string[$i]));if(strlen($htr)<2){$htr='0'.$htr;}$hex.=$htr;}$hex=strtoupper($hex);return $hex;}
function hexToStr($hex){$string='';for($i=0;$i<strlen($hex)-1;$i+=2)$string.=chr(hexdec($hex[$i].$hex[$i+1]));return $string;}

?>
