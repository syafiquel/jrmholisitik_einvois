 <?php
 include '../init.php';
 $role[] = "admin";
 // $role[] = "superadmin";
 // $role[] = "user";
 $main_orientation = 'horizontal';

 $user_id = $_SESSION['user_id'];
 $my_name = $_SESSION['nama'];

 require "../db_con/check.php";
 require "../db_con/connection.php";

 //------------------------------------------------------------------------------//
 // include '../shared/notificationCounter.php';
 //------------------------------------------------------------------------------//
 $setting = Credit::getSetting();
 $cut_off = $setting['cut_off_order_time'];
 $todayDateCutOff = date("Y-m-d $cut_off:00");
 $cut_off_ship = $setting['cut_off_ship_out_time'];
 $todayDateCutOffShip = date("Y-m-d $cut_off_ship:00");


$sql = "SELECT 'outstanding_approval' as counter_for, COUNT(*) as total
FROM credit_settlement
WHERE approval_status = 'pending'

UNION ALL

SELECT 'order_approval' as counter_for, COUNT(*) as total
FROM purchase_order po
WHERE po.payment_type = 'Bank Transfer'
AND po.isApproved IS NULL
AND po.paid_at IS NULL
AND po.isCanceled IS NULL

UNION ALL

SELECT 'to_pick_up' as counter_for, COUNT(*) as total
FROM purchase_order po
WHERE po.paid_at IS NOT NULL
AND po.pickup_name IS NOT NULL
AND po.isCompleted IS NULL
AND po.shipping_method = 'Pick Up'

UNION ALL

SELECT 'to_deliver' as counter_for, COUNT(*) as total
FROM purchase_order po
WHERE po.paid_at IS NOT NULL
AND po.shipping_to IS NOT NULL
AND po.isCompleted IS NULL
AND po.isApproved <= '$todayDateCutOff'
AND po.shipping_method = 'Delivery'

UNION ALL

SELECT 'to_ship_out' as counter_for, COUNT(*) as total
FROM purchase_order po
WHERE po.paid_at IS NOT NULL
AND po.shipping_to IS NOT NULL
AND po.isCompleted IS NULL
AND po.isApproved <= '$todayDateCutOffShip'
AND po.shipping_method = 'Shipping'
";


$result=mysqli_query($db,$sql);
// while($sidebar[] = mysqli_fetch_assoc($result));
$totalTodo = 0;
while ($row = $result->fetch_assoc()) {
    $totalTodo += $row['total'];
    $sidebar[$row['counter_for']] = $row['total'];
}
$todoTotalCount = $sidebar['to_pick_up']+$sidebar['to_deliver']+$sidebar['to_ship_out'];
// array_pop($sidebar);

// var_dump($sidebar);
// exit;

  ?>
