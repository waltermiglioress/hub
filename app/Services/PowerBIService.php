<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;


class PowerBIService
{


    protected $clientId;
    protected $clientSecretId;
    protected $tenantId;

    /**
     * @return mixed
     */
    public function __construct()
    {
        $this->clientId = env('powerbi_client_id');
        $this->clientSecretId = env('powerbi_client_secret');
        $this->tenantId = env('powerbi_tenant_id');
    }

    public function getAccessToken()
    {

        $client = new Client();
        $response = $client->post("https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token", [

            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecretId,
                'scope' => 'https://analysis.windows.net/powerbi/api/.default',
            ],

        ]);
        $body = json_decode((string)$response->getBody(), true);
        return $body['access_token'];

    }


    public function queryTable($datasetId, $tableName)
    {
        $accessToken = $this->getAccessToken();
//groups/a2903a95-48c0-4232-83c8-2009ac07ab5e
        $client = new Client();
        try {
            $response = $client->post("https://api.powerbi.com/v1.0/myorg/groups/a2903a95-48c0-4232-83c8-2009ac07ab5e/datasets/{$datasetId}/executeQueries", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'queries' => [
                        [
                            'query' => "EVALUATE VALUES({$tableName})"
                        ]
                    ],
                    'serializerSettings' => [
                        'includeNulls' => false
                    ]
                ],
            ]);

            $body = json_decode((string)$response->getBody(), true);
            return $body;

        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $headers = $e->getResponse()->getHeaders();
            $body = (string) $e->getResponse()->getBody();

            dd([
                'status' => $statusCode,
                'headers' => $headers,
                'body' => $body
            ]);
        }

    }

    public function getReportEmbedToken($reportId, $groupId)
    {
        $accessToken = $this->getAccessToken();

        $client = new Client();
        $response = $client->post("https://api.powerbi.com/v1.0/myorg/groups/{$groupId}/reports/{$reportId}/GenerateToken", [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'accessLevel' => 'View',
            ],
        ]);

        $body = json_decode((string)$response->getBody(), true);
        return $body['token'];
    }

}
