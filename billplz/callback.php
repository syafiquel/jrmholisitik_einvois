<?php

require_once 'billplz.php';
require_once 'configuration.php';
// include 'mail.php';
require "app/db_con/connection.php";


// define('DB_SERVER', 'localhost');
// define('DB_USERNAME', 'gajimasy_v2');
// define('DB_PASSWORD', '[^1a?^t=(9kl');
// define('DB_DATABASE', 'gajimasy_v2');
// $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);

/*
 * Get Data. Die if input is tempered or X Signature not enabled
 */
$data = billplz::getCallbackData($x_signature);
$tranID = $data['id'];

$billplz = new billplz;
$moreData = $billplz->check_bill($api_key, $tranID);

/*
 * Dalam variable $moreData ada maklumat berikut (array):
 * 1. reference_1
 * 2. reference_1_label
 * 3. reference_2
 * 4. reference_2_label
 * 5. amount
 * 6. description
 * 7. id // bill_id
 * 8. name
 * 9. email
 * 10. paid
 * 11. collection_id
 * 12. due_at
 * 13. mobile
 * 14. url
 * 15. callback_url
 * 16. redirect_url
 *
 * Contoh untuk akses data email: $moreData['email'];
 *
 * Dalam variable $data ada maklumat berikut (array):
 * 1. x_signature
 * 2. id // bill_id
 * 3. paid
 * 4. paid_at
 * 5. amount
 * 6. collection_id
 * 7. due_at
 * 8. email
 * 9. mobile
 * 10. name
 * 11. paid_at
 * 12. state
 * 13. url
 *
 * Contoh untuk ases data bill_id: $data['id']
 *
 */

/*
 * Jika bayaran telah dibuat
 */
