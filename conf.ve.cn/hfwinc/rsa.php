<?php
/*
 * linux 下生成密钥对
 * 
 * 生成私钥：openssl genrsa -out privatekey.key 1024
 * 对应公钥：openssl rsa -in privatekey.key -pubout -out pubkey.key
 * 
 *  提取16进制串: openssl asn1parse -out temp.ans -i -inform PEM < server.pem
 * 
 * */
$private_key = '-----BEGIN RSA PRIVATE KEY-----
MIICWwIBAAKBgQCleY/tLVS7GJxnB3pzthel7G+yqMOeuR9GtRnWVmIbkVAj3m94
/niSXkW82H2QCm87DI/BXZWbo9NZwwqjQG1Q8nB/AkbQrgfa9CG7zlYm0mWBzm2o
TiTvzJA17fDE03zzknWl010S9T9JuJ672Wogtvy4HuCi6ZUGxfRywah9DQIDAQAB
AoGATadNbo0+XQrAouz0fwat9FSPWnUuT/cqAUGNnXMuWSeJRzvkbhBlPrL04Rlr
W0Q6TKipcaHcSozH6zDHdjMO2RNlHKnmBXy02Z8P49TeAByQndlqlq2P/Hg+zagJ
73MAs5JjedjfZlnIoYWMhmjuLL19JxPb0I/PSLoO4OWT7aECQQDZdFvM1ZrJFGnG
EU5NaY+773ko2cUDxbzc9egFZcNutFI+inYCZcFbS79yHEp7PcMSsvkjuYQVEAiS
4yMMaKYVAkEAws547523BkS9CTGOvAQ/KqHMEMbjJdMbuiUMDSeZFGhJALApEA8O
d86ay/lIZOLCvvvJ1/XhoMV7T/rJkt1xGQJATRFzM56E1D0626rogIEoIuhVnYfI
znR3Yix5BeiyIfsgpu+1sVXU+IFZIZ0rPJCAIZFywRmP2VMsZrq/gjdYnQJAJfbf
ErOFy67xuCz0SCf7t284ubxI4EI4ERrPMnEZICUCtSOfnQWSKD8XY9D9Dswyb83a
FEBS7GYQFhIl3n1RYQJAPEQwwnaEtCNrTZAjqvK1YxC1+5B/O4qtxlZA2QIbDMTU
fTmgDqkjfCw3+zH6EPKWFzvhvJ4PrWDOFva2xRrv5w==
-----END RSA PRIVATE KEY-----';

$public_key = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCleY/tLVS7GJxnB3pzthel7G+y
qMOeuR9GtRnWVmIbkVAj3m94/niSXkW82H2QCm87DI/BXZWbo9NZwwqjQG1Q8nB/
AkbQrgfa9CG7zlYm0mWBzm2oTiTvzJA17fDE03zzknWl010S9T9JuJ672Wogtvy4
HuCi6ZUGxfRywah9DQIDAQAB
-----END PUBLIC KEY-----';

//公钥加密
function Pub_encrypt($str){
	global $public_key; $encrypted='';
    $pu_key=openssl_pkey_get_public($public_key);//判断公钥是否是可用
    if(!$pu_key){echo 'public_key false~!';exit();}
    openssl_public_encrypt($str,$encrypted,$pu_key);//公钥加密
    return $encrypted;
}
//公钥解密
function Pub_decrypt($str,$fromjs=FALSE){
	global $public_key; $decrypted='';
	$pu_key = openssl_pkey_get_public($public_key);//判断公钥是否是可用
	if(!$pu_key){echo 'public_key false~!';exit();}
	$padding = $fromjs ? OPENSSL_NO_PADDING : OPENSSL_PKCS1_PADDING;
	if(openssl_public_decrypt(base64_decode($str),$decrypted,$pu_key,$padding)){
	    return $fromjs ? rtrim(strrev($decrypted),"/0"):"".$decrypted;
    }else{
		return 'decrypt false~!';
    }
}

//私钥加密
function Priv_encrypt($str){
	global $private_key; $encrypted='';
	$pi_key = openssl_pkey_get_private($private_key);//判断私钥是否是可用
	if(!$pi_key){echo 'private_key false~!';exit();}
	openssl_private_encrypt($str,$encrypted,$pi_key);//私钥加密
	return $encrypted;
}
//私钥解密
function Priv_decrypt($str,$fromjs=FALSE){
	global $private_key; $decrypted='';
	$pi_key = openssl_pkey_get_private($private_key);//判断私钥是否是可用
	if(!$pi_key){echo 'private_key false~!';exit();}
	$padding = $fromjs ? OPENSSL_NO_PADDING : OPENSSL_PKCS1_PADDING;
	if(openssl_private_decrypt(base64_decode($str),$decrypted,$pi_key,$padding)){//私钥解密
		return $fromjs ? rtrim(strrev($decrypted),"/0"):"".$decrypted;
    }else{
		return 'decrypt false~!';
	}
}

?>
