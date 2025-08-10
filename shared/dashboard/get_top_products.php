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
    p.name AS name,
    p.product_img AS image,
    p.product_img AS image,
    MAX(od.rank_price) AS max_price,
    MIN(od.rank_price) AS min_price,
    SUM(od.total_price) AS total_sales,
    SUM(od.quantity) AS total_qty,
    COUNT(od.id) AS total_quantity_sold
FROM
    purchase_order o
JOIN
    order_details od ON o.id = od.order_id
JOIN
    product p ON od.product_id = p.id
WHERE
    o.paid_at IS NOT NULL
    $range_clause
GROUP BY
    od.product_id
ORDER BY
    total_sales DESC
LIMIT 10";
// echo $sql;exit;
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
  'top_products' => $sales
]);

 ?>
