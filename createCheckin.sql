create database checkin;

use checkin;

SET FOREIGN_KEY_CHECKS=0;

create table user (
id int NOT NULL AUTO_INCREMENT,
username varchar(12) NOT NULL,
password varchar(30) NOT NULL,
email varchar(50) NOT NULL,
primary key(id)
);

create table location (
id int NOT NULL AUTO_INCREMENT,
location varchar(100),
primary key(id),
    foreign key(id)
references user(id)
on delete cascade
on update cascade
);

create table social (
id int NOT NULL AUTO_INCREMENT,
friends varchar(1000),
news_sources varchar(1000),
primary key(id),
    foreign key(id)
references user(id)
on delete cascade
on update cascade
);

create table status (
id int NOT NULL AUTO_INCREMENT,
in_event int NOT NULL,
check_in_status int,
primary key(id),
    foreign key(id)
references user(id)
on delete cascade
on update cascade
);

create table tweet (
id int not null auto_increment,
location varchar(1000) ,
string varchar(1000),
hashtag varchar(1000),
primary key(id),
    foreign key(id)
references user(id)
on delete cascade
on update cascade
);
