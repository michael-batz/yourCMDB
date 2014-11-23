create table CmdbLocalUser (
  username VARCHAR(255) NOT NULL,
  passwordhash TEXT NOT NULL,
  PRIMARY KEY (username)
) CHARACTER SET utf8 ENGINE=INNODB;
