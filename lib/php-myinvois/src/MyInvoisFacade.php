<?php

namespace MyInvois;

use MyInvois\Core\MyInvoisClientInterface;
use MyInvois\Ubl\UblBuilderInterface;

class MyInvoisFacade
{
    protected $client;
    protected $ublBuilder;

    public function __construct(MyInvoisClientInterface $client, UblBuilderInterface $ublBuilder)
    {
        $this->client = $client;
        $this->ublBuilder = $ublBuilder;
    }

    public function submitInvoice(array $invoiceData, bool $sign = false): array
    {
        // Implementation to come
        return [];
    }

    public function getDownloadUrl(string $submissionId): string
    {
        $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return 'http://' . $domain . '/lib/php-myinvois/download.php?submission_id=' . $submissionId;
    }

    public function generateQrCode(string $url): string
    {
        require_once __DIR__ . '/../lib/qrcode.php';
        $qr = new \QRCode($url);
        ob_start();
        $qr->output_image();
        $imageData = ob_get_contents();
        ob_end_clean();
        return base64_encode($imageData);
    }
}
