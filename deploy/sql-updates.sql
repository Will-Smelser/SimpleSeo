ALTER TABLE  `AuthAssignment` CHANGE  `userid`  `userid` INT NOT NULL;
ALTER TABLE  `AuthAssignment` ADD FOREIGN KEY (  `userid` ) REFERENCES  `simpleseoapi`.`Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
#ALTER TABLE  `user_oauth` ADD FOREIGN KEY (  `user_id` ) REFERENCES  `simpleseoapi`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;