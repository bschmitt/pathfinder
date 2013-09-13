CREATE TABLE tx_pathfinder_domain_model_cache (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	rootpage int(11) DEFAULT '0' NOT NULL,
	mpvar tinytext,
	path text NOT NULL,
	dependencies tinytext,
	sys_language_uid int(11) DEFAULT '0',
	tstamp int(11) unsigned DEFAULT '0',
	crdate int(11) unsigned DEFAULT '0',
	deleted tinyint(4) unsigned DEFAULT '0',
	hidden tinyint(4) unsigned DEFAULT '0',
	starttime int(11) DEFAULT '0',
	endtime int(11) DEFAULT '0',
 	l18n_parent int(11) DEFAULT '0',
 	l18n_diffsource mediumblob,
	PRIMARY KEY (uid),
	KEY path1 (rootpage,path(255)),
	KEY path2 (pid,sys_language_uid,rootpage),
	UNIQUE INDEX path3 (pid,path(255),sys_language_uid,rootpage)
) ENGINE=InnoDB;

CREATE TABLE tx_pathfinder_domain_model_cachehistory (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	rootpage int(11) DEFAULT '0' NOT NULL,
	mpvar tinytext,
	path text NOT NULL,
	sys_language_uid int(11) DEFAULT '0',
	tstamp int(11) unsigned DEFAULT '0',
	crdate int(11) unsigned DEFAULT '0',
	deleted tinyint(4) unsigned DEFAULT '0',
	hidden tinyint(4) unsigned DEFAULT '0',
	starttime int(11) DEFAULT '0',
	endtime int(11) DEFAULT '0',
 	l18n_parent int(11) DEFAULT '0',
 	l18n_diffsource mediumblob,
	PRIMARY KEY (uid),
	KEY path1 (rootpage,path(255)),
	KEY path2 (pid,sys_language_uid,rootpage),
	UNIQUE INDEX path3 (pid,path(255),sys_language_uid,rootpage)
) ENGINE=InnoDB;

CREATE TABLE tx_pathfinder_domain_model_meta (
	ns tinytext NOT NULL,
	hash tinytext NOT NULL
	value text,
	PRIMARY KEY (hash(255))
) ENGINE=InnoDB;

CREATE TABLE tx_pathfinder_domain_model_404 (
	uid int(11) NOT NULL auto_increment,
	counter int(11) DEFAULT '1',
	path text NOT NULL,
	PRIMARY KEY (uid),
) ENGINE=InnoDB;

CREATE TABLE pages (
	tx_pathfinder_exclude int(1) DEFAULT '0' NOT NULL,
);
