#define KEYNUM  18 /*密钥长度*/
#define CMDNUM 10 /*指令数量*/
#define STRNUM 230 /*指令或说明长度*/

#define STRBUF 1030 /*临时字符串空间*/
#define HTTPHEAD   10240   /*10K头部大小*/
#define HTTPBUF       1024  /*1K数据缓存区*/
#define FILEPATHBUF  256   /*文件名缓存大小*/
#define KEYBUF 64  /*key 大小*/
#define DOMAINBUF  256  /*域大小*/
#define DATEBUF 64  /*时间截空间*/

#define IPLEN    18 /*IP地址长度*/
#define KEYLEN   64 /*节点密钥长度*/

#define BUF_128 128 /*消息长度*/
/*
 * 数据结构申明
 * */
struct dbinf{
	unsigned char type; /*数据库类型，0 mysql  1 sqlite*/
	unsigned char *name;  /*数据库名*/
	unsigned char *host;    /*数据库地址*/
	unsigned short port;    /*端口号*/
	unsigned char *path;    /*sqlite数据库地址*/
	unsigned char *user;     /*登录用户名*/
	unsigned char *pwd;     /*登录密码*/
};/* 数据库信息*/
struct SERINF{
	unsigned char host[18]; /*服务端IP地址*/
	//unsigned short port; /*端口号*/
	//unsigned char dmn[DOMAINBUF]; /*域名*/
	unsigned char tkey[KEYBUF];/*任务编号*/
	unsigned char dte[DATEBUF]; /*时间截*/
};/*服务端信息*/
struct HeadInfo{
	unsigned char server[64];  /*服务名称*/
	int ver;       /*版本号*/
	unsigned int ste; /*HTTP状态码*/
	unsigned int len; /*body数据*/
	unsigned char md5[64]; /*md5值*/
	unsigned int FileID; /*文件ID*/
	unsigned char file[FILEPATHBUF]; /*文件名*/
	unsigned int TaskID; /*任务ID*/
    unsigned char error[BUF_128]; /*错误信息*/
};/*HTTP头信息*/
struct ipadr{
	unsigned char cmd[3];/*动作指令*/
	unsigned char keys[KEYNUM];/*操作密钥*/
	unsigned char odat[BUF_128];/*其他*/
};  /*协议*/
struct Infos{
	int sock;  /*socketID*/
	unsigned char host[32]; /*IP地址*/
};  /*链接信息*/
struct LocalInfo{
	unsigned short Hport;    /*本地监听端口号*/
	unsigned char SerHost[IPLEN];/*远端服务地址*/
	unsigned char RePort;  /*远端端口*/
	unsigned char dmn[DOMAINBUF]; /*服务端主机名(域名)*/
	unsigned char HwHash[KEYLEN];  /*节点哈稀*/
};  /*本地服务配置信息*/

/*
 * 过程申明
 * */
unsigned int BaseEncode(unsigned char *hstr,unsigned int n,unsigned char *bstr,unsigned char *Bs64);/*Base64 编码*/
unsigned int BaseDecode(unsigned char *hstr,unsigned char *bstr,unsigned char *Bs64);/*base64 解码*/
void encrypt (uint32_t v[], uint32_t k[]) ;/*Tea 解码过程*/
void decrypt (uint32_t v[], uint32_t k[]);/*Tea 编码过程*/
int hwcrypt(unsigned char act,unsigned char *key,unsigned char *dat,unsigned int c);/*Tea 编码+解码*/
int split(char **arr,char *str,const char *del); /*字符串分离*/
void hms(unsigned int ti); /*延时*/
int hwtime(unsigned char *t); /*获取时间截*/
unsigned int hrand(unsigned char *enc,unsigned char m); /*产生随机字符串*/
int GetFileLen(unsigned char *FilePath);/*返回文件大小*/

void StateMsg(unsigned char *key,unsigned char *dat);/*错误消息反馈*/
int wlogs(unsigned char *logs); /*写日志*/
void thread(void *ipf); /*线程*/
void hcon(void *pov);/*链接发送线程*/
int HttpGet(unsigned char *PostDat);/*链接服务端并发送http请求，返回socket*/

//int HwSet(int agc,char *args[]); /*截取取变量*/
int CmdRun(int Ret,unsigned char *host,unsigned char *ClientDat); /*执行*/
int HwSend(char *dat,int sck); /*发送过程*/
int HwListen();/*创建服务过程*/
int ReadInit();/*初始化配置*/
int ExecSql(unsigned char *hsql);/*执行一条SQL*/

/*
 * 公共变量
 * */
unsigned char HwdbFile[]="init.db3";
unsigned char BaseStr[]="PD5nJutGSCKlfkN2bZ9BvFrHhAUj0Y7a[=L3OeWM4wd]i1RVQyXqoszcx8T6gIEmp";
unsigned char HwKey[17]={0x0c,0x02,0x0a,0x0f,0x01,0x07,0x06,0x10,0x0d,0x0b,0x0e,0x04,0x08,0x09,0x05,0x03,0x00}; /*Tea Key*/
unsigned char Debug[3]={0}; /*运行状态*/
struct LocalInfo Inits; /*本地配置信息*/
pthread_t Thid;
int Trel=0;
 
