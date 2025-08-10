<?php

namespace MyInvois\Core;

interface MyInvoisClientInterface
{
    public function submitDocument(array $document, bool $isSigned = false): array;

    public function getSubmission(string $submissionId): array;

    public function getDocument(string $documentId): array;
}