if ($moreData['paid']) {
    $bill_id = $moreData['id'];
    $amount = $moreData['amount'] / 100;
    $username = strtolower(trim($moreData['name']));
    $desc = $moreData['description'];
    // error_log("success payment has been recorded $username $bill_id $desc $email");
    // $sql =  "UPDATE wp_members_idp SET status = 'Selesai Bayaran', bill_id = '$bill_id', bayaran = '$amount' WHERE username = '$username'";
    // mysqli_query($db, $sql);

    if ($desc == 'Sim Card Purchase') {
      $order_id = $moreData['reference_1'];
    }



    $sql =  "UPDATE purchase_order SET payment_type = 'Billplz Inside' WHERE id = '$order_id'";
    mysqli_query($db, $sql);

    // $sql =  "UPDATE wp_members_idp SET status = 'Selesai Bayaran', bill_id = '$bill_id', bayaran = '$amount' WHERE username = '$username'";
    // mysqli_query($db, $sql);

    if($desc == 'Web Replika'){

        $sql = mysqli_query($db,"SELECT * FROM wp_members_idp WHERE username = '$username'");
        $row = mysqli_fetch_array($sql,MYSQLI_ASSOC);
        $nama = $row["nama"];
        $no_tel = $row["no_tel"];
        $no_kp = $row["no_kp"];
        $email = $row["email"];
        $img_url = $row["img_url"];
        $no_idp = $row["no_idp"];
        $status = $row["status"];
        $username = $row["username"];
        $to = $email;
        $email_subject = "WEBSITE REPLIKA MMGUIA";
        $message = '<html><body align="center">';
        $message .= '<h2 style="text-align: center; "><font face="Verdana" color="#0000ff"><b>Assalamualaikum dan Salam Hormat, <br>Tahniah ' . strip_tags($nama) . '!!!</b></font></h2>
        <h4 style="text-align: center; "><b><font face="Helvetica">Anda telah berjaya memiliki sebuah laman web istimewa dari MMGUIA. <br> Gunakan webreplika ini sebaik mungkin untuk menambah rakan niaga mahupun kembangkan jaringan bisnes anda. Berikut adalah link Web Replika anda.</font><br>
        <span style="text-align: center; "><a href="http://www.gajimasyuk.com/user/'.$username.'">http://www.gajimasyuk.com/user/'.$username.'</a></span><br>
        <font face="Helvetica" color="#ff0000">SEMOGA BERJAYA!</font></b></h4><br><br>
        <span style="text-align: left; color: #ff0000;"  >This is a system generated email. Please do not reply to it. If you want to contact us, please reply to:</span>
        <span style="text-align: left; " >support@mmguia.com </span><br><br>';
        $message .= "</body></html>";
        smtpmailer($to, $nama_prospek , 'info@gajimasyuk.com', 'Gajimasyuk', $email_subject, $message);

        $email_subject = "WEBSITE REPLIKA MMGUIA";
        $message = '<html><body align="center">';
        $message .= '<table bgcolor="#FFFFFF" border="0" cellpadding="5" cellspacing="0" width="100%">
        	<tbody>
        		<tr bgcolor="#EAF2FA">
        			<td colspan="2"><font style="font-family: sans-serif; font-size: 12px"><strong>Nama</strong></font></td>
        		</tr>
        		<tr bgcolor="#FFFFFF">
        			<td width="20">&nbsp;</td>
        			<td><font style="font-family: sans-serif; font-size: 12px">'.$nama.'</font></td>
        		</tr>
        		<tr bgcolor="#EAF2FA">
        			<td colspan="2"><font style="font-family: sans-serif; font-size: 12px"><strong>No Telefon</strong></font></td>
        		</tr>
        		<tr bgcolor="#FFFFFF">
        			<td width="20">&nbsp;</td>
        			<td><font style="font-family: sans-serif; font-size: 12px">'.$no_tel.'</font></td>
        		</tr>
        		<tr bgcolor="#EAF2FA">
        			<td colspan="2"><font style="font-family: sans-serif; font-size: 12px"><strong>Email</strong></font></td>
        		</tr>
        		<tr bgcolor="#FFFFFF">
        			<td width="20">&nbsp;</td>
        			<td><font style="font-family: sans-serif; font-size: 12px">'.$email.'</font></td>
        		</tr>
        		<tr bgcolor="#EAF2FA">
        			<td colspan="2"><font style="font-family: sans-serif; font-size: 12px"><strong>Username</strong></font></td>
        		</tr>
        		<tr bgcolor="#FFFFFF">
        			<td width="20">&nbsp;</td>
        			<td><font style="font-family: sans-serif; font-size: 12px">'.$username.'</font></td>
        		</tr>
        		<tr bgcolor="#EAF2FA">
        			<td colspan="2"><font style="font-family: sans-serif; font-size: 12px"><strong>Gambar</strong></font></td>
        		</tr>
        		<tr bgcolor="#FFFFFF">
        			<td width="20">&nbsp;</td>
        			<td>  <a href="v2.gajimasyuk.com/images/uploads/'.$img_url.'" target="_blank"><button type="button" class="btn btn-sm" id="btn-sm-padding" name="button">Gambar Pengguna</button></a></td>
        		</tr>
        		<tr bgcolor="#EAF2FA">
        			<td colspan="2"><font style="font-family: sans-serif; font-size: 12px"><strong>Order</strong></font></td>
        		</tr>
        		<tr bgcolor="#FFFFFF">
        			<td width="20">&nbsp;</td>
        			<td>
        				<table cellspacing="0" style="border-left: 1px solid #DFDFDF; border-top: 1px solid #DFDFDF" width="97%">
        					<thead>
        						<tr>
        							<th style="background-color: #F4F4F4; border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; font-family: sans-serif; font-size: 12px; text-align: left">Product</th>
        							<th style="background-color: #F4F4F4; border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; width: 50px; font-family: sans-serif; font-size: 12px; text-align: center">Qty</th>
        							<th style="background-color: #F4F4F4; border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; width: 155px; font-family: sans-serif; font-size: 12px; text-align: left">Unit Price</th>
        							<th style="background-color: #F4F4F4; border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; width: 155px; font-family: sans-serif; font-size: 12px; text-align: left">Price</th>
        						</tr>
        					</thead>
        					<tbody>
        						<tr>
        							<td style="border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; font-family: sans-serif; font-size: 11px">
        								<strong style="color: #BF461E; font-size: 12px; margin-bottom: 5px">Web Replika</strong>
        								<ul style="margin: 0"></ul>
        							</td>
        							<td style="border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; text-align: center; width: 50px; font-family: sans-serif; font-size: 11px">1</td>
        							<td style="border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; width: 155px; font-family: sans-serif; font-size: 11px">RM 50.00</td>
        							<td style="border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; width: 155px; font-family: sans-serif; font-size: 11px">RM 50.00</td>
        						</tr>
        					</tbody>
        					<tfoot>
        						<tr>
        							<td colspan="2" style="background-color: #F4F4F4; border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; font-size: 11px">&nbsp;</td>
        							<td style="border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; text-align: right; width: 155px; font-family: sans-serif"><strong style="font-size: 12px">Total:</strong></td>
        							<td style="border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; width: 155px; font-family: sans-serif"><strong style="font-size: 12px">RM 30.00</strong></td>
        						</tr>
        					</tfoot>
        				</table>
        			</td>
        		</tr>
        	</tbody>
        </table>';
        $message .= "</body></html>";
        // smtpmailer('info@gajimasyuk.com', 'Admin Gajimasyuk MMGUIA' , 'info@gajimasyuk.com', 'Gajimasyuk', $email_subject, $message);

        // error_log("jom email $desc $username $bill_id");
    }

    if($desc == 'Pakej 3G/4G'){

        $sql = mysqli_query($db,"SELECT * FROM wp_members_idp WHERE username = '$username'");
        $row = mysqli_fetch_array($sql,MYSQLI_ASSOC);
        $nama = $row["nama"];
        $no_tel = $row["no_tel"];
        $no_kp = $row["no_kp"];
        $email = $row["email"];
        $alamat = $row["alamat"];
        $img_url = $row["img_url"];
        $pakej = $row["pakej"];
        $no_idp = $row["no_idp"];
        $status = $row["status"];
        $username = $row["username"];
        $bayaran = $row["bayaran"];


        $email_subject = "PEMBELIAN PAKEJ ";
        $message = '<html><body align="center">';
        $message .= '<table bgcolor="#FFFFFF" border="0" cellpadding="5" cellspacing="0" width="100%">
        	<tbody>
        		<tr bgcolor="#EAF2FA">
        			<td colspan="2"><font style="font-family: sans-serif; font-size: 12px"><strong>Nama</strong></font></td>
        		</tr>
        		<tr bgcolor="#FFFFFF">
        			<td width="20">&nbsp;</td>
        			<td><font style="font-family: sans-serif; font-size: 12px">'.$nama.'</font></td>
        		</tr>
        		<tr bgcolor="#EAF2FA">
        			<td colspan="2"><font style="font-family: sans-serif; font-size: 12px"><strong>No Kad Pengenalan</strong></font></td>
        		</tr>
        		<tr bgcolor="#FFFFFF">
        			<td width="20">&nbsp;</td>
        			<td><font style="font-family: sans-serif; font-size: 12px">'.$no_kp.'</font></td>
        		</tr>
        		<tr bgcolor="#EAF2FA">
        			<td colspan="2"><font style="font-family: sans-serif; font-size: 12px"><strong>No Telefon</strong></font></td>
        		</tr>
        		<tr bgcolor="#FFFFFF">
        			<td width="20">&nbsp;</td>
        			<td><font style="font-family: sans-serif; font-size: 12px">'.$no_tel.'</font></td>
        		</tr>
        		<tr bgcolor="#EAF2FA">
        			<td colspan="2"><font style="font-family: sans-serif; font-size: 12px"><strong>Alamat</strong></font></td>
        		</tr>
        		<tr bgcolor="#FFFFFF">
        			<td width="20">&nbsp;</td>
        			<td><font style="font-family: sans-serif; font-size: 12px">'.$alamat.'</font></td>
        		</tr>
        		<tr bgcolor="#EAF2FA">
        			<td colspan="2"><font style="font-family: sans-serif; font-size: 12px"><strong>Email</strong></font></td>
        		</tr>
        		<tr bgcolor="#FFFFFF">
        			<td width="20">&nbsp;</td>
        			<td><font style="font-family: sans-serif; font-size: 12px">'.$email.'</font></td>
        		</tr>
        		<tr bgcolor="#EAF2FA">
        			<td colspan="2"><font style="font-family: sans-serif; font-size: 12px"><strong>Alamat</strong></font></td>
        		</tr>
        		<tr bgcolor="#FFFFFF">
        			<td width="20">&nbsp;</td>
        			<td><font style="font-family: sans-serif; font-size: 12px">'.$alamat.'</font></td>
        		</tr>
        		<tr bgcolor="#EAF2FA">
        			<td colspan="2"><font style="font-family: sans-serif; font-size: 12px"><strong>Username</strong></font></td>
        		</tr>
        		<tr bgcolor="#FFFFFF">
        			<td width="20">&nbsp;</td>
        			<td><font style="font-family: sans-serif; font-size: 12px">'.$username.'</font></td>
        		</tr>
        		<tr bgcolor="#EAF2FA">
        			<td colspan="2"><font style="font-family: sans-serif; font-size: 12px"><strong>Gambar</strong></font></td>
        		</tr>
        		<tr bgcolor="#FFFFFF">
        			<td width="20">&nbsp;</td>
        			<td>  <a href="v2.gajimasyuk.com/images/uploads/'.$img_url.'" target="_blank"><button type="button" class="btn btn-sm" id="btn-sm-padding" name="button">Gambar Pengguna</button></a></td>
        		</tr>
        		<tr bgcolor="#EAF2FA">
        			<td colspan="2"><font style="font-family: sans-serif; font-size: 12px"><strong>Order</strong></font></td>
        		</tr>
        		<tr bgcolor="#FFFFFF">
        			<td width="20">&nbsp;</td>
        			<td>
        				<table cellspacing="0" style="border-left: 1px solid #DFDFDF; border-top: 1px solid #DFDFDF" width="97%">
        					<thead>
        						<tr>
        							<th style="background-color: #F4F4F4; border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; font-family: sans-serif; font-size: 12px; text-align: left">Product</th>
        							<th style="background-color: #F4F4F4; border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; width: 50px; font-family: sans-serif; font-size: 12px; text-align: center">Qty</th>
        							<th style="background-color: #F4F4F4; border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; width: 155px; font-family: sans-serif; font-size: 12px; text-align: left">Unit Price</th>
        							<th style="background-color: #F4F4F4; border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; width: 155px; font-family: sans-serif; font-size: 12px; text-align: left">Price</th>
        						</tr>
        					</thead>
        					<tbody>
        						<tr>
        							<td style="border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; font-family: sans-serif; font-size: 11px">
        								<strong style="color: #BF461E; font-size: 12px; margin-bottom: 5px">'.$pakej.'termasuk penghantaran.</strong>
        								<ul style="margin: 0"></ul>
        							</td>
        							<td style="border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; text-align: center; width: 50px; font-family: sans-serif; font-size: 11px">1</td>
        							<td style="border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; width: 155px; font-family: sans-serif; font-size: 11px">RM '.$bayaran.'.00</td>
        							<td style="border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; width: 155px; font-family: sans-serif; font-size: 11px">RM '.$bayaran.'.00</td>
        						</tr>
        					</tbody>
        					<tfoot>
        						<tr>
        							<td colspan="2" style="background-color: #F4F4F4; border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; font-size: 11px">&nbsp;</td>
        							<td style="border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; text-align: right; width: 155px; font-family: sans-serif"><strong style="font-size: 12px">Total:</strong></td>
        							<td style="border-bottom: 1px solid #DFDFDF; border-right: 1px solid #DFDFDF; padding: 7px; width: 155px; font-family: sans-serif"><strong style="font-size: 12px">RM '.$bayaran.'.00</strong></td>
        						</tr>
        					</tfoot>
        				</table>
        			</td>
        		</tr>
        	</tbody>
        </table>';
        $message .= "</body></html>";
        // smtpmailer('info@gajimasyuk.com', 'Admin Gajimasyuk MMGUIA' , 'info@gajimasyuk.com', 'Gajimasyuk', $email_subject, $message);
    }
    // error_log("dah bayar");
}
/*
 * Jika bayaran tidak dibuat
 */
 else {
    // $satu = implode(" ",$moreData);
    // $dua  = implode(" ",$data);
    // error_log("tak bayar haha $satu huhu $dua");
}
