#
# Table structure for table 'tx_agora_domain_model_forum'
#
CREATE TABLE tx_agora_domain_model_forum (

	uid                             INT(11)                       NOT NULL AUTO_INCREMENT,
	pid                             INT(11) DEFAULT 0             NOT NULL,

	title                           VARCHAR(255) DEFAULT ''       NOT NULL,
	description                     TEXT                          NOT NULL,

	parent                          INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	sub_forums                      INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	threads                         INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	groups_with_read_access         INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	groups_with_write_access        INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	groups_with_modification_access INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	users_with_read_access          INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	users_with_write_access         INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	users_with_modification_access  INT(11) UNSIGNED DEFAULT 0    NOT NULL,

	tstamp                          INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	crdate                          INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	cruser_id                       INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	deleted                         TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	hidden                          TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	starttime                       INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	endtime                         INT(11) UNSIGNED DEFAULT 0    NOT NULL,

	t3ver_oid                       INT(11) DEFAULT 0             NOT NULL,
	t3ver_id                        INT(11) DEFAULT 0             NOT NULL,
	t3ver_wsid                      INT(11) DEFAULT 0             NOT NULL,
	t3ver_label                     VARCHAR(255) DEFAULT ''       NOT NULL,
	t3ver_state                     TINYINT(4) DEFAULT 0          NOT NULL,
	t3ver_stage                     INT(11) DEFAULT 0             NOT NULL,
	t3ver_count                     INT(11) DEFAULT 0             NOT NULL,
	t3ver_tstamp                    INT(11) DEFAULT 0             NOT NULL,
	t3ver_move_id                   INT(11) DEFAULT 0             NOT NULL,
	sorting                         INT(11) DEFAULT 0             NOT NULL,

	sys_language_uid                INT(11) DEFAULT 0             NOT NULL,
	l10n_parent                     INT(11) DEFAULT 0             NOT NULL,
	l10n_diffsource                 MEDIUMBLOB,
	l10n_state                      TEXT,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid),
	KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_agora_domain_model_post'
#
CREATE TABLE tx_agora_domain_model_post (

	uid                 INT(11)                       NOT NULL AUTO_INCREMENT,
	pid                 INT(11) DEFAULT 0             NOT NULL,

	thread              INT(11) UNSIGNED                       DEFAULT 0,
	forum               INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	replies             INT(11) UNSIGNED DEFAULT 0    NOT NULL,

	topic               VARCHAR(255) DEFAULT ''       NOT NULL,
	text                TEXT                          NOT NULL,
	publishing_date     INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	quoted_post         INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	historical_versions INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	original_post       INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	ratings			        INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	voting              INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	attachments         INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	creator             INT(11) UNSIGNED                       DEFAULT 0,

	tstamp              INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	crdate              INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	cruser_id           INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	deleted             TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	hidden              TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	starttime           INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	endtime             INT(11) UNSIGNED DEFAULT 0    NOT NULL,

	t3ver_oid           INT(11) DEFAULT 0             NOT NULL,
	t3ver_id            INT(11) DEFAULT 0             NOT NULL,
	t3ver_wsid          INT(11) DEFAULT 0             NOT NULL,
	t3ver_label         VARCHAR(255) DEFAULT ''       NOT NULL,
	t3ver_state         TINYINT(4) DEFAULT '0'        NOT NULL,
	t3ver_stage         INT(11) DEFAULT 0             NOT NULL,
	t3ver_count         INT(11) DEFAULT 0             NOT NULL,
	t3ver_tstamp        INT(11) DEFAULT 0             NOT NULL,
	t3ver_move_id       INT(11) DEFAULT 0             NOT NULL,

	sys_language_uid    INT(11) DEFAULT 0             NOT NULL,
	l10n_parent         INT(11) DEFAULT 0             NOT NULL,
	l10n_diffsource     MEDIUMBLOB,
	l10n_state          TEXT,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid),
	KEY language (l10n_parent, sys_language_uid)

);


