CREATE TABLE `ssl`.`ssl_auth` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`uid` INT NOT NULL COMMENT '会员id',
`pfxcontent` TEXT NOT NULL COMMENT '证书内容',
`outtime` INT NOT NULL COMMENT '过期时间',
`create` INT NOT NULL COMMENT '生成时间'
) ENGINE = MYISAM COMMENT = '证书安装记录表';
