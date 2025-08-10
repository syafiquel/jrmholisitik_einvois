<?php
require_once "../db_con/connection.php";
require '../db_con/check_authentication.php';

$ids = $_GET['ids'];

if (!$ids) {
  echo "No Selected Id";
  exit;
}
$sql = "SELECT
po.id as id,
po.total_qty as total_qty,
po.remark as remark,
COALESCE (po.shipping_to, po.pickup_name)  as nama,
COALESCE (po.shipping_contact, po.pickup_phone)  as phone,
IF(po.pickup_name IS NULL,'Delivery','Pickup') as order_type,
IF(po.paid_at IS NULL,0,1) as order_status,
po.paid_at as paid_at,
pr.name as product_name,
op.quantity as product_qty,
op.total_price as product_total
    FROM purchase_order po
    LEFT JOIN user usr ON po.user_id = usr.id
    LEFT JOIN order_details op ON po.id = op.order_id
    LEFT JOIN product pr ON op.product_id = pr.id
    WHERE po.id in ({$ids})
    ORDER BY po.id DESC";

$result = $db->query($sql);
// $data=$result->fetch_assoc();
while ($row = $result->fetch_assoc()) {
  $data[$row['id']]['details'] = $row;
  $data[$row['id']]['products'][] = [
    'title' => $row['product_name'],
    'qty' => $row['product_qty'],
    'total' => $row['product_total']
  ];
}

// var_dump($order);
// exit;
// while($data[] = mysqli_fetch_assoc($result));
// array_pop($data);

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="do_assets/do.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css">
    <style media="screen, print">
      body { background: #bc7bd1 }
      #table-product{
        width: 100%;
        border: 1px solid black;
      }
      #table-product td, #table-product th{
        border-top: 1px solid black;
        border-bottom: 1px solid black;
        border-right: 1px solid black;
        padding: 5px;
        font-size: 13px;
      }

    </style>
  <!-- Set page size here: A5, A4 or A3 -->

  <!-- Set also "landscape" if you need -->

  </head>

   <?php
   foreach ($data as $key => $orderFull) {
     $order = $orderFull['details'];
     $products = $orderFull['products'];
     ?>
     <body class="A4 center">


    <!-- Each sheet element should have the class "sheet" -->
    <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
    <section class="sheet padding-10mm center">

      <!-- Write HTML just like a web page -->
      <article>

        <div>

          <?php include '_do_header.php'; ?>
          <hr class="" style="background: black;">

          <section>
            <b>Customer:</b> <?php echo $order['order_type'] ?>
            <br>
            <b>Name:</b>  <?php echo $order['nama'] ?><br>
            <b>Phone:</b>  <?php echo $order['phone'] ?><br>

            <br>

            <table id="table-product">
              <tr>
                <th style="width: 10%;text-align: center;">No</th>
                <th style="width: 70%;text-align: center;" >Product</th>
                <th style="width: 10%;text-align: center;">Unit</th>
                <th style="width: 10%;text-align: center;">Check</th>
              </tr>
              <?php
              $i=1;
               foreach ($products as $key => $product) {

                 ?>
                 <tr>
                   <td style="text-align: center;"><?php echo $i++; ?></td>
                   <td><?php echo $product['title'] ?></td>
                   <td  style="text-align: right;"><?php echo $product['qty'] ?></td>
                   <td></td>
                 </tr>
                 <?php
               }
               ?>
             <tr>
               <td colspan="2" style="text-align: right;">
                 Total
               </td>
               <td  style="text-align: right;"><?php echo $order['total_qty'] ?></td>
               <td></td>
             </tr>
            </table>

            <br>

            <b>Remarks:</b> <?php echo $order['remark']??'-' ?>

          </section>

        </div>


      </article>

    </section>

  </body>
     <?php
   }
   ?>

</html>
