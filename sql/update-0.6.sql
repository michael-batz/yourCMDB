create table CmdbJob (
  jobid INT UNSIGNED NOT NULL AUTO_INCREMENT,   
  action VARCHAR(255),
  actionParameter VARCHAR(255),
  timestamp DATETIME,          
  PRIMARY KEY (jobid)
) CHARACTER SET utf8 ENGINE=INNODB;

