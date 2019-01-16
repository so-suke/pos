SELECT items.name, DATE_FORMAT(saled_at, '%Y-%m-%d') AS time, SUM(num) AS sum_num, SUM(num) * items.price AS sum_money
FROM sales
JOIN (
	SELECT *
	FROM items
	WHERE small_category_id=1
) AS items
ON sales.barcode = items.barcode
WHERE (DATE_FORMAT(saled_at, '%Y-%m-%d') BETWEEN '2018-11-29' AND '2018-12-01')
OR DATE_FORMAT(saled_at, '%Y-%m-%d') = '2019-01-17'
GROUP BY DATE_FORMAT(saled_at, '%Y-%m-%d'), items.name, items.price