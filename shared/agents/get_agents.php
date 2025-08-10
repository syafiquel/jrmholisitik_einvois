<?php
require_once "../../db_con/connection.php";
require '../../db_con/check_authentication.php';
// Get parameters from DataTables
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Total records
// $totalQuery = $db->query('SELECT COUNT(*) AS total FROM purchase_order');
// $totalRecords = $totalQuery->fetch(PDO::FETCH_ASSOC)['total'];
$sql_count = "SELECT COUNT(*) AS total FROM user WHERE access_level <> 66 AND isDeleted IS NULL";
$result=mysqli_query($db,$sql_count);
$totalRecords=mysqli_fetch_array($result,MYSQLI_ASSOC)['total'];

// Filtered records
if (!empty($searchValue)) {
    // $filteredQuery = $db->prepare();
    // $filteredQuery->execute([':search' => "%$searchValue%"]);
    // $totalFiltered = $filteredQuery->fetch(PDO::FETCH_ASSOC)['total'];
    $sql_count = "$sql_count AND
      ( nama LIKE '%$searchValue%'
        OR email LIKE '%$searchValue%'
        OR no_tel LIKE '%$searchValue%'
      )";
    $result=mysqli_query($db,$sql_count);
    $totalFiltered=mysqli_fetch_array($result,MYSQLI_ASSOC)['total'];


} else {
    $totalFiltered = $totalRecords;
}

// Fetch data with custom SQL style
$sql = !empty($searchValue)
    ? "SELECT id, nama, no_tel, email, created_at, rank_id, credit_limit FROM user
    WHERE access_level <> 66
    AND isDeleted IS NULL
    AND
      ( nama LIKE '%$searchValue%'
        OR email LIKE '%$searchValue%'
        OR no_tel LIKE '%$searchValue%'
      )
    ORDER BY id DESC LIMIT $start, $length"
    :
    "SELECT id, nama, no_tel, email, created_at, rank_id, credit_limit FROM user
    WHERE access_level <> 66
    AND isDeleted IS NULL
    ORDER BY id DESC LIMIT $start, $length";
$result = $db->query($sql);
// $data=$result->fetch_assoc();
while($data[] = mysqli_fetch_assoc($result));
array_pop($data);
$response = [
    'draw' => $draw,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $totalFiltered,
    'data' => $data
];

header('Content-Type: application/json');
echo json_encode($response);;

 ?>
