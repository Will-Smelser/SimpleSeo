ALTER TABLE  `AuthAssignment` CHANGE  `userid`  `userid` INT NOT NULL;
ALTER TABLE  `AuthAssignment` ADD FOREIGN KEY (  `userid` ) REFERENCES  `simpleseoapi`.`Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
#ALTER TABLE  `user_oauth` ADD FOREIGN KEY (  `user_id` ) REFERENCES  `simpleseoapi`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;


CREATE DEFINER =  `root`@`localhost` EVENT `clean_tokens` ON SCHEDULE EVERY1 MINUTE STARTS '2013-09-22 21:29:31' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM simpleseoapi.tokens WHERE expire < UNIX_TIMESTAMP( )