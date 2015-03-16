/*链接服务端并发送http请求，返回socket*/
int HttpGet(unsigned char *PostDat){
	 int sock; struct sockaddr_in hsck; unsigned char Header[HTTPHEAD]={0};
	 if((sock=socket(AF_INET,SOCK_STREAM,0))==-1){wlogs("Error from HttpGet() function in socket()\n"); return -1;}
    memset(&hsck,0,sizeof(hsck)); hsck.sin_family=AF_INET;  hsck.sin_port=htons(Inits.RePort); hsck.sin_addr.s_addr=inet_addr(Inits.SerHost); /*需要严控地址安全的最后屏障也是唯一屏障*/
     if(0==connect(sock,(const struct sockaddr *)&hsck,sizeof(hsck))){  /*链接成功*/
		 sprintf(Header,"GET /set.php HTTP/1.0\nHost: %s\nVeVer: v1.0\nVehash: %s\n%s\n\n",Inits.dmn,Inits.HwHash,PostDat); //http头部
		 if(-1==HwSend(Header,sock)){wlogs("Error from HttpGet() function , When I send data ~!\n");close(sock); sock=-1;}
	 }else{sock=-1;printf("\033[1;31mConnect Error~!\033[0m\n");memset(Header,0,HTTPHEAD); sprintf(Header,"[%s:%d] Connect Error~!\n",Inits.SerHost,Inits.RePort); wlogs(Header);}
    return sock;
}

