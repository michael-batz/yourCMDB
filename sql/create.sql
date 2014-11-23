create table CmdbObject (
  assetid INT UNSIGNED NOT NULL AUTO_INCREMENT, 
  type VARCHAR(64), 
  active ENUM('A', 'D', 'N'), 
  PRIMARY KEY (assetid)
) CHARACTER SET utf8 ENGINE=INNODB;

create table CmdbObjectField (
  assetid INT UNSIGNED NOT NULL, 
  fieldkey VARCHAR(64) NOT NULL, 
  fieldvalue TEXT, 
  PRIMARY KEY (assetid, fieldkey), 
  FOREIGN KEY (assetid) REFERENCES CmdbObject(assetid) ON DELETE CASCADE
) CHARACTER SET utf8 ENGINE=INNODB;

create table CmdbObjectLog(
  assetid INT UNSIGNED NOT NULL, 
  action ENUM('add', 'change', 'delete') NOT NULL, 
  date DATETIME NOT NULL,
  FOREIGN KEY (assetid) REFERENCES CmdbObject(assetid) ON DELETE CASCADE
) CHARACTER SET utf8 ENGINE=INNODB;


create table CmdbObjectLink(
  assetidA INT UNSIGNED NOT NULL, 
  assetidB INT UNSIGNED NOT NULL,
  FOREIGN KEY (assetidA) REFERENCES CmdbObject(assetid) ON DELETE CASCADE,
  FOREIGN KEY (assetidB) REFERENCES CmdbObject(assetid) ON DELETE CASCADE
) CHARACTER SET utf8 ENGINE=INNODB;

create table CmdbJob (
  jobid INT UNSIGNED NOT NULL AUTO_INCREMENT, 
  action VARCHAR(255), 
  actionParameter VARCHAR(255), 
  timestamp DATETIME, 
  PRIMARY KEY (jobid)
) CHARACTER SET utf8 ENGINE=INNODB;

create table CmdbLocalUser (
  username VARCHAR(255) NOT NULL,
  passwordhash TEXT NOT NULL,
  PRIMARY KEY (username)
) CHARACTER SET utf8 ENGINE=INNODB;
