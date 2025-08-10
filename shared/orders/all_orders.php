<?php
require_once "../../db_con/connection.php";
require '../../db_con/check_authentication.php';
// Get parameters from DataTables


$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

$status = isset($_GET['status']) ? $_GET['status'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$cut_off = isset($_GET['cut_off']) ? $_GET['cut_off'] : 9;

$columnIndex = $_POST['order']['0']['column'];
$columnName = $_POST["columns"][$columnIndex]["name"] ?? 'po.isApproved';
$sortDir = strtoupper($_POST['order']['0']['dir']) ?? 'DESC';

if (isset($_GET['status'])) {
  $typeClause = " AND po.isCompleted IS NOT NULL";
}
$statusClause = " AND po.paid_at IS NOT NULL";

if (isset($_GET['type'])) {
  if ($_GET['type'] == 'pickup') {
    $typeClause = " AND po.pickup_name IS NOT NULL";
  }
  if ($_GET['type'] == 'to_pickup') {
    $typeClause = " AND po.shipping_method = 'Pick Up' AND po.pickup_name IS NOT NULL AND po.isCompleted IS NULL";
  }
  if ($_GET['type'] == 'to_deliver') {
    $cut_off = str_replace(".", ":", $cut_off);
    $todayDateCutOff = date("Y-m-d $cut_off:00");
    $typeClause = " AND po.shipping_method = 'Delivery' AND po.shipping_to IS NOT NULL AND po.isCompleted IS NULL AND po.isApproved <= '$todayDateCutOff'";
  }
  if ($_GET['type'] == 'to_ship_out') {
    $cut_off = str_replace(".", ":", $cut_off);
    $todayDateCutOff = date("Y-m-d $cut_off:00");
    $typeClause = " AND po.shipping_method = 'Shipping' AND po.shipping_to IS NOT NULL AND po.isCompleted IS NULL  AND po.isApproved <= '$todayDateCutOff'";
  }
}


// Total records
// $totalQuery = $db->query('SELECT COUNT(*) AS total FROM purchase_order');
// $totalRecords = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];

$sqlBase = "SELECT COUNT(*) AS total FROM purchase_order po WHERE 1=1 $statusClause $typeClause";
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
    po.payment_type as payment_type,
    ct.status as credit_status
    FROM purchase_order po
    LEFT JOIN user usr ON po.user_id = usr.id
    LEFT JOIN credit_terms ct on ct.order_id = po.id
    WHERE (
      po.shipping_to LIKE '%$searchValue%'
      OR po.pickup_name LIKE '%$searchValue%'
    )
    $statusClause
    $typeClause
    ORDER BY $columnName $sortDir LIMIT $start, $length"
    :
    "SELECT *, po.id as id, IF(po.paid_at IS NULL,0,1) as order_status,
    IF(po.pickup_name IS NULL,'Delivery','Pickup') as order_type,
    IF(po.pickup_name IS NOT NULL,po.pickup_name,po.shipping_to) as send_to,
    IF(po.pickup_datetime IS NOT NULL,po.pickup_datetime,po.paid_at) as send_at,
    po.paid_at as paid_at,
    po.payment_type as payment_type,
    ct.status as credit_status
    FROM purchase_order po
    LEFT JOIN user usr ON po.user_id = usr.id
    LEFT JOIN credit_terms ct on ct.order_id = po.id
    WHERE 1=1
    $statusClause
    $typeClause
    ORDER BY $columnName $sortDir LIMIT $start, $length";
// echo $sql ;
$result = $db->query($sql);
// $data=$result->fetch_assoc();
while($data[] = mysqli_fetch_assoc($result));
array_pop($data);
$response = [
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalFiltered,
    'data' => $data,
    'sql' => $sql,
    'sort' => $columnIndex."|".$columnName."|".$sortDir,
];

header('Content-Type: application/json');
echo json_encode($response);;

 ?>
