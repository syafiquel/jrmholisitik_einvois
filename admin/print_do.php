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
po.shipping_method as shipping_method,
COALESCE (po.shipping_to, po.pickup_name)  as nama,
COALESCE (po.shipping_contact, po.pickup_phone)  as phone,
IF(po.pickup_name IS NULL,'Delivery','Pickup') as order_type,
IF(po.paid_at IS NULL,0,1) as order_status,
po.paid_at as paid_at,
po.isCompleted as isCompleted,
pr.name as product_name,
op.quantity as product_qty,
op.total_price as product_total,
ct.status as outstanding_status
    FROM purchase_order po
    LEFT JOIN user usr ON po.user_id = usr.id
    LEFT JOIN order_details op ON po.id = op.order_id
    LEFT JOIN product pr ON op.product_id = pr.id
    LEFT JOIN credit_terms ct ON ct.order_id = po.id
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
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" /> -->
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="do_assets/do.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css">
    <style media="screen, print">
    @page { margin: 0 }
    body {
      margin: 0;
      font-family: Helvetica;
      background-color: black
    }
    .sheet {
      margin: 0;
      overflow: hidden;
      position: relative;
      box-sizing: border-box;
      page-break-after: always;
    }

    /** Paper sizes **/
    body.A3           .sheet { width: 297mm; height: 419mm }
    body.A3.landscape .sheet { width: 420mm; height: 296mm }
    body.A4           .sheet { width: 210mm; height: 296mm }
    body.A4.landscape .sheet { width: 297mm; height: 209mm }
    body.A5           .sheet { width: 148mm; height: 209mm }
    body.A5.landscape .sheet { width: 210mm; height: 147mm }
    body.Thermal      .sheet { width: auto; height: auto }

    /** Padding area **/
    .sheet.padding-10mm { padding: 10mm }
    .sheet.padding-15mm { padding: 15mm }
    .sheet.padding-20mm { padding: 20mm }
    .sheet.padding-25mm { padding: 25mm }

    /** For screen preview **/
    @media screen {
      body { background: #e0e0e0 }
      .sheet {
        background: white;
        box-shadow: 0 .5mm 2mm rgba(0,0,0,.3);
        margin: 0;
        padding: 3mm;
      }
    }

    /** Fix for Chrome issue #273306 **/
    @media print {
               body.A3.landscape { width: 420mm }
      body.A3, body.A4.landscape { width: 297mm }
      body.A4, body.A5.landscape { width: 210mm }
      body.A5                    { width: 148mm }
      body.Thermal      .sheet { width: 100mm; height: auto }
      .sheet {
        padding: 3mm;
      }
      body {
        zoom: 1; /* Adjust zoom level to fit content */
        margin: auto; /* Center the content */
        display: block;
        background: white; /* Optional: Set background color */
      }
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box; /* Include padding and border in width/height calculations */
      }
    }
      @page {
        size: A4
      }

      .center {
        margin-left: auto;
        margin-right: auto;
      }



      .dlm {
        border: 1px #464646;
        border-style: solid;
        padding: 5px;
      }

      .heady {
        width:100%;
      }

      .head2 {
        font-size:14px;
      }

      .text {
        text-align:center;
      }

      .info {
        font-size: 10px;
        font-style: italic;
        margin-top: 10px;
        color: #585656;
      }

      .data {
        padding: 5px;
      }



      .buton {
        border-radius: 50px;
        font-size: 0.8rem;
        padding: 5px;
        cursor: pointer;
      }

      .nama-pills{
        border: 1px solid #e6b0b0;
        padding: 5px 0px;
        margin: 2px 5px
      }
      .padding-5mm{
        padding: 10mm;
      }

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
        font-size: 16px;
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
     <body class="Thermal " style="max-width: 80mm;">


    <!-- Each sheet element should have the class "sheet" -->
    <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
    <section class="sheet ">

      <!-- Write HTML just like a web page -->
      <article>

        <div>

          <?php include '_do_header.php'; ?>
          <hr class="" style="background: black;">

          <section>
            <b>Customer:</b> <?php echo $order['shipping_method'] ?>
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