#
# Table structure for table 'tx_agora_domain_model_thread'
#
CREATE TABLE tx_agora_domain_model_thread (

	uid                             INT(11)                         NOT NULL AUTO_INCREMENT,
	pid                             INT(11) DEFAULT 0               NOT NULL,

	forum                           INT(11) UNSIGNED DEFAULT 0      NOT NULL,
	observers                       INT(11) UNSIGNED DEFAULT 0      NOT NULL,
	readers		                      INT(11) UNSIGNED DEFAULT 0      NOT NULL,

	title                           VARCHAR(255) DEFAULT ''         NOT NULL,
	solved                          TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
	closed                          TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
	sticky                          TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL,
	creator                         INT(11) UNSIGNED                         DEFAULT 0,
	posts                           INT(11) UNSIGNED DEFAULT 0      NOT NULL,
	tags                            INT(11) UNSIGNED DEFAULT 0      NOT NULL,
	views                           INT(11) UNSIGNED DEFAULT 0      NOT NULL,

	tstamp                          INT(11) UNSIGNED DEFAULT 0      NOT NULL,
	crdate                          INT(11) UNSIGNED DEFAULT 0      NOT NULL,
	cruser_id                       INT(11) UNSIGNED DEFAULT 0      NOT NULL,
	deleted                         TINYINT(4) UNSIGNED DEFAULT 0   NOT NULL,
	hidden                          TINYINT(4) UNSIGNED DEFAULT 0   NOT NULL,
	starttime                       INT(11) UNSIGNED DEFAULT 0      NOT NULL,
	endtime                         INT(11) UNSIGNED DEFAULT 0      NOT NULL,

	t3ver_oid                       INT(11) DEFAULT 0               NOT NULL,
	t3ver_id                        INT(11) DEFAULT 0               NOT NULL,
	t3ver_wsid                      INT(11) DEFAULT 0               NOT NULL,
	t3ver_label                     VARCHAR(255) DEFAULT ''         NOT NULL,
	t3ver_state                     TINYINT(4) DEFAULT '0'          NOT NULL,
	t3ver_stage                     INT(11) DEFAULT 0               NOT NULL,
	t3ver_count                     INT(11) DEFAULT 0               NOT NULL,
	t3ver_tstamp                    INT(11) DEFAULT 0               NOT NULL,
	t3ver_move_id                   INT(11) DEFAULT 0               NOT NULL,
	sorting                         INT(11) DEFAULT 0               NOT NULL,

	sys_language_uid                INT(11) DEFAULT 0               NOT NULL,
	l10n_parent                     INT(11) DEFAULT 0               NOT NULL,
	l10n_diffsource                 MEDIUMBLOB,
	l10n_state                      TEXT,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid),
	KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_agora_domain_model_rating'
#
CREATE TABLE tx_agora_domain_model_rating (
	uid              INT(11)                       NOT NULL AUTO_INCREMENT,
	pid              INT(11) DEFAULT 0             NOT NULL,

	post             INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	user             INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	value            INT(11) DEFAULT 0             NOT NULL,

	tstamp           INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	crdate           INT(11) UNSIGNED DEFAULT 0    NOT NULL,

	t3ver_oid        INT(11) DEFAULT 0             NOT NULL,
	t3ver_id         INT(11) DEFAULT 0             NOT NULL,
	t3ver_wsid       INT(11) DEFAULT 0             NOT NULL,
	t3ver_label      VARCHAR(255) DEFAULT ''       NOT NULL,
	t3ver_state      TINYINT(4) DEFAULT '0'        NOT NULL,
	t3ver_stage      INT(11) DEFAULT 0             NOT NULL,
	t3ver_count      INT(11) DEFAULT 0             NOT NULL,
	t3ver_tstamp     INT(11) DEFAULT 0             NOT NULL,
	t3ver_move_id    INT(11) DEFAULT 0             NOT NULL,

	sys_language_uid INT(11) DEFAULT 0             NOT NULL,
	l10n_parent      INT(11) DEFAULT 0             NOT NULL,
	l10n_diffsource  MEDIUMBLOB,
	l10n_state       TEXT,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid),
	KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_agora_domain_model_tag'
