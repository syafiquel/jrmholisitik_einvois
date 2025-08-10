<table class="heady">
  <tr>

    <td width="30%" class="">
      <center>
        <img src="https://app.jrmholistikampang.com/assets/logo/logo-offiial.png" style="width: 90%"/>
      </center>
    </td>
    <td width="40%" class="" style="padding-left: 10px;font-size: 15px;text-align: center;;">
      <div class="">
        <b>
          JAMU RATU MALAYA
          <br>STOKIS AMPANG
        </b>
        <br>
        <small>JRM HOLISTIK AMPANG</small>
      </div>
      <div class="">
        <div style="font-size: 12px;margin: 8px 0px;">
          kiinoonessdnbhd@gmail.com
        </div>
         PUBLIC BANK KIINO ONE SDN BHD (3206592214 )
      </div>
    </td>
    <td width="30%" style="font-size: 15px;text-align: right;;">
      <b class="" style="font-size: 18px;margin-bottom: 5px">
        DELIVERY ORDER
      </b>
      <br>
      <br>
      <b>REF#: <?php echo $order['id'] ?></b>
      <br>
      Invoice Date : <?php echo date('d/m/Y', strtotime($order['paid_at'])); ?>
      <br>
      Invoice Status : <?php echo $order['paid_at'] ? 'PAID' : 'PENDING' ?>
      <br>
      Delivered Date: <?php echo date('d/m/Y', strtotime($order['paid_at'])); ?>
      <br>

    </td>
  </tr>

</table>
