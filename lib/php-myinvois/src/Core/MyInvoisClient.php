<?php

namespace MyInvois\Core;

class MyInvoisClient implements MyInvoisClientInterface
{
    private $config;
    private $accessToken;
    private $digitalSigner;

    public function __construct(array $config, DigitalSignerInterface $digitalSigner = null)
    {
        $this->config = $config;
        $this->digitalSigner = $digitalSigner;
    }

    private function getAccessToken()
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $url = $this->config['identity_server_url'];
        $ch = curl_init($url);

        $data = [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'grant_type' => 'client_credentials',
            'scope' => 'Invoicing.Read Invoicing.Write',
        ];

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            $data = json_decode($response, true);
            $this->accessToken = $data['access_token'];
            return $this->accessToken;
        } else {
            // Handle error
            return null;
        }
    }

    public function submitDocument(array $document, bool $isSigned = false): array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Failed to get access token'];
        }

        if ($isSigned) {
            if (!$this->digitalSigner) {
                return ['success' => false, 'message' => 'Digital signer is not configured'];
            }
            // Canonicalize the document to a JSON string
            $canonicalizedDocument = json_encode($document, JSON_UNESCAPED_SLASHES);
            $signature = $this->digitalSigner->sign($canonicalizedDocument);
            $document['signature'] = $signature;
        }

        $url = $this->config['base_url'] . '/api/v1.1/documents';
        $ch = curl_init($url);

        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($document));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 202) {
            $data = json_decode($response, true);
            return ['success' => true, 'submissionId' => $data['submissionUid']];
        } else {
            // Handle error
            return ['success' => false, 'message' => 'Failed to submit document', 'response' => json_decode($response, true)];
        }
    }

    public function getSubmission(string $submissionId): array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Failed to get access token'];
        }

        $url = $this->config['base_url'] . '/api/v1.0/submissions/' . $submissionId;
        $ch = curl_init($url);

        $headers = [
            'Authorization: Bearer ' . $accessToken,
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            return ['success' => true, 'data' => json_decode($response, true)];
        } else {
            // Handle error
            return ['success' => false, 'message' => 'Failed to get submission details', 'response' => json_decode($response, true)];
        }
    }

    public function getDocument(string $documentId): array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Failed to get access token'];
        }

        $url = $this->config['base_url'] . '/api/v1.0/documents/' . $documentId;
        $ch = curl_init($url);

        $headers = [
            'Authorization: Bearer ' . $accessToken,
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            return ['success' => true, 'data' => json_decode($response, true)];
        } else {
            // Handle error
            return ['success' => false, 'message' => 'Failed to get document', 'response' => json_decode($response, true)];
        }
    }
}
