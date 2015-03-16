/*
 * H*F*W release服务端
 * 编译： gcc confsvr.c -lpthread -o confsvr -lmysqlclient
 * 
 * */
#include <stdio.h>
#include <stdlib.h>
#include <memory.h>
#include <string.h>
#include <stdint.h>
#include <pthread.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <errno.h>
#include <time.h>
#include "hwmd5.h"
#include "sqlite3.h"
#include "conf_header.h"
#include "conf_code.h"
#include "conf_funs.h"

/*指令设置*/
//int HwSet(int agc,char *args[]){
//	unsigned int i=0,n=0,den=0; 
//	unsigned char help[]="\n\033[1;31mUage:\033[0m \033[1;32mdebug\033[0m/\033[1;32mhelp\033[0m/[\033[1;32m-p\033[0m \033[1;33m3-65500\033[0m]\n      \033[1;32mhelp\033[0m\t\t帮助\n      \033[1;32mdebug\033[0m\t\t输出调试信息\n      \033[1;32m-p\033[0m \033[1;33mPort_Number\033[0m\t指定端口号\n\n"; /*使用帮助信息*/
//	char *arr[agc+1];
//	
//	if(agc>1){
		//memset(&Hdb,0,sizeof( struct dbinf));
//		for(n=0;n<agc;n++){
//			if(2==strlen(args[n])){
//					if(0==memcmp(args[n],"-p",2)){ /*端口号*/
//						  if(agc<n+1){printf("%s",help);return -1;}else{n++;Hport=atoi(args[n]);}
//					}
//				   else if(0==memcmp(args[n],"-h",2)){ /*数据库地址*/
//						  if(agc<n+1&&NULL==Hdb.host){printf("%s",help);return -1;}else{
//								 n++; Hdb.host=(char*)malloc(strlen(args[n])*sizeof(char)+1); sprintf(Hdb.host,"%s",args[n]);
//						  }
//					}
//					else if(0==memcmp(args[n],"-P",2)){/*数据库端口号*/
//						  if(agc<n+1&&0==Hdb.port){printf("%s",help);return -1;}else{
//							  n++;Hdb.port=atoi(args[n]);
//						  }
//					}
//					else if(0==memcmp(args[n],"-u",2)){/*登录用户*/
//						  if(agc<n+1&&NULL==Hdb.user){printf("%s",help);return -1;}else{
//							  n++;  Hdb.user=(char*)malloc(strlen(args[n])*sizeof(char)+1); sprintf(Hdb.user,"%s",args[n]);
//						  }
//					}
//					else if(0==memcmp(args[n],"-w",2)){/*密码*/
//						  if(agc<n+1&&NULL==Hdb.pwd){printf("%s",help);return -1;}else{
//							  n++; Hdb.pwd=(char*)malloc(strlen(args[n])*sizeof(char)+1);  sprintf(Hdb.pwd,"%s",args[n]);
//						  }
//					}
//					else if(0==memcmp(args[n],"-t",2)){/*数据库类型*/
//						  if(agc<n+1){printf("%s",help);return -1;}else{ n++; Hdb.type=atoi(args[n]); }
//					}
//					else if(0==memcmp(args[n],"-n",2)){/*数据库名称*/
//						  if(agc<n+1){printf("%s",help);return -1;}else{
//							  n++; Hdb.name=(char*)malloc(strlen(args[n])*sizeof(char)+1);  sprintf(Hdb.name,"%s",args[n]);
//						  }
//					}
//			}else{
//				if(0==memcmp(args[n],"--help",6)){printf("%s",help);return -1;} /*帮助*/
//				else if(0==memcmp(args[n],"-debug",6)){if(agc>n+1){ n++; den=atoi(args[n]);  if(den<1){Debug[0]=1;}else{ if(den>3){den=3;}  for(i=0;i<den;i++){Debug[i]=1;} }}else{Debug[0]=1;}} /*调试开关*/
//				else if(0==memcmp(args[n],"-path",5)){ /*sqlite数据库路径*/
//					if(agc<n+1&&NULL==Hdb.user){printf("%s",help);return -1;}else{
//						  n++; Hdb.path=(char*)malloc(strlen(args[n])*sizeof(char)+1); sprintf(Hdb.path,"%s",args[n]);
//				    }
//			    }
//		    }
//	    }
//		if(Hport<3||Hport>65500){Hport=32906;} //服务端口号检测
//		if(Hdb.port<1||Hdb.port>65500){Hdb.port=3306;} //数据库端口检测
//		if(0==Hdb.type){
//			if(NULL==Hdb.host||NULL==Hdb.user||NULL==Hdb.pwd||NULL==Hdb.name){printf("\033[1;31m数据库信息不完整\033[0m\n");}
//		}
//	}else{printf("%s",help);return -1;}
//	
//	return 0;
//}


/*入口*/
int main(int argc,char *argv[]){
	//int ret=0,n=0;
	char *hbuf;
	hbuf=(char*)malloc(1024);
		
		free(hbuf); /*释放内存*/
		ReadInit();  /*读配置*/
		Inits.RePort=80; /*默认80端口*/
		sprintf(Inits.dmn,"conf.ve.cn");
//printf("%d\n%s\n%s\n",Inits.Hport,Inits.SerHost,Inits.HwHash);
		HwListen();  /*进入服务建立过程*/

  return 0;
}


