GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root' WITH GRANT OPTION;
FLUSH PRIVILEGES;

CREATE DATABASE Karma8;

CREATE TABLE Karma8.Users (
	id bigint not null primary key auto_increment,
	username varchar(255) not null,
	email varchar(255) not null ,
	validts date not null default '1000-01-01 00:00:00',
	confirmed int(1) unsigned not null default 0,
	checked int(1) unsigned not null default 0,
	valid int(1) unsigned not null default 0,
	reminder_sent_at datetime not null default '1000-01-01 00:00:00',
	UNIQUE INDEX email_uq (email),
	INDEX validts_idx (validts)
);

CREATE TABLE Karma8.ReminderQueue (
    id bigint not null primary key auto_increment,
    user_id bigint not null,
    errcnt int(2) unsigned not null default 0,
    next_processing_time datetime not null default CURRENT_TIMESTAMP,
    created_at datetime not null default CURRENT_TIMESTAMP
);


