<?php

class User {

  public static function isDealer($user_id ){
    global $database;

    $sql = "SELECT * FROM user WHERE user_type = 'd' AND id = $user_id ";
    // echo $sql;
    // exit;
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      return 1;
    }else {
      return 0;
    }

  }
  public static function waitingApproval($user_id ){
    global $database;

    $sql = "SELECT * FROM purchase_order
            WHERE user_id = $user_id
            AND payment_status = ''
            AND resit_img = ''
            AND date_time_transfer = ''
            AND isUpgrade = 1
            LIMIT 1";
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      return 1;
      // while($row = $result->fetch_assoc()) {
      //   $order_id = $row['id'];
      // }
    }else {
      return 0;
    }

  }
  public static function isSuperAdmin($user_id ){
    global $database;

    $sql = "SELECT * FROM user WHERE access_level = '99' AND id = $user_id";
    // echo $sql;
    // exit;
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      return 1;
    }else {
      return 0;
    }

  }
  public static function getIdByIdp($idp){
    global $database;
    global $webType;

    $sql = "SELECT id FROM user WHERE no_idp = $idp ";
    $result = $database->query($sql);
    $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
    return $row["id"];
  }

  public static function getUserInfo($idp){
    global $database;
    global $webType;

    $sql = "SELECT * FROM user WHERE no_idp = '$idp' AND payment_status = 'Done' AND web_type = '$webType' AND isNonJ2S IS NULL";
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $user["id"] = $row["id"];
      $user["nama"] = $row["nama"];
      $user["nick_name"] = $row["nick_name"];
      $user["no_tel"] = $row["no_tel"];
      $user["payment_status"] = $row["payment_status"];
      $user["level"] = 1;
      return $user;
    }
    // return '';
  }
  public static function getUserInfoByUsername($username, $business_id = 0){
    global $database;
    global $webType;




    $sql = "SELECT * FROM user WHERE username = '$username' AND payment_status = 'Done' ";
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $user["id"] = $row["id"];
      // $currentBizRank = Business::currentBizRank($user["id"], $business_id);
      $user['isActive'] = $currentBizRank['membership_type'] == 'lifetime' ? 1 : self::checkUserIsActive($row['active_until']);
      // $user['isActive'] = self::checkUserIsActive($row['active_until']);
      $user["no_idp"] = $row["no_idp"];
      $user["nama"] = $row["nama"];
      $user["nick_name"] = $row["nick_name"];
      $user["no_tel"] = $row["no_tel"];
      $user["payment_status"] = $row["payment_status"];
      $user["profile_picture"] = $row["profile_picture"];
      $user["level"] = 1;
      return $user;
    }
    // return '';
  }

  public static function getUser($id){
    global $database;
    global $ranks_ref;

    $sql = "SELECT * FROM user WHERE id = '$id'";
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $row['my_rank'] = $ranks_ref[$row['rank_id']];
      return $row;
    }
    // return '';
  }

  public static function checkUserIsActive($active_until)
  {
    if ($active_until) {

      $today_time = strtotime(date("Y-m-d H:i:s"));
      $expire_time = strtotime($active_until);
      if ($expire_time > $today_time){
        return 1;
      }

    }
    return 0;
  }

  public static function get($id){
    global $database;
    // $business = Business::currentBusiness();
    // $business_id = $business['id'];
    $sql = "SELECT * FROM user WHERE id = '$id'";
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      $user = mysqli_fetch_array($result,MYSQLI_ASSOC);
      // $user['isActive'] = self::checkUserIsActive($user['active_until']);
      // $level_info = Level::get($user["level_id"]);
      // if ($level_info) {
      //   $user["level_info"] = $level_info;
      //   $user["next_level_id"] = $level_info['level_up_id'];
      //   $user["next_level_info"] = Level::get($level_info['level_up_id']);
      //   $user["next_level_requirements"] = Level::exp_guide($level_info["level_up_id"]);
      // }
      // $user["current_experience"] = self::getCurrentExp($id);
      // $user["unlock_bonus"] = self::getUnlockBonusStatus($id, $business_id);
      return $user;
    }
  }

  public static function getLevelId($id){
    global $database;

    $sql = "SELECT * FROM user WHERE id = '$id'";
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      $user = mysqli_fetch_array($result,MYSQLI_ASSOC);
      return $user["level_id"] ?? 1;
    }
  }

  public static function getUnlockBonusStatus($id, $business_id){
    global $database;

    $month = date('m');
    $year = date('Y');
    $sql = "SELECT * FROM unlock_user WHERE user_id = '$id' AND business_id = '$business_id' AND month = '$month' AND year = '$year' AND paid_at IS NOT NULL";
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      return 1;

    }
    return 0;
  }

  public static function getUserInfoById($id, $business_id = null){
    global $database;

    $sql = "SELECT * FROM user WHERE id = $id";
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $user = $row;
      if ($business_id) {
        $currentBizRank = Business::currentBizRank($user["id"], $business_id);
        $user['isActive'] = $currentBizRank['membership_type'] == 'lifetime' ? 1 : self::checkUserIsActive($row['active_until']);
      }
      $user["id"] = $row["id"];
      $user["no_idp"] = $row["no_idp"];
      $user["nama"] = $row["nama"];
      $user["email"] = $row["email"];
      $user["username"] = $row["username"];
      $user["no_tel"] = $row["no_tel"];
      $user["level_id"] = $row["level_id"];
      $user["web_type"] = $row["web_type"];
      $level_info = Level::get($row["level_id"]);
      $user["level_info"] = $level_info;
      $user["next_level_id"] = $level_info['level_up_id'];
      $user["upline_tree"] = $row["upline_tree"];
      $user["current_experience"] = self::getCurrentExp($id);
      return $user;
    }
    // return '';
  }

  public static function getUserBizInfo($id){
    global $database;

    $sql = "SELECT * FROM business_user WHERE id = $id";
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);

      return $row ?? null;
    }
    // return '';
  }

  public static function getUserPointBalance($id){
    global $database;

    $sql = "SELECT * FROM user WHERE id = $id";
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $user["id"] = $row["id"];
      $user["point_wallet"] = $row["point_wallet"];
      return $user;
    }
    // return '';
  }

  public static function getCurrentExp($id)
  {
    global $database;
    $downlines = [
      'recruitments' => 0,
      'level_1' => 0,
      'level_2' => 0,
      'level_3' => 0,
      'level_4' => 0
    ];
    $recruitments = 0;
    $team = 0;
    $sql = "SELECT COUNT(*) as count, level_id FROM user WHERE introducer = '$id' AND payment_status = 'Done' AND created_at > '2023-03-01' GROUP BY level_id";
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        if ($row['level_id']) {
          $recruitments += $row['count'];
          $downlines["level_".$row['level_id']] = $row['count'];
          $team += $row['count'];
        }
      }
      $downlines['recruitments'] = $recruitments;
      $downlines['team'] = $team;
      return $downlines;
    }
    return null;
  }

  public static function getPenajaInfo($id){
    global $database;

    $sql = "SELECT *, u.nama as nama, u.no_idp as no_idp, u.no_tel_tone_excel as no_tel_tone_excel, u.no_tel as no_tel,
            u.track_no as track_no, u.no_idp_penaja as no_idp_penaja, up.nama as nama_penaja, up.no_tel as no_tel_penaja, up.email as email_penaja
            FROM user u
            LEFT JOIN user up
            ON u.introducer = up.id
            WHERE u.id = $id";
    $result = mysqli_query($database,$sql);
    $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
    $penaja['nama'] = $row['nama_penaja'];
    $penaja['no_idp'] = $row['no_idp_penaja'];
    $penaja['no_tel'] = $row['no_tel_penaja'];
    $penaja['email'] = $row['email_penaja'];
    return $penaja;
  }
  public static function getPenajaInfoById($id){
    global $database;

    $sql = "SELECT *
            FROM user
            WHERE id = $id";
    $result = mysqli_query($database,$sql);
    $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
    $penaja['nama'] = $row['nama'];
    $penaja['email'] = $row['email'];
    $penaja['no_idp'] = $row['no_idp'];
    $penaja['no_tel'] = $row['no_tel'];
    $penaja['web_type'] = $row['web_type'];
    return $penaja;
  }

  public static function getUserByOrder($key, $webType = null){
    global $database;
    $whereWebType= '';
    // if ($webType) {
    //   $whereWebType = "AND web_type = '$webType'";
    // }
    $sql = "SELECT * FROM user WHERE payment_key = '$key' $whereWebType";
    // echo $sql;
    // exit;
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $user["id"] = $row["id"];
      $user["nama"] = $row["nama"];
      $user["no_idp"] = $row["no_idp"];
      $user["no_tel"] = Helper::sanitizePhone($row["no_tel"]);
      $user["email"] = $row["email"];
      $user["web_type"] = $row["web_type"];
      $user["bill_id"] = $row["bill_id"];
      $user["alamat"] = $row["alamat"];
      $user["negeri"] = $row["negeri"];
      $user["harga_pakej"] = $row["harga_pakej"];
      $user["pakej"] = $row["pakej"];
      $user["introducer"] = $row["introducer"];
      $user["payment_status"] = $row["payment_status"] ? : 'Pending';
      $user["date_transfer"] = $row["date_transfer"];
      $user["time_transfer"] = $row["time_transfer"];
      $user["kekal_nombor_lama"] = $row["kekal_nombor_lama"];
      $user["no_kp"] = $row["no_kp"];
      $user["no_passport"] = $row["no_passport"];
      $user["img_url"] = $row["img_url"];
      $user['upline_tree'] = $row['upline_tree'] ? $row['upline_tree'] : Level::checkUserUplineTree($row['id']);
      $user["website_activation"] = $row["website_activation"];

      $user["telco_lama"] = $row["telco_lama"];
      if ($row["payment_status"] == 'Done') {
        $user["payment_status"] == 'Done';
      }elseif ($row["payment_status"] != 'Done' && $row["date_transfer"] && $row["time_transfer"]) {
        $user["payment_status"] == 'Awaits approval';
        // code...
      }else {
        $user["payment_status"] == 'Pending';
      }

      return $user;
    }
    // return '';
  }


  public static function getUserByPurchaseOrder($order_id){

    global $database;
    $sql = "SELECT u.id as id, u.nama as nama, u.email as email, u.no_tel as no_tel, u.web_type as web_type
            FROM purchase_order po
            LEFT JOIN user u
            ON u.id = po.user_id
            WHERE po.id = '$order_id'
            LIMIT 1";
    // echo $sql;
    // exit;
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      $user = mysqli_fetch_array($result,MYSQLI_ASSOC);
    }
    return $user;
  }

  public static function businessRank($user_id, $biz_id){

    global $database;

    $sql = "SELECT * FROM business_user bizu LEFT JOIN business_rank bizr ON bizr.id = bizu.rank_id WHERE bizu.user_id = '$user_id' AND bizu.business_id = '$biz_id'";
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      return mysqli_fetch_array($result,MYSQLI_ASSOC);
      return $row['rank_id'];
    }
    return null;
  }

  public static function updatePoints($user_id, $points, $type = 'add')
  {
    global $database;
    $operator = $type == 'add' ? '+': '-';
    $sql = "UPDATE user SET point_wallet = point_wallet $operator $points WHERE id = $user_id";
    mysqli_query($database, $sql);
  }

  public static function updateActiveUntilDate($renewal)
  {
    global $database;
    $renewal = Membership::getRenewalLog($renewal['payment_ref']);

    // echo "masuk <br/>";
    // var_dump($renewal);
    // exit;
    $until_date = $renewal['renew_until'];
    $user_id = $renewal['user_id'];

    $billplz_id = $renewal['bill_id'];
    $paid_at = $renewal['paid_at'];
    $membership_type = $renewal['membership_type'];
    $user_biz_id = $renewal['user_biz_id'];

    // $sql = "UPDATE user SET active_until = '$until_date' WHERE id = $user_id";
    $sql = "UPDATE business_user SET
      active_until = '$until_date'
      , billplz_id = '$billplz_id'
      , paid_at = '$paid_at'
      , membership_type = '$membership_type'
      , payment_status = 'Done'
    WHERE id = $user_biz_id";
    // echo $sql;exit;
    if (mysqli_query($database, $sql)) {
      // echo "done update $user_id to $until_date<hr>";
      // TODO: award upline 3 level up.
      $user = User::getUserInfoById($user_id);
      // var_dump($user);
      // exit;
      $upline_tree = json_decode($user['upline_tree'], true);
      // var_dump($upline_tree);
      // exit;
      // echo "<hr>Update upline bonus<br>";
      if ($user['upline_tree']) {
        $upline_tree = json_decode($user['upline_tree'], true);
        Bonus::overideTopTier($upline_tree, $renewal, $user_id);
      }else {
        // echo "<br>no upline";
      }

    }

  }
  public static function updateActiveRegistration($user_id, $month_count = 1)
  {
    global $database;
    // var_dump($renewal);
    // exit;
    $date = new DateTime();
    $daysPerMonth = 30;
    $totalDays = ($daysPerMonth * $month_count) + 1;
    // $date->modify('+'.$month_count.' months');
    $date->modify("+$totalDays days");
    $renew_until = $date->format("Y-m-d 23:59:59");

    $sql = "UPDATE user SET active_until = '$renew_until' WHERE id = $user_id";
    if (mysqli_query($database, $sql)) {
    }else {
      // echo "<br>no upline";
    }


  }

  public static function upgradeToStarter($user_id, $bill_id, $amount)
  {
    // code...
  }

}

?>
