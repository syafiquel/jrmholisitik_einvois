  <?php

/**
 *
 */
class Credit
{

  public static function getSetting()
  {
    global $database;

    $sql = "SELECT * FROM setting WHERE id = '1' ";
    $result = $database->query($sql);
    return $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
    return 1;

  }

  public static function available($user_id)
  {
    global $database;

    $sql = "SELECT * FROM credit_terms WHERE user_id = '$user_id' AND status = 'outstanding'";
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      return 0;
    }
    return 1;

  }
  public static function getCreditInfo($user_id, $order_id)
  {
    global $database;

    $sql = "SELECT * FROM credit_terms WHERE user_id = '$user_id' AND order_id = '$order_id'";
    $result = $database->query($sql);
    $term = mysqli_fetch_array($result,MYSQLI_ASSOC);
    $settlement = null;
    if ($term['settlement_ref']) {
      $sql = "SELECT * FROM credit_settlement WHERE settlement_ref = '{$term['settlement_ref']}'";
      $result = $database->query($sql);
      $settlement = mysqli_fetch_array($result,MYSQLI_ASSOC);
      $settled_via = $settlement['billplz_id'] ? 'Billplz' : 'Bank Transfer';
      $settled_resit = $settlement['billplz_id'] ? "https://billplz.com/bills/{$settlement['billplz_id']}" : "/credit_settlement/".$settlement['resit_file'];
    }

    return [
      'status' => $term['status'],
      'settled_via' => $settled_via ?? '',
      'settled_resit' => $settled_resit ?? ''
    ];


  }

  public static function recordCreditTermsUse($order)
  {
    global $database;

      $sql =  "INSERT INTO credit_terms (user_id, order_id, outstanding_amount, status, created_at)
    VALUES ('{$order['user_id']}', '{$order['id']}', '{$order['all_items_price']}', 'outstanding', now())";
    if (mysqli_query($database, $sql)) {
      $sqlU = "UPDATE user SET credit_used = credit_used + {$order['all_items_price']}, last_credit_used_at = now() WHERE id = '{$order['user_id']}'";
      mysqli_query($database, $sqlU);
      return 1;
    }
    return 0;
  }

  public static function getOutstanding($user_id)
  {
    global $database;
    $sql = "SELECT * FROM credit_terms WHERE user_id = '$user_id' AND status = 'outstanding'";
    $result = $database->query($sql);
    return $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
  }

  public static function getCreditTerms($ref)
  {
    global $database;
    $sql = "SELECT * FROM credit_terms WHERE settlement_ref = '$ref' ";
    $result = $database->query($sql);
    return $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
  }

  public static function getPendingSettlement($user_id)
  {
    global $database;
    $sql = "SELECT * FROM credit_settlement WHERE user_id = '$user_id' AND approval_status = 'pending' LIMIT 1";
    $result = $database->query($sql);
    return $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
  }

  public static function getSettlementByRef($ref)
  {
    global $database;
    $sql = "SELECT * FROM credit_settlement WHERE settlement_ref = '$ref'";
    $result = $database->query($sql);
    return $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
  }

  public static function settleOutstanding($respon, $isCallback = 'callback')
  {
    global $database;

    $amount_paid = $respon['amount'];
    $ref = $respon['settlement_ref'];
    $type = $respon['settlement_type'];

    $fullClause = '';

    $creditTerm = self::getCreditTerms($respon['settlement_ref']);
    $user_id = $creditTerm['user_id'];
    $user = User::getUser($user_id);
    if ($user['credit_used'] < $amount_paid ) {
      // Telegram::sendMessage('')
      Telegram::sendMessage('87754515', "BILLPLZ ({$respon['bill_id']})- {$user['id']} - {$respon['settlement_ref']} ");
      // return;
    }

    $sql = "SELECT * FROM credit_settlement WHERE billplz_id = '{$respon['bill_id']}'";
    $result = $database->query($sql);
    if ($result->num_rows > 0) {
      return 0;
    }else {
      Telegram::sendMessage('87754515', "{$isCallback} - BILLPLZ ({$respon['bill_id']})- {$user['id']} - {$respon['settlement_ref']} ");
      // code...
      $sql =  "INSERT INTO credit_settlement (user_id, type, settlement_ref, billplz_id, amount, paid_at)
      VALUES ('{$user_id}', '$type', '$ref', '{$respon['bill_id']}', '{$respon['amount']}', now())";
      // echo "$sql";
      // exit;
      if (mysqli_query($database, $sql)) {

        // TODO: check sql to "SELECT SUM(outstanding_amount) AS credit_used FROM credit_terms WHERE user_id = 43 AND (status = 'outstanding');"
        // echo "$sql";
        // exit;
        $sql = "UPDATE credit_terms SET
        status = 'completed',
        billplz_id = '{$respon['bill_id']}',
        settled_at = now()
        WHERE settlement_ref = '$ref'";

        if (mysqli_query($database, $sql)) {
          $credit_used = self::calculateCreditUsed($user_id);

          $sql = "UPDATE user SET
          credit_used = $credit_used
          WHERE id = '{$user_id}'";
          // echo "$sql";
          // exit;
          mysqli_query($database, $sql);
          return 1;
        }
      }
    }

    return 0;
  }

  public static function updateSettlementRef($ids, $ref)
  {
    global $database;
    $sql = "UPDATE credit_terms SET
      settlement_ref = '$ref'
      WHERE order_id IN ($ids)";
    mysqli_query($database, $sql);
    return 1;
  }

  public function calculateCreditUsed($user_id) {
    global $database;

    $query = "
        SELECT SUM(outstanding_amount) AS credit_used
        FROM credit_terms
        WHERE user_id = $user_id
          AND (status != 'completed' OR settled_at IS NULL)
    ";

    $result = $database->query($query);
    $row = $result->fetch_assoc();

    return $row['credit_used'] ?? 0;
  }


}


 ?>
