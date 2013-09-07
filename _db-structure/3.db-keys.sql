ALTER TABLE `base09_persons` ADD INDEX `second_name_k` (`second_name`);
ALTER TABLE `base09_persons` ADD INDEX `first_name_k` (`first_name`);
ALTER TABLE `base09_persons` ADD INDEX `middle_name_k` (`middle_name`);
ALTER TABLE `base09_persons` ADD INDEX `room_num_k` (`room_num`);
ALTER TABLE `base09_persons` ADD INDEX `room_letter_k` (`room_letter`);

ALTER TABLE `base09_streets` ADD INDEX `street_k` (`name`);

ALTER TABLE `base09_houses` ADD INDEX `house_num_k` (`num`);
ALTER TABLE `base09_houses` ADD INDEX `house_block_k` (`block`);