/*链接发送线程*/
void hcon(void *pov){
  struct HeadInfo Hinfo; /*协议信息*/
  struct SERINF SRVINF; /*服务端信息*/
  int sock,ret=0,buflen=0,btn=0,n=0,e=0,i=0;  FILE *wf;
  unsigned char Header[HTTPHEAD]={0}, buf[HTTPBUF]={0};
  unsigned char byte,*Bty;
  char *Arrs[30];
  
  memcpy(&SRVINF,pov,sizeof(struct SERINF));
  memset(Header,0,HTTPHEAD); sprintf(Header,"get\x01"); HwCrypt(0,Header);
  memset(buf,0,HTTPBUF); sprintf(buf,"VeKey: %s\nVeData: %s",SRVINF.tkey,Header);
  
  sock=HttpGet(buf);
  if(-1!=sock){ /*链接成功*/
	  memset(Header,0,HTTPHEAD);
	  while(0==btn){ /*截取HTTP头部*/
	       ret=recv(sock,&byte,1,0);
	       if(-1==ret){wlogs("hcon() Error to get http-header\n"); btn=-1;}else{
			   if(buflen<HTTPHEAD){
				   if(buflen>10&&0x0a==byte){if(0x0d==Header[buflen-3]&&0x0a==Header[buflen-2]&&0x0d==Header[buflen-1]&&0x0a==byte){btn=1;}} /*确定头部*/
				   Header[buflen]=byte; buflen++;
				 }else{btn=-1;}/*头部长度超出预设值*/
			}
	   }

		if(1==btn){
//				   printf("============================================\n%s\n=======================================\n",Header); //fflush(stdout);
				   /*配置文件信息*/
				   memset(buf,0,HTTPBUF);
				   Bty=strstr(Header,"VeFile: "); if(NULL!=Bty){ ret=strlen(Bty); for(btn=0;btn<ret;btn++){if(0x0d==Bty[btn]){Bty[btn]=0x00;break;}} ret=strlen(Bty+8); if(ret>HTTPBUF){ret=HTTPBUF-1;} memcpy(buf,Bty+8,ret);}
				   if(strlen(buf)>1){btn=HwCrypt(1,buf); btn=(int)split(Arrs,buf,"\x01");
					      if(btn>4){
                                      Hinfo.len=(unsigned int)atoi(Arrs[0]); /*文件长度*/
                                      btn=strlen(Arrs[1]); if(btn>32){btn=32;} memcpy(Hinfo.md5,Arrs[1],btn); /*文件MD5值*/
                                      Hinfo.FileID=(unsigned int)atoi(Arrs[2]); /*文件ID*/
                                      btn=strlen(Arrs[3]); if(btn>=FILEPATHBUF){btn=FILEPATHBUF-1;} memcpy(Hinfo.file,Arrs[3],btn); /*文件路径*/
                                      Hinfo.TaskID=(unsigned int)atoi(Arrs[4]); /*任务ID*/
						     }else{printf("Http header Error: 'VeFile'\n");wlogs("Http header Error: 'VeFile'\n");}
                    }
                                   
				   /*其他信息*/
				   memset(buf,0,HTTPBUF);
				   Bty=strstr(Header,"veProc: "); if(NULL!=Bty){ ret=strlen(Bty); for(btn=0;btn<ret;btn++){if(0x0d==Bty[btn]){Bty[btn]=0x00;break;}} ret=strlen(Bty+8); if(ret>HTTPBUF){ret=HTTPBUF-1;} memset(buf,0,HTTPBUF); memcpy(buf,Bty+8,ret);}
				   if(strlen(buf)>1){ btn=HwCrypt(1,buf); btn=(int)split(Arrs,buf,"\x01");
					  if(btn>1){
						  Hinfo.ver=(int)atoi(Arrs[0]); /*版本号*/
						  btn=strlen(Arrs[1]); if(btn>=BUF_128){btn=BUF_128-1;} memcpy(Hinfo.error,Arrs[1],btn); /*Web端错误信息*/
				      }else{printf("Http header Error: 'veProc'\n");wlogs("Http header Error: 'veProc'\n");}
				   }

				   Bty=strstr(Header,"Server: "); if(NULL!=Bty){ ret=strlen(Bty); for(btn=0;btn<ret;btn++){if(0x0d==Bty[btn]){Bty[btn]=0x00;break;}} ret=strlen(Bty+7); if(ret>sizeof(Hinfo.server)){ret=sizeof(Hinfo.server)-1;} memcpy(Hinfo.server,Bty+7,ret);} /*截取服务器信息*/
				   Bty=strstr(Header,"HTTP/1.1 "); if(NULL!=Bty){ ret=strlen(Bty); for(btn=0;btn<ret;btn++){if(0x0d==Bty[btn]){Bty[btn]=0x00;break;}} Hinfo.ste=(int)atoi(Bty+8);}

//                                  printf("\nveState: %s\nVeFile: %s\nveLen: %d\nveVer: %d\nServer: %s\n",Hinfo.error,Hinfo.file,Hinfo.len,Hinfo.ver,Hinfo.server);

			if(200==Hinfo.ste){
			   if(strlen(Hinfo.error)<3){ printf("%s-[%s]文件: %s",SRVINF.dte,SRVINF.host,Hinfo.file);
				   if(access(Hinfo.file,0)){ //检测文件
					   printf("   ---> DOESN'T EXISIT~!\n");  memset(buf,0,HTTPBUF); sprintf(buf,"state\x01serror\x02%d\x02%s\x02%s",Hinfo.TaskID,SRVINF.tkey,"File not found ~!"); StateMsg(SRVINF.tkey,buf);
					    goto VeError;
					 }
					 
				   if(strlen(Hinfo.md5)>30&&GetFileLen(Hinfo.file)>1){  /*文件校验错误*/
					   memset(buf,0,HTTPBUF); sprintf(buf,"%s",FileSum(Hinfo.file));
					   if(0!=memcmp(Hinfo.md5,buf,32)){
						   printf(" ---> md5sum error ~!\n"); memset(buf,0,HTTPBUF); sprintf(buf,"state\x01serror\x02%d\x02%s\x02%s",Hinfo.TaskID,SRVINF.tkey,"Md5sum error ~!"); StateMsg(SRVINF.tkey,buf);
						   goto VeError;
						}
					 }
				   //printf("\nHinfo.md5: %s , FileSum: %s\n",Hinfo.md5,buf); /*MD5校验*/
				   wf=fopen(Hinfo.file,"wb"); //写入文件
				   if(NULL!=wf){
					   buflen=0;btn=0;n=0;
					   while(0==btn){ /*接收HTTP body 内容*/
						  memset(buf,0,HTTPBUF); ret=recv(sock,buf,HTTPBUF,0);
						   if(-1==ret){printf("\nhcon() Error to get http-body~!\n");btn=1;}
						   else if(0==ret){btn=1;}else{ if(1!=fwrite(buf,ret,1,wf)){e++;} }
					   }
					   fclose(wf);  memset(buf,0,HTTPBUF);
					   if(e>0){printf("   ---> Write error : %d\n",e);sprintf(buf,"[%s] Write error : %d\n",Hinfo.file,e);wlogs(buf);}else{printf("   ---> OK\n");sprintf(buf,"state\x01success\x02%d\x02%s\x02OK",Hinfo.TaskID,SRVINF.tkey);StateMsg(SRVINF.tkey,buf);}
					   
					   memset(buf,0,HTTPBUF); sprintf(buf,"filesum\x01%d\x02%s",Hinfo.FileID,FileSum(Hinfo.file)); StateMsg(SRVINF.tkey,buf);
					   
				   }else{printf("  ---> Error can not be open file\n");memset(buf,0,HTTPBUF);sprintf(buf,"[%s] Error can not be open file\n",Hinfo.file);wlogs(buf);}
			   }else{n=HwCrypt(1,Hinfo.error); memset(buf,0,HTTPBUF); sprintf(buf,"Return Error: %s\n",Hinfo.error); printf("%s",buf); wlogs(buf);}
			 }else{memset(buf,0,HTTPBUF); sprintf(buf,"Error HTTP state: %d\n",Hinfo.ste); printf("%s",buf); wlogs(buf);}
		}
VeError:
	   	fflush(stdout); close(sock); hms(300);
  }
}

