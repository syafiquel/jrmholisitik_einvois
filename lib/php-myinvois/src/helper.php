<?php

function getInvoiceData($order_id, $db)
{
    $invoiceData = [];

    // Get order details
    $sql = "SELECT * FROM purchase_order WHERE id = $order_id";
    $result = $db->query($sql);
    $order = $result->fetch_assoc();

    // Get user details
    $user_id = $order['user_id'];
    $sql = "SELECT * FROM user WHERE id = $user_id";
    $result = $db->query($sql);
    $user = $result->fetch_assoc();

    // Get order items
    $sql = "SELECT od.*, p.product_name FROM order_details od JOIN product p ON od.product_id = p.id WHERE order_id = $order_id";
    $result = $db->query($sql);
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    // Now, let's format the data into the structure that the UblBuilder will expect.
    // This is a sample structure, you might need to adjust it based on the actual UBL schema.
    $invoiceData = [
        'invoice' => [
            'id' => $order['id'],
            'issue_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d'),
            'invoice_type_code' => '388',
        ],
        'supplier' => [
            'name' => 'Your Company Name',
            'address' => 'Your Company Address',
            'tin' => 'Your Company TIN',
        ],
        'customer' => [
            'name' => $user['nama'],
            'address' => $user['alamat'],
            'tin' => 'Customer TIN', // You might need to add this to your user table
        ],
        'lines' => [],
        'tax_total' => [],
        'legal_monetary_total' => [],
    ];

    $total_tax = 0;
    $total_amount = 0;

    foreach ($items as $item) {
        $line_total = $item['quantity'] * $item['price'];
        $tax_amount = $line_total * 0.06; // Assuming 6% tax
        $total_tax += $tax_amount;
        $total_amount += $line_total;

        $invoiceData['lines'][] = [
            'id' => $item['id'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'name' => $item['product_name'],
            'tax' => [
                'amount' => $tax_amount,
                'rate' => 6,
            ],
            'total' => $line_total,
        ];
    }

    $invoiceData['tax_total'] = [
        'amount' => $total_tax,
    ];

    $invoiceData['legal_monetary_total'] = [
        'payable_amount' => $total_amount + $total_tax,
    ];

    return $invoiceData;
}
