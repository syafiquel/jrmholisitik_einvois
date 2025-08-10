<?php
require_once "../../db_con/connection.php";
require '../../db_con/check_authentication.php';
// Get parameters from DataTables
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

$whereArr = [];

$date_start = date('Y-m-1');
$date_end = date('Y-m-d');

if ( $_GET['date_start']) {
  $date_start = $_GET['date_start'];
}
$whereArr[] = "AND po.paid_at > '{$date_start} 00:00:00'";
if ( $_GET['date_end']) {
  $date_end = $_GET['date_end'];
}
$whereArr[] = " po.paid_at < '{$date_end} 23:59:59'";
if (isset($_GET['agent_id']) && $_GET['agent_id']) {
  $whereArr[] = " po.user_id = '{$_GET['agent_id']}'";
}

if (count($whereArr)) {
  $whereCluase = implode($whereArr, ' AND ');
}


// Total records
// $totalQuery = $db->query('SELECT COUNT(*) AS total FROM purchase_order');
// $totalRecords = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];

$sqlBase = "SELECT COUNT(*) AS total FROM purchase_order po WHERE po.paid_at IS NOT NULL $whereCluase";
$result=mysqli_query($db,$sqlBase);
$totalRecords=mysqli_fetch_array($result,MYSQLI_ASSOC)['total'];

// Filtered records
if (!empty($searchValue)) {

    $result=mysqli_query($db,"$sqlBase WHERE po.shipping_to LIKE :search OR po.pickup_name LIKE %$searchValue%");
    $totalFiltered=mysqli_fetch_array($result,MYSQLI_ASSOC)['total'];

} else {
    $totalFiltered = $totalRecords;
}

// Fetch data with custom SQL style
$sql = !empty($searchValue)
    ? "SELECT *, po.id as id, IF(po.paid_at IS NULL,0,1) as order_status,
    IF(po.pickup_name IS NULL,'Delivery','Pickup') as order_type,
    IF(po.pickup_name IS NULL,po.pickup_name,po.shipping_to) as send_to,
    IF(po.pickup_datetime IS NULL,po.pickup_datetime,po.paid_at) as send_at,
    po.paid_at as paid_at,
    po.payment_type as payment_type
    FROM purchase_order po
    LEFT JOIN user usr ON po.user_id = usr.id
    LEFT JOIN credit_terms ct on ct.order_id = po.id
    WHERE (
      po.shipping_to LIKE '%$searchValue%'
      OR po.pickup_name LIKE '%$searchValue%'
    )
    $statusClause
    $typeClause
    ORDER BY po.isApproved DESC LIMIT $start, $length"
    :
    "SELECT usr.nama, po.all_items_price, po.id as id, IF(po.paid_at IS NULL,0,1) as order_status,
    IF(po.pickup_name IS NULL,'Delivery','Pickup') as order_type,
    IF(po.pickup_name IS NOT NULL,po.pickup_name,po.shipping_to) as send_to,
    IF(po.pickup_datetime IS NOT NULL,po.pickup_datetime,po.paid_at) as send_at,
    po.paid_at as paid_at,
    po.payment_type as payment_type
    FROM purchase_order po
    LEFT JOIN user usr ON po.user_id = usr.id
    WHERE po.paid_at IS NOT NULL $whereCluase
    ORDER BY po.isApproved DESC LIMIT $start, $length";
// echo $sql ;
$result = $db->query($sql);
// $data=$result->fetch_assoc();
while($data[] = mysqli_fetch_assoc($result));
array_pop($data);

$sqlx = "SELECT SUM(po.all_items_price) AS order_total
        FROM purchase_order po
        LEFT JOIN user usr ON po.user_id = usr.id
        WHERE po.paid_at IS NOT NULL $whereCluase";

$result = $db->query($sqlx);
$dataX = $result->fetch_assoc(); // Fetch only one row since SUM() returns a single value

$order_total = number_format($dataX['order_total'], 2) ?? 0; // Default to 0 if no result


$response = [
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalFiltered,
    'data' => $data,
    'sql' => $sql,
    'order_total' => $order_total
];

header('Content-Type: application/json');
echo json_encode($response);

 ?>
