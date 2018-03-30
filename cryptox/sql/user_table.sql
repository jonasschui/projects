
CREATE TABLE threads (thread_id int NOT NULL AUTO_INCREMENT,
				thread_title varchar(100),
				thread_content varchar(144),
				currency_id varchar(10) NOT NULL,
				user_id int NOT NULL,
				thread_date datetime NOT NULL,
				FOREIGN KEY (currency_id) REFERENCES currencies(currency_id),
				FOREIGN KEY (user_id) REFERENCES Users(user_id),
				PRIMARY KEY (thread_id));

CREATE TABLE replies (reply_id int NOT NULL AUTO_INCREMENT,
				reply varchar(144) NOT NULL,
				reply_date datetime NOT NULL,
				thread_id int NOT NULL,
				user_id int NOT NULL,
				FOREIGN KEY (thread_id) REFERENCES threads(thread_id),
				FOREIGN KEY (user_id) REFERENCES Users(user_id),
				PRIMARY KEY (reply_id));
	
CREATE TABLE Users (user_id int NOT NULL AUTO_INCREMENT,
				user_name varchar(220) NOT NULL,
				password_hash char(60) NOT NULL,
				first_name varchar(220),
				last_name varchar(220),
				email varchar(320),
				bio varchar (200),
				PRIMARY KEY (user_id));
					
CREATE TABLE currencies (currency_id varchar(10) NOT NULL,
				currency_name varchar(220) NOT NULL,
				currency_url varchar(512),
				currency_description varchar(250),
				PRIMARY KEY (currency_id));

CREATE TABLE posts (post_id int NOT NULL AUTO_INCREMENT,
				poster_id int NOT NULL,
				post_date datetime NOT NULL,
				post_title varchar(220),
				post_content text NOT NULL,
				FOREIGN KEY (poster_id) REFERENCES Users(user_id),
				PRIMARY KEY (post_id));

CREATE TABLE announcements (announcement_id int NOT NULL AUTO_INCREMENT,
				announcer_id varchar(10) NOT NULL,
				announcement_date datetime NOT NULL,
				announcement_title varchar(220),
				announcement_content text NOT NULL,
				FOREIGN KEY (announcer_id) REFERENCES currencies(currency_id),
				PRIMARY KEY (announcement_id));

CREATE TABLE relationships (user_one_id int  NOT NULL,
				user_two_id int NOT NULL,
				status tinyint(3)  NOT NULL DEFAULT '0',
				action_user_id int  NOT NULL,
				FOREIGN KEY (user_one_id) REFERENCES Users(user_id),
				FOREIGN KEY (user_two_id) REFERENCES Users(user_id),
				FOREIGN KEY (action_user_id) REFERENCES Users(user_id));

ALTER TABLE relationships
				ADD UNIQUE KEY unique_users_id (user_one_id,user_two_id);


