<?php

namespace MyInvois\Core;

class DigitalSigner implements DigitalSignerInterface
{
    private $privateKey;
    private $certificate;

    public function __construct(string $privateKeyPath, string $certificatePath)
    {
        $this->privateKey = file_get_contents($privateKeyPath);
        $this->certificate = file_get_contents($certificatePath);
    }

    public function sign(string $dataToSign): string
    {
        $pKey = openssl_pkey_get_private($this->privateKey);
        openssl_sign($dataToSign, $signature, $pKey, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }
}
