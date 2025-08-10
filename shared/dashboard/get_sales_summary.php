<?php
require_once "../../db_con/connection.php";
require '../../db_con/check_authentication.php';


$sql = "SELECT
    'today' AS period,
    SUM(all_items_price) AS total_price
FROM
    purchase_order
WHERE
    paid_at IS NOT NULL
    AND DATE(paid_at) = CURDATE()

UNION ALL

SELECT
    'yesterday' AS period,
    SUM(all_items_price) AS total_price
FROM
    purchase_order
WHERE
    paid_at IS NOT NULL
    AND DATE(paid_at) = CURDATE() - INTERVAL 1 DAY

UNION ALL

SELECT
    'this_week' AS period,
    SUM(all_items_price) AS total_price
FROM
    purchase_order
WHERE
    paid_at IS NOT NULL
    AND YEARWEEK(paid_at, 1) = YEARWEEK(CURDATE(), 1)

UNION ALL

SELECT
    'last_week' AS period,
    SUM(all_items_price) AS total_price
FROM
    purchase_order
WHERE
    paid_at IS NOT NULL
    AND YEARWEEK(paid_at, 1) = YEARWEEK(CURDATE(), 1) - 1

UNION ALL

SELECT
    'this_month' AS period,
    SUM(all_items_price) AS total_price
FROM
    purchase_order
WHERE
    paid_at IS NOT NULL
    AND YEAR(paid_at) = YEAR(CURDATE())
    AND MONTH(paid_at) = MONTH(CURDATE())

UNION ALL

SELECT
    'last_month' AS period,
    SUM(all_items_price) AS total_price
FROM
    purchase_order
WHERE
    paid_at IS NOT NULL
    AND YEAR(paid_at) = YEAR(CURDATE() - INTERVAL 1 MONTH)
    AND MONTH(paid_at) = MONTH(CURDATE() - INTERVAL 1 MONTH)";

$sql = "WITH sales_data AS (
    SELECT
        'today' AS period,
        SUM(all_items_price) AS total_price
    FROM
        purchase_order
    WHERE
        paid_at IS NOT NULL
        AND DATE(paid_at) = CURDATE()

    UNION ALL

    SELECT
        'yesterday' AS period,
        SUM(all_items_price) AS total_price
    FROM
        purchase_order
    WHERE
        paid_at IS NOT NULL
        AND DATE(paid_at) = CURDATE() - INTERVAL 1 DAY

    UNION ALL

    SELECT
        'this_week' AS period,
        SUM(all_items_price) AS total_price
    FROM
        purchase_order
    WHERE
        paid_at IS NOT NULL
        AND YEARWEEK(paid_at, 1) = YEARWEEK(CURDATE(), 1)

    UNION ALL

    SELECT
        'last_week' AS period,
        SUM(all_items_price) AS total_price
    FROM
        purchase_order
    WHERE
        paid_at IS NOT NULL
        AND YEARWEEK(paid_at, 1) = YEARWEEK(CURDATE(), 1) - 1

    UNION ALL

    SELECT
        'this_month' AS period,
        SUM(all_items_price) AS total_price
    FROM
        purchase_order
    WHERE
        paid_at IS NOT NULL
        AND YEAR(paid_at) = YEAR(CURDATE())
        AND MONTH(paid_at) = MONTH(CURDATE())

    UNION ALL

    SELECT
        'last_month' AS period,
        SUM(all_items_price) AS total_price
    FROM
        purchase_order
    WHERE
        paid_at IS NOT NULL
        AND (
            YEAR(paid_at) = YEAR(CURDATE() - INTERVAL 1 MONTH)
            AND MONTH(paid_at) = MONTH(CURDATE() - INTERVAL 1 MONTH)
        )
)
SELECT
    period,
    total_price,
    CASE
        WHEN period = 'today' THEN
            (total_price - (SELECT total_price FROM sales_data WHERE period = 'yesterday'))
            / (SELECT total_price FROM sales_data WHERE period = 'yesterday') * 100
        WHEN period = 'this_week' THEN
            (total_price - (SELECT total_price FROM sales_data WHERE period = 'last_week'))
            / (SELECT total_price FROM sales_data WHERE period = 'last_week') * 100
        WHEN period = 'this_month' THEN
            (total_price - (SELECT total_price FROM sales_data WHERE period = 'last_month'))
            / (SELECT total_price FROM sales_data WHERE period = 'last_month') * 100
        ELSE NULL
    END AS percentage_difference
FROM
    sales_data";
$result=mysqli_query($db,$sql);
// while($sales[] = mysqli_fetch_assoc($result));
while ($row = $result->fetch_assoc()) {
    $row['total_price'] = number_format($row['total_price'], 2);
    $row['percentage_difference'] = number_format($row['percentage_difference'], 2);
    $sales[$row['period']] = $row;
}
// Return JSON response
header('Content-Type: application/json');
// array_pop($sales);
echo json_encode($sales);

 ?>
