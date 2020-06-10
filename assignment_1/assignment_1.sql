--assignment 1 question 1 solution
SELECT a.sku, a.description, a.quantity AS quantity_1, b.quantity AS quantity_2
FROM items AS a
INNER JOIN items AS b
ON a.sku = b.sku AND b.`warehouse` = 'Warehouse 2'
WHERE a.`warehouse` = 'Warehouse 1';


--assignment 1 question 2 solution
SELECT *
FROM items
GROUP BY sku
HAVING COUNT(*) = 1;


--assignment 1 question 3 solution
CREATE TABLE `items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse` varchar(100) NOT NULL DEFAULT '',
  `sku` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
);
-- add indexes on columns
ALTER TABLE items ADD INDEX (warehouse);
ALTER TABLE items ADD INDEX (sku);