/*错误消息反馈*/
void StateMsg(unsigned char *key,unsigned char *dat){
	int sock;
	unsigned char hdat[HTTPBUF]={0},buf[HTTPBUF*2]={0};
	sprintf(hdat,"%s",dat); HwCrypt(0,hdat); sprintf(buf,"VeKey: %s\nVeData: %s",key,hdat); sock=HttpGet(buf);
	if(-1==sock){printf("Send Error~!\n");}else{close(sock);}
}

/*服务开启过程*/
int HwListen(){
  pthread_t tid;
  int sock,ret,alen=0,rel; struct sockaddr_in hsck,client_addr;
  socklen_t addr_len=sizeof(client_addr);
  struct Infos ConInf;

  if((sock=socket(AF_INET,SOCK_STREAM,0))==-1){printf("Error in socket()\n");return;}  /*socket建立*/
  memset(&hsck,0,sizeof(hsck)); hsck.sin_family=AF_INET; hsck.sin_port=htons(Inits.Hport); hsck.sin_addr.s_addr=INADDR_ANY; /*任何一个地址*/
  if(bind(sock,(struct sockaddr *)&hsck,sizeof(hsck))==-1){printf("\033[1;31mbind socket error~!\033[0m\n");return 0;}  /*绑定socket失败*/
  alen=sizeof(sock);

   while(1){
        if(listen(sock,0)==-1){printf("listen() error~!\n");return 0;} /*监听端口失败*/
        ret=accept(sock,(struct sockaddr * )&client_addr,&addr_len);               /*等待连接*/
        memset(&ConInf,0,sizeof(ConInf)); ConInf.sock=ret;  /*socket标识*/
        sprintf(ConInf.host,"%s",inet_ntoa(client_addr.sin_addr)); /*IP地址*/
        if(Debug[1]>0){printf("hcon:[%s]",ConInf.host); } /*输出调试信息*/
        rel=pthread_create(&tid,NULL,(void *)thread,&ConInf);  /*建立应答线程*/
   }
  return 0;
}


/*发送线程*/
void thread(void *ipf){
     int Ret=0,n=0; unsigned int Cmd=0; unsigned char hbuf[1024]={0},keys[KEYNUM+1]={0};  /*缓冲区*/
     struct ipadr Hret;
     struct Infos Hinf;
     memcpy(&Hinf,ipf,sizeof(Hinf));

      Ret=1;
          while(Ret){
               memset(hbuf,0,1024); Ret=recv(Hinf.sock,hbuf,1024,0);  /*清空缓存，接收数据*/
               if(-1==Ret){if(Debug[0]>0){printf("Ret Error return\n");} /*错误退出*/ break;}else{ /*收到信息*/
		  if(Ret>0){ n=HwCrypt(1,hbuf);  CmdRun(Hinf.sock,Hinf.host,hbuf); }
               }
          }
       hms(300); /*等待 300毫秒*/
       close(Hinf.sock); /*关闭socket*/
       if(Debug[1]>0){printf("Thread close~!\n");} /*退出线程*/
}

/*发送过程*/
int HwSend(char *dat,int sck){int ret=0; ret=send(sck,dat,strlen(dat),0);if(-1==ret){printf("\033[1;31m发送错误\033[0m\n");} return ret;}

/*执行*/
int CmdRun(int Ret,unsigned char *host,unsigned char *ClientDat){
   int i=0,n=0,cmd=0,rel=0;
   struct SERINF serv; /*服务端信息*/
   char *arr[30];
   n=split(arr,ClientDat,"\x01");
  
   cmd=atoi(arr[0]);
  switch(cmd){
     case 100: /*建立应答线程,tkey*/
	 memset(&serv,0,sizeof(struct SERINF));
         sprintf(serv.host,"%s",host);
         n=strlen(arr[1]); if(n>KEYBUF){n=KEYBUF-1;} memcpy(serv.tkey,arr[1],n);
         hwtime(serv.dte); /*时间截*/
         Trel=pthread_create(&Thid,NULL,(void *)hcon,&serv);  /* 创建GET线程*/
      break;
     default:printf("Error cmd: %d\n",cmd);break;
   }
   return 0;
}

