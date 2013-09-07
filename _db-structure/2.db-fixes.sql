UPDATE `base09_locations` SET `name` = 'Вишневое' WHERE `name` = 'Вишнево' AND `city_id` = 1 LIMIT 1;

UPDATE `base09_streets` SET `type`='ул.', `name` = 'Радянская' WHERE `name` = "Радянская ул.-Жуляны";

UPDATE `base09_houses` SET `block` = '19' WHERE `block` = '19-' LIMIT 1;

UPDATE `base09_persons` SET `house_id` = 
	(SELECT `id` FROM `base09_houses` WHERE `num` = '14' AND 'block' = '' AND `street_id` = 
		(SELECT `street_id` FROM `base09_houses` WHERE `num` = '461587' LIMIT 0, 1)
	LIMIT 0, 1)
WHERE `house_id` = (SELECT `id` FROM `base09_houses` WHERE `num` = '461587');
DELETE FROM `base09_houses` WHERE `num` = '461587' LIMIT 1;