#
CREATE TABLE tx_agora_domain_model_tag (

	uid              INT(11)                         NOT NULL AUTO_INCREMENT,
	pid              INT(11) DEFAULT 0               NOT NULL,

	title            VARCHAR(255) DEFAULT ''         NOT NULL,

	tstamp           INT(11) UNSIGNED DEFAULT 0      NOT NULL,
	crdate           INT(11) UNSIGNED DEFAULT 0      NOT NULL,
	cruser_id        INT(11) UNSIGNED DEFAULT 0      NOT NULL,
	deleted          TINYINT(4) UNSIGNED DEFAULT 0   NOT NULL,
	hidden           TINYINT(4) UNSIGNED DEFAULT 0   NOT NULL,
	starttime        INT(11) UNSIGNED DEFAULT 0      NOT NULL,
	endtime          INT(11) UNSIGNED DEFAULT 0      NOT NULL,

	t3ver_oid        INT(11) DEFAULT 0               NOT NULL,
	t3ver_id         INT(11) DEFAULT 0               NOT NULL,
	t3ver_wsid       INT(11) DEFAULT 0               NOT NULL,
	t3ver_label      VARCHAR(255) DEFAULT ''         NOT NULL,
	t3ver_state      TINYINT(4) DEFAULT '0'          NOT NULL,
	t3ver_stage      INT(11) DEFAULT 0               NOT NULL,
	t3ver_count      INT(11) DEFAULT 0               NOT NULL,
	t3ver_tstamp     INT(11) DEFAULT 0               NOT NULL,
	t3ver_move_id    INT(11) DEFAULT 0               NOT NULL,
	sorting          INT(11) DEFAULT 0               NOT NULL,

	sys_language_uid INT(11) DEFAULT 0               NOT NULL,
	l10n_parent      INT(11) DEFAULT 0               NOT NULL,
	l10n_diffsource  MEDIUMBLOB,
	l10n_state       TEXT,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid),
	KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	view             	INT(11) 			UNSIGNED DEFAULT 0 	NOT NULL,
	signiture        	TEXT    			                   	NOT NULL,
	observed_threads 	INT(11) 			UNSIGNED DEFAULT 0 	NOT NULL,
	read_threads 			INT(11) 			UNSIGNED DEFAULT 0 	NOT NULL,
	favorite_posts   	INT(11) 			UNSIGNED DEFAULT 0 	NOT NULL,
	tx_extbase_type  	VARCHAR(255) 	DEFAULT ''    			NOT NULL
);

#
# Table structure for table 'fe_groups'
#
CREATE TABLE fe_groups (
	tx_extbase_type VARCHAR(255) DEFAULT '' NOT NULL
);

# Table structure for table 'tx_agora_domain_model_notification'
#
CREATE TABLE tx_agora_domain_model_notification (

	uid              INT(11)                         NOT NULL AUTO_INCREMENT,
	pid              INT(11) DEFAULT '0'             NOT NULL,

	type             INT(11) UNSIGNED                         DEFAULT '0',
	post             INT(11) UNSIGNED                         DEFAULT '0',
	thread           INT(11) UNSIGNED                         DEFAULT '0',
	owner            INT(11) UNSIGNED                         DEFAULT '0',
	user             INT(11) UNSIGNED                         DEFAULT '0',
	title            VARCHAR(255) DEFAULT ''         NOT NULL,
	description      TEXT                            NOT NULL,
	data             TEXT                            NOT NULL,
	link             VARCHAR(255) DEFAULT ''         NOT NULL,
	sent             INT(11) UNSIGNED                         DEFAULT '0',
	count             INT(11) UNSIGNED                        DEFAULT '0',
	page             INT(11) UNSIGNED                         DEFAULT '0',

	tstamp           INT(11) UNSIGNED DEFAULT '0'    NOT NULL,
	crdate           INT(11) UNSIGNED DEFAULT '0'    NOT NULL,
	cruser_id        INT(11) UNSIGNED DEFAULT '0'    NOT NULL,
	deleted          TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,
	hidden           TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,
	starttime        INT(11) UNSIGNED DEFAULT '0'    NOT NULL,
	endtime          INT(11) UNSIGNED DEFAULT '0'    NOT NULL,

	t3ver_oid        INT(11) DEFAULT '0'             NOT NULL,
	t3ver_id         INT(11) DEFAULT '0'             NOT NULL,
	t3ver_wsid       INT(11) DEFAULT '0'             NOT NULL,
	t3ver_label      VARCHAR(255) DEFAULT ''         NOT NULL,
	t3ver_state      TINYINT(4) DEFAULT '0'          NOT NULL,
	t3ver_stage      INT(11) DEFAULT '0'             NOT NULL,
	t3ver_count      INT(11) DEFAULT '0'             NOT NULL,
	t3ver_tstamp     INT(11) DEFAULT '0'             NOT NULL,
	t3ver_move_id    INT(11) DEFAULT '0'             NOT NULL,

	sys_language_uid INT(11) DEFAULT '0'             NOT NULL,
	l10n_parent      INT(11) DEFAULT '0'             NOT NULL,
	l10n_diffsource  MEDIUMBLOB,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid),
	KEY language (l10n_parent, sys_language_uid)
);

