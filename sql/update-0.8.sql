create table CmdbLocalUser (
  username VARCHAR(255) NOT NULL,
  passwordhash TEXT NOT NULL,
  accessgroup VARCHAR(255) NOT NULL,
  PRIMARY KEY (username)
) CHARACTER SET utf8 ENGINE=INNODB;