/*返回文件大小*/
int GetFileLen(unsigned char *FilePath){FILE* fp=NULL; int nFileLen=0; fp=fopen(FilePath,"rb"); if(fp==NULL){printf("can't open file");return -1;} fseek(fp,0,SEEK_END); /*定位到文件末 */  nFileLen=ftell(fp); /*文件长度*/ fclose(fp); return nFileLen;}
/*写日志*/
int wlogs(unsigned char *logs){FILE *stream; unsigned char buf[30]={0};if((stream=fopen("./hwlogs.log","ab+"))>0){hwtime(buf); fwrite(buf,strlen(buf),1,stream); /*时间截*/ fwrite(logs,strlen(logs),1,stream);/*日志内容*/fclose(stream);}return 0;}
 /*Sleep 1毫秒*/
void hms(unsigned int ti){unsigned int i;i=ti;while(i--)usleep(1000);}
/*获取时间截*/
int hwtime(unsigned char *t){time_t rawtime; struct tm * timeinfo; time(&rawtime); timeinfo = localtime(&rawtime);sprintf (t,"[%4d-%02d-%02d %02d:%02d:%02d]",1900+timeinfo->tm_year,1+timeinfo->tm_mon,timeinfo->tm_mday,timeinfo->tm_hour,timeinfo->tm_min,timeinfo->tm_sec);}
/*字符串分离*/
int split(char **arr,char *str,const char *del){int c=0;char *s=strtok(str,del);while(s!=NULL){*arr++=s;s=strtok(NULL,del);c++;}return c;}

/*读sqlite3*/
int ReadInit(){
     char *errmsg=0,**dbresult;
     int ret=0,i,n=0,m=0,nrow,ncol,index;
     sqlite3 *db=0;
     unsigned char hsql[300]={0},buf[STRBUF]={0};

     sprintf(hsql,"select id,nkey,var,str from initab;");
     if(SQLITE_OK!=(ret=sqlite3_open(HwdbFile,&db))){sprintf(buf,"Cannot open db: %s",sqlite3_errmsg(db)); if(Debug[0]>0){printf("%s\n",buf);} wlogs(buf); return -1;}
     if(SQLITE_OK==sqlite3_get_table(db,hsql,&dbresult,&nrow,&ncol,&errmsg)){index=ncol;
         if(index>0){
	       memset(&Inits,0,sizeof(struct LocalInfo)); /*初始化*/
               for(i=0;i<nrow;i++){
		 n=strlen(dbresult[index+1]); m=strlen(dbresult[index+2]);
		 if(7==n){
		   if(0==memcmp(dbresult[index+1],"cf_port",n)){Inits.Hport=(unsigned short)atoi(dbresult[index+2]);}
		   else if(0==memcmp(dbresult[index+1],"cf_hash",n)){if(m>KEYLEN){m=KEYLEN-1;} memcpy(Inits.HwHash,dbresult[index+2],m);}
		   else if(0==memcmp(dbresult[index+1],"cf_host",n)){if(m>IPLEN){m=IPLEN-1;} memcpy(Inits.SerHost,dbresult[index+2],m);}
		 }
		index+=ncol;
               }
//printf("Hport= %d\nSerHost = %s\nHwHash = %s\n",Hport,SerHost,HwHash);
         }else{sprintf(buf,"Error: sql return empty ~!"); if(Debug[0]>0){printf("%s\n",buf);} wlogs(buf); ret=-3;}
     }else{sprintf(buf,"Error to exec Sqlcmd: %s\n",errmsg); if(Debug[0]>0){printf("%s\n",buf);}  wlogs(buf); ret=-2;}
     sqlite3_free_table(dbresult); sqlite3_free(errmsg); sqlite3_close(db);
  return ret;
}

int ExecSql(unsigned char *hsql){
  char *errmsg=0,**dbresult;
  int ret=0,nrow,ncol,index;
  sqlite3 *db=0;

  if(SQLITE_OK!=(ret=sqlite3_open(HwdbFile,&db))){fprintf(stderr,"Cannot open db: %s\n",sqlite3_errmsg(db)); return -1;}
  if(SQLITE_OK==sqlite3_get_table(db,hsql,&dbresult,&nrow,&ncol,&errmsg)){ret=0;}else{fprintf(stderr,"Error to exec Sqlcmd: %s\n",errmsg);ret=-2;}
  sqlite3_free_table(dbresult); sqlite3_free(errmsg); sqlite3_close(db);
  return ret;
}

