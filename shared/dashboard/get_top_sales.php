<?php
require_once "../../db_con/connection.php";
require '../../db_con/check_authentication.php';

$range = isset($_GET['range']) ? $_GET['range'] : 'this_month';

$range_clause  = "";

switch ($range) {
  case 'this_month':
    $range_clause  = "AND MONTH(o.paid_at) = MONTH(CURRENT_DATE()) AND YEAR(o.paid_at) = YEAR(CURRENT_DATE()) ";

    break;
  case 'this_year':
    $range_clause  = "AND YEAR(o.paid_at) = YEAR(CURRENT_DATE()) ";

    break;
  case 'last_month':
      $range_clause  = "AND MONTH(o.paid_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(o.paid_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH) ";
    break;

  case 'last_year':
      $range_clause  = "AND YEAR(o.paid_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH) ";
    break;

  case 'all_time':
      $range_clause  = "";
    break;

}



 $sql = "SELECT
    u.nama AS nama,
    r.title AS rank_title,
    SUM(o.all_items_price) AS total_sales
FROM
    user u
JOIN
    purchase_order o
ON
    u.id = o.user_id
JOIN
    rank r
ON
    r.id = u.rank_id
WHERE
    o.paid_at IS NOT NULL
    $range_clause
GROUP BY
    u.id
ORDER BY total_sales DESC
LIMIT 10";

$result=mysqli_query($db,$sql);
while($sales[] = mysqli_fetch_assoc($result));
array_pop($sales);

// while ($row = $result->fetch_assoc()) {
//     $row['total_price'] = number_format($row['total_price'], 2);
//     $row['percentage_difference'] = number_format($row['percentage_difference'], 2);
//     $sales[$row['period']] = $row;
// }
// Return JSON response
header('Content-Type: application/json');
echo json_encode( [
  'top_sales' => $sales
]);

 ?>
