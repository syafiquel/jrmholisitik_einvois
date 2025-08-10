<?php
// require 'init.php';
require_once "db_con/connection.php";

// 1. Load Reference 1 values from CSV file (only for 'Credit Terms Settlement')
$csvFile = '__checker/7-7-2025 JRM.csv'; // Save Excel as .csv first
$handle = fopen($csvFile, 'r');
$referenceList = [];

if ($handle !== false) {
    $header = fgetcsv($handle); // Read header row

    while (($data = fgetcsv($handle)) !== false) {
        $billDescription = $data[4]; // Column E
        $reference1 = trim($data[8]); // Column I

        if ($billDescription === "Credit Terms Settlement" && $reference1 !== '') {
            $referenceList[] = $reference1;
            $bills[$reference1] = $data;
        }
    }
    fclose($handle);
} else {
    die("Could not open the CSV file.");
}

// 2. Get all settlement_ref values (from 1 May 2025 onward)
$settlementRefs = [];
$sql = "SELECT settlement_ref, user_id FROM credit_settlement WHERE approval_status IS NULL AND settlement_ref IS NOT NULL ORDER BY id DESC";
$result = mysqli_query($db, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $settlementRefs[] = $row['settlement_ref'];
    $settlement[$row['settlement_ref']] = $row;
}

// 2.2 Get all credit terms
$allRefs = implode("','", $referenceList );
$sql = "SELECT user_id, settlement_ref FROM credit_terms WHERE settlement_ref IN ('{$allRefs}') ORDER BY id DESC";
$resultTerm = mysqli_query($db, $sql);
while ($row = mysqli_fetch_assoc($resultTerm)) {
    $terms[$row['settlement_ref']] = $row;
}
// var_dump($terms);
// exit;
// mysqli_close($db);

// 3. Compare arrays
$settlementSet = array_flip($settlementRefs);
$results = [];

foreach ($referenceList as $ref) {
    $status = isset($settlementSet[$ref]) ? '✅ Found' : '❌ Missing';
    $results[] = [
        'Reference 1' => $ref,
        'Status' => $status,
    ];
}
// var_dump($settlement);
// exit;


// 4. Output results
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Reference 1</th><th>Status</th></tr>";
foreach ($results as $result) {
  $reff = $result['Reference 1'];
  $bill = $bills[$reff];
  $billDesc = $bill[4];
  $settle = isset($settlement[$reff]) ? $settlement[$reff] : null;
  $insert = '- not paid';
  $sql = "SELECT * FROM credit_settlement WHERE billplz_id = '{$bill[2]}'";
  $resultx = $db->query($sql);
  if ($resultx->num_rows > 0) {
    // return 0;
  }else {
    // if (!isset($terms[$reff])) {
    //   // code...
    //   continue;
    // }
    // var_dump($reff, $terms[$reff]);
    // exit;
    $user_id = $terms[$reff]['user_id'];
    $ref = $bill[8];
    $type = $bill[9];
    $amount = $bill[16];
    $dateInput = $bill[10];
    $date = DateTime::createFromFormat('d/m/y', $dateInput); // handles single-digit day/month
    $dateBill = $date->format('Y-m-d');
    // code...
    $sql =  "INSERT INTO credit_settlement (user_id, type, settlement_ref, billplz_id, amount, paid_at)
    VALUES ('{$user_id}', '$type', '$ref', '{$bill[2]}', '$amount', '{$dateBill}')";
    echo $sql."<br>";
    echo "<tr><td colspan=5 >$sql</td></tr>";
    // exit;
    if ($user_id && mysqli_query($db, $sql)) {

      $sql = "UPDATE user SET
      credit_used = credit_used - $amount
      WHERE id = '{$user_id}'";
      // echo "$sql";
      // exit;
      if (mysqli_query($db, $sql)) {
        $sql = "UPDATE credit_terms SET
        status = 'completed',
        billplz_id = '{$bill[2]}',
        settled_at = '$dateBill'
        WHERE user_id = '{$user_id}'
        AND settlement_ref = '$ref'";
        echo "$sql<br>";
        // exit;
        mysqli_query($db, $sql);
        $insert = '- paid';
        // return 1;

      }
    }else {
      echo "<tr><td colspan=5 >Insert Error: " . mysqli_error($db) . "</td></tr>";

      $insert = '- failed';
      // code...
    }
  }
  // if ($result['Status'] == '❌ Missing') {
  //   echo "<tr><td colspan=5 >".print_r($bill)."</td></tr>";
  //
  // }
    echo "<tr><td>{$billDesc}</td><td>{$reff}</td><td>{$result['Status']} {$insert}</td></tr>";
}
echo "</table>";
?>
