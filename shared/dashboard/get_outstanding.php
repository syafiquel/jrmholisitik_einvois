<?php
require_once "../../db_con/connection.php";
require '../../db_con/check_authentication.php';


 $sql = "SELECT
      u.nama as nama,
      r.title AS rank_title,
      u.credit_used as credit_used,
      u.last_credit_used_at as last_credit_used_at

  FROM credit_terms c
  JOIN
      user u
    ON
      u.id = c.user_id
  JOIN
      rank r
  ON
      r.id = u.rank_id
  WHERE
      c.status = 'outstanding'
  ORDER BY
      c.id DESC
  LIMIT
      10";

 $sql = "SELECT
      u.nama as nama,
      r.title AS rank_title,
      u.credit_used as credit_used,
      u.last_credit_used_at as last_credit_used_at
  FROM user u
  JOIN
      rank r
  ON
      r.id = u.rank_id
  WHERE
      u.credit_used > 0
  ORDER BY
      u.credit_used DESC
  LIMIT
      10";

// echo "$sql";

$result=mysqli_query($db,$sql);
while($data[] = mysqli_fetch_assoc($result));
array_pop($data);

// while ($row = $result->fetch_assoc()) {
//     $row['total_price'] = number_format($row['total_price'], 2);
//     $row['percentage_difference'] = number_format($row['percentage_difference'], 2);
//     $data[$row['period']] = $row;
// }
// Return JSON response
header('Content-Type: application/json');
echo json_encode( $data);

 ?>
