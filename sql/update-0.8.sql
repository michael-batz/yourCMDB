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