#
# Table structure for table 'tx_agora_domain_model_action'
#
CREATE TABLE tx_agora_domain_model_action (

	uid              INT(11)                         NOT NULL AUTO_INCREMENT,
	pid              INT(11) DEFAULT '0'             NOT NULL,

	page             INT(11) UNSIGNED                         DEFAULT '0',
	type             INT(11) UNSIGNED                         DEFAULT '0',
	post             INT(11) UNSIGNED                         DEFAULT '0',
	thread           INT(11) UNSIGNED                         DEFAULT '0',
	user             INT(11) UNSIGNED                         DEFAULT '0',
	groups           VARCHAR(255) DEFAULT ''         NOT NULL,
	title            VARCHAR(255) DEFAULT ''         NOT NULL,
	description      TEXT                            NOT NULL,
	data             TEXT                            NOT NULL,
	link             VARCHAR(255) DEFAULT ''         NOT NULL,
	sent             INT(11) UNSIGNED                         DEFAULT '0',
	hash             VARCHAR(255) DEFAULT ''         NOT NULL,

	tstamp           INT(11) UNSIGNED DEFAULT '0'    NOT NULL,
	crdate           INT(11) UNSIGNED DEFAULT '0'    NOT NULL,
	cruser_id        INT(11) UNSIGNED DEFAULT '0'    NOT NULL,
	deleted          TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,
	hidden           TINYINT(4) UNSIGNED DEFAULT '0' NOT NULL,
	starttime        INT(11) UNSIGNED DEFAULT '0'    NOT NULL,
	endtime          INT(11) UNSIGNED DEFAULT '0'    NOT NULL,

	t3ver_oid        INT(11) DEFAULT '0'             NOT NULL,
	t3ver_id         INT(11) DEFAULT '0'             NOT NULL,
	t3ver_wsid       INT(11) DEFAULT '0'             NOT NULL,
	t3ver_label      VARCHAR(255) DEFAULT ''         NOT NULL,
	t3ver_state      TINYINT(4) DEFAULT '0'          NOT NULL,
	t3ver_stage      INT(11) DEFAULT '0'             NOT NULL,
	t3ver_count      INT(11) DEFAULT '0'             NOT NULL,
	t3ver_tstamp     INT(11) DEFAULT '0'             NOT NULL,
	t3ver_move_id    INT(11) DEFAULT '0'             NOT NULL,

	sys_language_uid INT(11) DEFAULT '0'             NOT NULL,
	l10n_parent      INT(11) DEFAULT '0'             NOT NULL,
	l10n_diffsource  MEDIUMBLOB,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid),
	KEY language (l10n_parent, sys_language_uid)
);

