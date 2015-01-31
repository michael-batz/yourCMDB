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
  accessgroup VARCHAR(255) NOT NULL,
  PRIMARY KEY (username)
) CHARACTER SET utf8 ENGINE=INNODB;

create table CmdbAccessRules (
  accessgroup VARCHAR(255) NOT NULL,
  applicationpart VARCHAR(255) NOT NULL,
  access INT UNSIGNED NOT NULL,
  PRIMARY KEY (accessgroup, applicationpart)
) CHARACTER SET utf8 ENGINE=INNODB;

insert into CmdbLocalUser (username, passwordhash, accessgroup)  VALUES('admin', '9ab732164fd0d571173d4441ab042ef883bc1787ae84a9b851cf4c34541580d2', 'admin');
insert into CmdbAccessRules (accessgroup, applicationpart, access)  VALUES('admin', 'default', 2);
insert into CmdbAccessRules (accessgroup, applicationpart, access)  VALUES('user', 'default', 2);  
insert into CmdbAccessRules (accessgroup, applicationpart, access)  VALUES('user', 'admin', 0);
insert into CmdbAccessRules (accessgroup, applicationpart, access)  VALUES('user', 'rest', 0);
