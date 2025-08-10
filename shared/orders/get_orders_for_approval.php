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
$pending = isset($_GET['pending']) ? $_GET['pending'] : '';
$rejectClause = " AND po.isCanceled IS NULL";

if (isset($_GET['type'])) {
  if ($_GET['type'] == 'pickup') {
    $typeClause = " AND po.pickup_name IS NOT NULL";
  }
  if ($_GET['type'] == 'to_pickup') {
    $typeClause = " AND po.pickup_name IS NOT NULL AND isCompleted IS NULL";
  }
}

if (isset($_GET['status'])) {
  $statusClause = " AND po.paid_at IS NOT NULL";
}

if (isset($_GET['pending'])) {
  $rejectClause = " AND po.isCanceled IS NOT NULL";
}

// Total records
// $totalQuery = $db->query('SELECT COUNT(*) AS total FROM purchase_order');
// $totalRecords = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];

$sqlBase = "SELECT COUNT(*) AS total FROM purchase_order po WHERE 1=1 AND po.payment_type = 'Bank Transfer'
AND po.isApproved IS NULL
AND po.paid_at IS NULL
$rejectClause
$statusClause
$typeClause";
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
    ? "SELECT *, po.id as id, IF(po.paid_at IS NULL,0,1) as order_status, po.paid_at as paid_at
    FROM purchase_order po
    LEFT JOIN user usr ON po.user_id = usr.id
    WHERE (
      po.shipping_to LIKE '%$searchValue%'
      OR po.pickup_name LIKE '%$searchValue%'
    )
    AND po.payment_type = 'Bank Transfer'
    AND po.isApproved IS NULL
    AND po.paid_at IS NULL
    $rejectClause
    $statusClause
    $typeClause
    ORDER BY po.id DESC LIMIT $start, $length"
    :
    "SELECT *, po.id as id, IF(po.paid_at IS NULL,0,1) as order_status, po.paid_at as paid_at
    FROM purchase_order po
    LEFT JOIN user usr ON po.user_id = usr.id
    WHERE 1=1
    AND po.payment_type = 'Bank Transfer'
    AND po.isApproved IS NULL
    AND po.paid_at IS NULL
    $rejectClause
    $statusClause
    $typeClause
    ORDER BY po.id DESC LIMIT $start, $length";


$result = $db->query($sql);
// $data=$result->fetch_assoc();
while($data[] = mysqli_fetch_assoc($result));
array_pop($data);
$response = [
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalFiltered,
    'data' => $data,
];

header('Content-Type: application/json');
echo json_encode($response);;

 ?>
