Create table initab(
   `id` INTEGER PRIMARY KEY,        /*唯一标志ID*/
   `nkey` char(13) not null,        /*配置项名*/
   `var` varchar(300) not null,     /*配置值*/
   `str` varchar(300) default null  /*配置说明*/
);

