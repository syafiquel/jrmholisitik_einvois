<?php

namespace MyInvois\Ubl;

interface UblBuilderInterface
{
    public function buildInvoice(array $data): array;

    public function buildCreditNote(array $data): array;

    public function buildDebitNote(array $data): array;
}
