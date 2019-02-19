var mysql = require('mysql');
var fs = require('fs');

var con = mysql.createConnection({
  host: "localhost",
  user: "root",
  password: "a",
  database: 'pos',
});

//小カテゴリ(揚げ物と中華まん)に属する商品のみ取得。
const sql = `SELECT i.barcode, i.name, i.price, i.discount_amt, i.img_name, s.name AS small_category_name
FROM items AS i
INNER JOIN small_categories AS s ON i.small_category_id = s.id
AND s.id IN (3,4)`;

con.connect(function(err) {
  if (err) throw err;
  console.log("Connected!");
  con.query(sql, function(err, results) {
    if (err) throw err;
    console.log("Result: " + results);
    // const to_obj = results.reduce((acc, result) => {
    //   if(acc.hasOwnProperty(result.sc_name) !== true) {
    //     acc[result.sc_name] = []
    //   }
    //   acc[result.sc_name].push(result)
    //   return acc
    // }, {})
    // var json = JSON.stringify(to_obj);
    var json = JSON.stringify(results);
    fs.writeFile('simple_masters.json', json, 'utf8', (err) => {
      if (err) throw err;
      console.log('Data written to file');
    }, 2);
  });
});
