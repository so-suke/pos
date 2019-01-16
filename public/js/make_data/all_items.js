var mysql = require('mysql');
var fs = require('fs');

var con = mysql.createConnection({
  host: "localhost",
  user: "root",
  password: "a",
  database: 'pos',
});

const sql = `SELECT i.barcode, i.name, i.price, i.discount_amt, s.name AS small_category_name
FROM items AS i
INNER JOIN small_categories AS s ON i.small_category_id = s.id`;

con.connect(function(err) {
  if (err) throw err;
  console.log("Connected!");
  con.query(sql, function(err, results) {
    if (err) throw err;
    console.log("Result: " + results);
    const to_obj = results.reduce((acc, result) => {
      acc[result.barcode] = result
      return acc
    }, {});
    var json = JSON.stringify(to_obj);
    fs.writeFile('item_masters.json', json, 'utf8', (err) => {
      if (err) throw err;
      console.log('Data written to file');
    }, 2);
  });
});
