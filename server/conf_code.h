/*产生随机字符串*/
unsigned int hrand(unsigned char *enc,unsigned char m){
	unsigned char estr[]="gpwn5BtuiPGAT]=68ZFScKbNvQyO9I0XDE1hzkYRm2UJj[rqlL3Wos7aexfHV4CdM"; unsigned int i=0,k=0,c=0,t=0,j=0,n=m; srand((unsigned int)time(0)); /*随机因子*/
    while(i<n){k=rand()%64; for(c=0;c<i;c++){if(enc[c+t]==estr[k]){break;}} if(c==i){enc[i]=estr[k];i++;j++;if(j>=32){t+=32;j=0;}}} return n;
}
/*Base64 编码: hstr=>要编码的数据，n=>数据长度，bstr=>编码后的数据存储区，Ｂs64=>base64编码串*/
unsigned int BaseEncode(unsigned char *hstr,unsigned int n,unsigned char *bstr,unsigned char *Bs64){
     unsigned int i,m,c=0,w=0; unsigned char bas; m=(n/3);
     for(i=0;i<m;i++){bas=(hstr[c]>>2);bstr[w]=Bs64[bas];w++; bas=(hstr[c]<<6)|(hstr[c+1]>>2);bas>>=2;c++;bstr[w]=Bs64[bas];w++; bas=(hstr[c]<<4)|(hstr[c+1]>>4);bas>>=2;c++;bstr[w]=Bs64[bas];w++; bas=(hstr[c]<<2);bas>>=2; c++; bstr[w]=Bs64[bas]; w++;}
     switch(n-(m*3)){
        case 1:bas=(hstr[n-1]>>2);bstr[w]=Bs64[bas];w++; bas=(hstr[n-1]<<6);bas>>=2;bstr[w]=Bs64[bas];w++;break;
	    case 2:bas=(hstr[n-2]>>2);bstr[w]=Bs64[bas];w++; bas=(hstr[n-2]<<6)|(hstr[n-1]>>2);bas>>=2;bstr[w]=Bs64[bas];w++; bas=(hstr[n-1]<<4);bas>>=2;bstr[w]=Bs64[bas];w++; break;
	} return w;
}
/*base64 解码: hstr=>要解码的数据，bstr=>解码后的数据存储区，Bs64=>base64编码串*/
unsigned int BaseDecode(unsigned char *hstr,unsigned char *bstr,unsigned char *Bs64){
     unsigned int i,n,c=0; unsigned char m;
     n=strlen(hstr);for(i=0;i<n;i++){for(m=0;m<65;m++){if(Bs64[m]==hstr[i]){bstr[i]=(m<<2);break;}}}n=(strlen(hstr)/4);
     for(i=0;i<n;i++){bstr[c]=bstr[i*4]|(bstr[(i*4)+1]>>6);c++;bstr[c]=(bstr[(i*4)+1]<<2)|(bstr[(i*4)+2]>>4);c++;bstr[c]=(bstr[(i*4)+2]<<4)|(bstr[(i*4)+3]>>2);c++;}
   switch(strlen(hstr)-(n*4)){
      case 2:bstr[c]=bstr[i*4]|(bstr[(i*4)+1]>>6);c++;break;
      case 3:bstr[c]=bstr[i*4]|(bstr[(i*4)+1]>>6);c++;bstr[c]=(bstr[(i*4)+1]<<2)|(bstr[(i*4)+2]>>4);c++;break;
   } return c;
}

