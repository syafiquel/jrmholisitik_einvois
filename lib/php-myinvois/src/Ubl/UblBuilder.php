<?php

namespace MyInvois\Ubl;

class UblBuilder implements UblBuilderInterface
{
    public function buildInvoice(array $data): array
    {
        // This is a simplified builder. In a real-world scenario, you would have
        // a more robust implementation that validates the data and handles all the
        // complexities of the UBL standard.
        return [
            'ubl' => '2.1',
            'id' => $data['invoice']['id'],
            'issue_date' => $data['invoice']['issue_date'],
            'due_date' => $data['invoice']['due_date'],
            'invoice_type_code' => $data['invoice']['invoice_type_code'],
            'supplier' => $data['supplier'],
            'customer' => $data['customer'],
            'lines' => $data['lines'],
            'tax_total' => $data['tax_total'],
            'legal_monetary_total' => $data['legal_monetary_total'],
        ];
    }

    public function buildCreditNote(array $data): array
    {
        // Implementation to come
        return [];
    }

    public function buildDebitNote(array $data): array
    {
        // Implementation to come
        return [];
    }
}
