<table class="heady">
  <tr>

    <td width="30%" class="">
      <center>
        <img src="https://app.jrmholistikampang.com/assets/logo/logo-offiial.png" style="width: 90%"/>
      </center>
    </td>
    <td width="70%" style="font-size: 15px;text-align: right;;">
      <b class="" style="font-size: 18px;margin-bottom: 5px">
        DELIVERY ORDER
      </b>
      <br>
      <br>
      <b>REF#: <?php echo $order['id'] ?></b>
      <br>
      Invoice Date : <?php echo date('d/m/Y', strtotime($order['paid_at'])); ?>
      <br>
      Invoice Status :
      <?php if (trim($order['outstanding_status']) == 'outstanding'): ?>
        PAYMENT PENDING
      <?php else: ?>
        <?php echo $order['paid_at'] ? 'PAID' : 'PENDING' ?>
      <?php endif; ?>
      <br>

    </td>
  </tr>

</table>