/*Tea 解码过程*/
void encrypt (uint32_t v[], uint32_t k[]) {
    uint32_t v0=v[0], v1=v[1], sum=0, i;           /* set up */
    uint32_t delta=0x9e3779b9;                     /* a key schedule constant */
    uint32_t k0=k[0], k1=k[1], k2=k[2], k3=k[3];   /* cache key */
    for (i=0; i < 32; i++) {                       /* basic cycle start */
        sum += delta;
        v0 += ((v1<<4) + k0) ^ (v1 + sum) ^ ((v1>>5) + k1);
        v1 += ((v0<<4) + k2) ^ (v0 + sum) ^ ((v0>>5) + k3);
    }                                              /* end cycle */
    v[0]=v0; v[1]=v1;
}
/*Tea 编码过程*/
void decrypt (uint32_t v[], uint32_t k[]) {
    uint32_t v0=v[0], v1=v[1], sum=0xC6EF3720, i;  /* set up */
    uint32_t delta=0x9e3779b9;                     /* a key schedule constant */
    uint32_t k0=k[0], k1=k[1], k2=k[2], k3=k[3];   /* cache key */
    for (i=0; i<32; i++) {                         /* basic cycle start */
        v1 -= ((v0<<4) + k2) ^ (v0 + sum) ^ ((v0>>5) + k3);
        v0 -= ((v1<<4) + k0) ^ (v1 + sum) ^ ((v1>>5) + k1);
        sum -= delta;
    }                                              /* end cycle */
    v[0]=v0; v[1]=v1;
}
/*Tea 编码+解码*/
int Hcrypt(unsigned char act,unsigned char *key,unsigned char *dat,unsigned int c){
  int i=0,n=0,m=0,t=0; uint32_t v[2]={0x00,0x00}, k[4]={0x00,0x00,0x00,0x00};
  if(16!=strlen(key)){return -1;}
  for(n=0;n<4;n++){i=0;for(i=0;i<4;i++){k[n]=k[n]<<8|key[m]; m++;}} /*Key*/
//  printf("[%08x%08x%08x%08x]\n",k[0],k[1],k[2],k[3]);
  if(0!=(c%8)){return -2;}
  //c=strlen(dat)-(strlen(dat)%8); 
  i=0;n=0;m=0;
  while(m<c){
    for(n=0;n<2;n++){i=0;for(i=0;i<4;i++){v[n]=v[n]<<8|dat[m]; m++;}}
//    printf("%08x%08x\n",v[0],v[1]);
    if(act>0){decrypt(v,k);/*解码*/}else{encrypt(v,k);/*编码*/}
    dat[t]=v[0]>>24; dat[t+1]=(v[0]>>16)&0x000000ff; dat[t+2]=(v[0]>>8)&0x000000ff; dat[t+3]=v[0]&0x000000ff;
    dat[t+4]=v[1]>>24; dat[t+5]=(v[1]>>16)&0x000000ff; dat[t+6]=(v[1]>>8)&0x000000ff; dat[t+7]=v[1]&0x000000ff;
    t+=8; n=0;
    //printf("%08x%08x\n",v[0],v[1]);
  }
  dat[t]=0;
  return t;
}

//编码解码
int HwCrypt(unsigned char act,unsigned char *dats){
    unsigned char *HwDat,HwTmp[64]={0};
    int i=0,b=0,n=0;

    b=strlen(dats); n=b*2; HwDat=(unsigned char *)malloc(n); memset(HwDat,0,n);
    if(0==act){
       memcpy(HwDat,dats,b); i=b%8; if(0!=i){i=8-i; hrand(HwTmp,i); memcpy(HwDat+b,HwTmp,i); HwDat[b]=0x00; b+=i;}
       b=Hcrypt(0,HwKey,HwDat,b); //编码
       i=BaseEncode(HwDat,b,dats,BaseStr); dats[i]=0x00;
       return i;
    }else{  //解码
       b=BaseDecode(dats,HwDat,BaseStr);
       i=b%8; if(0!=i){b=b-i;}
       b=Hcrypt(1,HwKey,HwDat,b); //解码
       memset(dats,0,strlen(dats)); memcpy(dats,HwDat,b); dats[b]=0x00;
       return b;
    }
/*
    unsigned char HwDat[1025]={0};
    unsigned char HwTmp[2048]={0};
    int i=0,b=0;

         if(0==act){
            b=strlen(dats); if(b>1024){b=1024;}  memcpy(HwDat,dats,b); i=b%8; if(0!=i){i=8-i; hrand(HwTmp,i); memcpy(HwDat+b,HwTmp,i); HwDat[b]=0x00; b+=i;}
            b=Hcrypt(0,HwKey,HwDat,b); //编码
            memset(HwTmp,0,2048);
            i=BaseEncode(HwDat,b,HwTmp,BaseStr);
            memset(dats,0,strlen(dats)); memcpy(dats,HwTmp,i);
//            printf("\n编码后: %s\n\n",HwTmp);
           return i;
         }else{  //解码
            b=BaseDecode(dats,HwDat,BaseStr);
            i=b%8; if(0!=i){b=b-i;}
            b=Hcrypt(1,HwKey,HwDat,b); //解码
//            printf("\n%s\n\n",HwDat);
            memset(dats,0,strlen(dats)); memcpy(dats,HwDat,b);
            return b;
         }
printf("\nend\n");
*/
// return 0;
}


