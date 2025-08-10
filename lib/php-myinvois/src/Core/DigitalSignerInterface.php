<?php

namespace MyInvois\Core;

interface DigitalSignerInterface
{
    public function sign(string $dataToSign): string;
}