#
# Table structure for table 'tx_agora_forum_groupswithreadaccess_mm'
#
CREATE TABLE tx_agora_forum_groupswithreadaccess_mm (
	uid_local       INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	uid_foreign     INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting         INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting_foreign INT(11) UNSIGNED DEFAULT 0 NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_agora_forum_grouspwithwriteaccess_mm'
#
CREATE TABLE tx_agora_forum_groupswithwriteaccess_mm (
	uid_local       INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	uid_foreign     INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting         INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting_foreign INT(11) UNSIGNED DEFAULT 0 NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_agora_forum_groupswithmodificationaccess_mm'
#
CREATE TABLE tx_agora_forum_groupswithmodificationaccess_mm (
	uid_local       INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	uid_foreign     INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting         INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting_foreign INT(11) UNSIGNED DEFAULT 0 NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_agora_forum_userswithreadaccess_mm'
#
CREATE TABLE tx_agora_forum_userswithreadaccess_mm (
	uid_local       INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	uid_foreign     INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting         INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting_foreign INT(11) UNSIGNED DEFAULT 0 NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_agora_forum_userswithwriteaccess_mm'
#
CREATE TABLE tx_agora_forum_userswithwriteaccess_mm (
	uid_local       INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	uid_foreign     INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting         INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting_foreign INT(11) UNSIGNED DEFAULT 0 NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_agora_forum_userswithmodificationaccess_mm'
#
CREATE TABLE tx_agora_forum_userswithmodificationaccess_mm (
	uid_local       INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	uid_foreign     INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting         INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting_foreign INT(11) UNSIGNED DEFAULT 0 NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_agora_forum_user_post_mm'
#
CREATE TABLE tx_agora_feuser_post_mm (
	uid_local       INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	uid_foreign     INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting         INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting_foreign INT(11) UNSIGNED DEFAULT 0 NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_agora_forum_user_thread_mm'
#
CREATE TABLE tx_agora_feuser_thread_mm (
	uid_local       INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	uid_foreign     INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting         INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting_foreign INT(11) UNSIGNED DEFAULT 0 NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_agora_forum_user_thread_mm'
#
CREATE TABLE tx_agora_tag_thread_mm (
	uid_local       INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	uid_foreign     INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting         INT(11) UNSIGNED DEFAULT 0 NOT NULL,
	sorting_foreign INT(11) UNSIGNED DEFAULT 0 NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);


/**
 * ============================================================
 * TABLES WITH JET NO USE
 * ============================================================
 */

#
# Table structure for table 'tx_agora_domain_model_voting'
#
CREATE TABLE tx_agora_domain_model_voting (

	uid              INT(11)                       NOT NULL AUTO_INCREMENT,
	pid              INT(11) DEFAULT 0             NOT NULL,

	question         VARCHAR(255) DEFAULT ''       NOT NULL,
	answers          INT(11) UNSIGNED DEFAULT 0    NOT NULL,

	tstamp           INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	crdate           INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	cruser_id        INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	deleted          TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	hidden           TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	starttime        INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	endtime          INT(11) UNSIGNED DEFAULT 0    NOT NULL,

	t3ver_oid        INT(11) DEFAULT 0             NOT NULL,
	t3ver_id         INT(11) DEFAULT 0             NOT NULL,
	t3ver_wsid       INT(11) DEFAULT 0             NOT NULL,
	t3ver_label      VARCHAR(255) DEFAULT ''       NOT NULL,
	t3ver_state      TINYINT(4) DEFAULT '0'        NOT NULL,
	t3ver_stage      INT(11) DEFAULT 0             NOT NULL,
	t3ver_count      INT(11) DEFAULT 0             NOT NULL,
	t3ver_tstamp     INT(11) DEFAULT 0             NOT NULL,
	t3ver_move_id    INT(11) DEFAULT 0             NOT NULL,

	sys_language_uid INT(11) DEFAULT 0             NOT NULL,
	l10n_parent      INT(11) DEFAULT 0             NOT NULL,
	l10n_diffsource  MEDIUMBLOB,
	l10n_state       TEXT,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid),
	KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_observed_threadsagora_domain_model_attachment'
#
CREATE TABLE tx_agora_domain_model_attachment (

	uid              INT(11)                       NOT NULL AUTO_INCREMENT,
	pid              INT(11) DEFAULT 0             NOT NULL,

	post             INT(11) UNSIGNED DEFAULT 0    NOT NULL,

	title            VARCHAR(255) DEFAULT ''       NOT NULL,
	file             INT(11) UNSIGNED              NOT NULL DEFAULT '0',

	tstamp           INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	crdate           INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	cruser_id        INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	deleted          TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	hidden           TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	starttime        INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	endtime          INT(11) UNSIGNED DEFAULT 0    NOT NULL,

	t3ver_oid        INT(11) DEFAULT 0             NOT NULL,
	t3ver_id         INT(11) DEFAULT 0             NOT NULL,
	t3ver_wsid       INT(11) DEFAULT 0             NOT NULL,
	t3ver_label      VARCHAR(255) DEFAULT ''       NOT NULL,
	t3ver_state      TINYINT(4) DEFAULT '0'        NOT NULL,
	t3ver_stage      INT(11) DEFAULT 0             NOT NULL,
	t3ver_count      INT(11) DEFAULT 0             NOT NULL,
	t3ver_tstamp     INT(11) DEFAULT 0             NOT NULL,
	t3ver_move_id    INT(11) DEFAULT 0             NOT NULL,
	sorting          INT(11) DEFAULT 0             NOT NULL,

	sys_language_uid INT(11) DEFAULT 0             NOT NULL,
	l10n_parent      INT(11) DEFAULT 0             NOT NULL,
	l10n_diffsource  MEDIUMBLOB,
	l10n_state       TEXT,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid),
	KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_agora_domain_model_vote'
#
CREATE TABLE tx_agora_domain_model_vote (

	uid              INT(11)                       NOT NULL AUTO_INCREMENT,
	pid              INT(11) DEFAULT 0             NOT NULL,

	voting           INT(11) UNSIGNED                       DEFAULT 0,
	voting_answers   INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	user             INT(11) UNSIGNED                       DEFAULT 0,

	tstamp           INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	crdate           INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	cruser_id        INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	deleted          TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	hidden           TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	starttime        INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	endtime          INT(11) UNSIGNED DEFAULT 0    NOT NULL,

	t3ver_oid        INT(11) DEFAULT 0             NOT NULL,
	t3ver_id         INT(11) DEFAULT 0             NOT NULL,
	t3ver_wsid       INT(11) DEFAULT 0             NOT NULL,
	t3ver_label      VARCHAR(255) DEFAULT ''       NOT NULL,
	t3ver_state      TINYINT(4) DEFAULT '0'        NOT NULL,
	t3ver_stage      INT(11) DEFAULT 0             NOT NULL,
	t3ver_count      INT(11) DEFAULT 0             NOT NULL,
	t3ver_tstamp     INT(11) DEFAULT 0             NOT NULL,
	t3ver_move_id    INT(11) DEFAULT 0             NOT NULL,

	sys_language_uid INT(11) DEFAULT 0             NOT NULL,
	l10n_parent      INT(11) DEFAULT 0             NOT NULL,
	l10n_diffsource  MEDIUMBLOB,
	l10n_state       TEXT,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid),
	KEY language (l10n_parent, sys_language_uid)

);

#
# Table structure for table 'tx_agora_domain_model_votinganswer'
#
CREATE TABLE tx_agora_domain_model_votinganswer (

	uid              INT(11)                       NOT NULL AUTO_INCREMENT,
	pid              INT(11) DEFAULT 0             NOT NULL,

	voting           INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	vote             INT(11) UNSIGNED DEFAULT 0    NOT NULL,

	answer           TEXT                          NOT NULL,

	tstamp           INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	crdate           INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	cruser_id        INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	deleted          TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	hidden           TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	starttime        INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	endtime          INT(11) UNSIGNED DEFAULT 0    NOT NULL,

	t3ver_oid        INT(11) DEFAULT 0             NOT NULL,
	t3ver_id         INT(11) DEFAULT 0             NOT NULL,
	t3ver_wsid       INT(11) DEFAULT 0             NOT NULL,
	t3ver_label      VARCHAR(255) DEFAULT ''       NOT NULL,
	t3ver_state      TINYINT(4) DEFAULT '0'        NOT NULL,
	t3ver_stage      INT(11) DEFAULT 0             NOT NULL,
	t3ver_count      INT(11) DEFAULT 0             NOT NULL,
	t3ver_tstamp     INT(11) DEFAULT 0             NOT NULL,
	t3ver_move_id    INT(11) DEFAULT 0             NOT NULL,
	sorting          INT(11) DEFAULT 0             NOT NULL,

	sys_language_uid INT(11) DEFAULT 0             NOT NULL,
	l10n_parent      INT(11) DEFAULT 0             NOT NULL,
	l10n_diffsource  MEDIUMBLOB,
	l10n_state       TEXT,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid),
	KEY language (l10n_parent, sys_language_uid)

);


#
# Table structure for table 'tx_agora_domain_model_view'
#
CREATE TABLE tx_agora_domain_model_view (

	uid              INT(11)                       NOT NULL AUTO_INCREMENT,
	pid              INT(11) DEFAULT 0             NOT NULL,

	thread           INT(11) UNSIGNED                       DEFAULT 0,
	user             INT(11) UNSIGNED DEFAULT 0    NOT NULL,

	tstamp           INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	crdate           INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	cruser_id        INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	deleted          TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	hidden           TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	starttime        INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	endtime          INT(11) UNSIGNED DEFAULT 0    NOT NULL,

	t3ver_oid        INT(11) DEFAULT 0             NOT NULL,
	t3ver_id         INT(11) DEFAULT 0             NOT NULL,
	t3ver_wsid       INT(11) DEFAULT 0             NOT NULL,
	t3ver_label      VARCHAR(255) DEFAULT ''       NOT NULL,
	t3ver_state      TINYINT(4) DEFAULT 0          NOT NULL,
	t3ver_stage      INT(11) DEFAULT 0             NOT NULL,
	t3ver_count      INT(11) DEFAULT 0             NOT NULL,
	t3ver_tstamp     INT(11) DEFAULT 0             NOT NULL,
	t3ver_move_id    INT(11) DEFAULT 0             NOT NULL,

	sys_language_uid INT(11) DEFAULT 0             NOT NULL,
	l10n_parent      INT(11) DEFAULT 0             NOT NULL,
	l10n_diffsource  MEDIUMBLOB,
	l10n_state       TEXT,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid),
	KEY language (l10n_parent, sys_language_uid)

);

#
# A lookup table to determine whether a user has already read a certain thread.
#
CREATE TABLE tx_agora_domain_model_user_readthread (
	uid                             INT(11)                       NOT NULL AUTO_INCREMENT,
	pid                             INT(11) DEFAULT 0             NOT NULL,

	uid_local 											int(11) DEFAULT 0		 					NOT NULL,
	uid_foreign											int(11) DEFAULT 0		 					NOT NULL,
	timestamp 											int(11) unsigned DEFAULT 0	 	NOT NULL,
	crdate                          INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	sorting 												int(11) unsigned 							NOT NULL default '0',
	sorting_foreign 								int(11) unsigned 							NOT NULL default '0',
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);


#
# Table structure for table 'tx_agora_domain_model_mod_report'
#
CREATE TABLE tx_agora_domain_model_mod_report (
	uid                             INT(11)                       NOT NULL AUTO_INCREMENT,
	pid                             INT(11) DEFAULT 0             NOT NULL,

	feuser													INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	post			                      INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	type														INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	reporter								        INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	text					                  VARCHAR(2000) DEFAULT ''      NOT NULL,

	tstamp                          INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	crdate                          INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	cruser_id                       INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	deleted                         TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	hidden                          TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL,
	starttime                       INT(11) UNSIGNED DEFAULT 0    NOT NULL,
	endtime                         INT(11) UNSIGNED DEFAULT 0    NOT NULL,

	t3ver_oid                       INT(11) DEFAULT 0             NOT NULL,
	t3ver_id                        INT(11) DEFAULT 0             NOT NULL,
	t3ver_wsid                      INT(11) DEFAULT 0             NOT NULL,
	t3ver_label                     VARCHAR(255) DEFAULT ''       NOT NULL,
	t3ver_state                     TINYINT(4) DEFAULT 0          NOT NULL,
	t3ver_stage                     INT(11) DEFAULT 0             NOT NULL,
	t3ver_count                     INT(11) DEFAULT 0             NOT NULL,
	t3ver_tstamp                    INT(11) DEFAULT 0             NOT NULL,
	t3ver_move_id                   INT(11) DEFAULT 0             NOT NULL,
	sorting                         INT(11) DEFAULT 0             NOT NULL,

	sys_language_uid                INT(11) DEFAULT 0             NOT NULL,
	l10n_parent                     INT(11) DEFAULT 0             NOT NULL,
	l10n_diffsource                 MEDIUMBLOB,
	l10n_state                      TEXT,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid, t3ver_wsid),
	KEY language (l10n_parent, sys_language_uid)

);
