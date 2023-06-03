<?php

require_once 'vendor/autoload.php';

// az ad sp create-for-rbac --sdk-auth --name 'azure-identity-dev'

$_SERVER['AZURE_TENANT_ID'] = 'f7ee1891-8c7e-487d-9267-ea5fb5b3a5bc';
$_SERVER['AZURE_CLIENT_ID'] = 'ca76a582-01ea-4c6c-8249-669f79883f2e';
//$_SERVER['AZURE_CLIENT_SECRET'] = 'REDACTED'; Must be pre-set from environment

use Azure\Identity\Credential\DefaultAzureCredential;

$credential = new DefaultAzureCredential();

$token = $credential->getToken(['https://servicebus.azure.net//.default']);
var_dump($token);
