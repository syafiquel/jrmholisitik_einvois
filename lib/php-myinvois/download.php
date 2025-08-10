<?php

require_once __DIR__ . '/autoload.php';

if (isset($_GET['submission_id'])) {
    $submissionId = $_GET['submission_id'];

    // You would have your own logic to instantiate the facade.
    // For this example, we are creating a new instance.
    $config = require __DIR__ . '/config.php';
    $client = new MyInvois\Core\MyInvoisClient($config);
    $ublBuilder = new MyInvois\Ubl\UblBuilder();
    $facade = new MyInvois\MyInvoisFacade($client, $ublBuilder);

    $document = $facade->getDocument($submissionId);

    if ($document['success']) {
        $fileContent = json_encode($document['data'], JSON_PRETTY_PRINT);
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="invoice.json"');
        header('Content-Length: ' . strlen($fileContent));
        echo $fileContent;
        exit;
    } else {
        // Handle error
        echo "Failed to download document.";
    }
}
