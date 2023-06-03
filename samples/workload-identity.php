<?php

require_once 'vendor/autoload.php';

$_SERVER['AZURE_TENANT_ID'] = 'f7ee1891-8c7e-487d-9267-ea5fb5b3a5bc';
$_SERVER['AZURE_CLIENT_ID'] = 'ca76a582-01ea-4c6c-8249-669f79883f2e';
$_SERVER['AZURE_FEDERATED_TOKEN_FILE'] = 'sample/azure-identity-token';

use Azure\Identity\Credential\DefaultAzureCredential;

$credential = new DefaultAzureCredential();

$token = $credential->getToken(['https://servicebus.azure.net//.default']);
var_dump($token);
