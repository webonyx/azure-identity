# azure-identity

Azure Active Directory token authentications in PHP

Implemented solutions:

* Client Secret Credential: Local development
* Workload Identity Credential: Azure Kubernetes


## Usage

**Service Principle**

```php
<?php

require_once 'vendor/autoload.php';

// az ad sp create-for-rbac --sdk-auth --name 'azure-identity-dev'

$_SERVER['AZURE_TENANT_ID'] = 'f7ee1891-8c7e-487d-9267-ea5fb5b3a5bc';
$_SERVER['AZURE_CLIENT_ID'] = 'ca76a582-01ea-4c6c-8249-669f79883f2e';
//$_SERVER['AZURE_CLIENT_SECRET'] = 'REDACTED'; Must be pre-set from environment

use Azure\Identity\Credential\DefaultAzureCredential;
use Azure\Identity\Exception\CredentialUnavailableException;

$credential = new DefaultAzureCredential();

try {
    $token = $credential->getToken(['https://servicebus.azure.net//.default']);
    var_dump($token);
} catch (CredentialUnavailableException $exception) {
    print_r([
        'message' => $exception->getMessage(),
        'location' => sprintf('%s:%d', $exception->getFile(), $exception->getLine()),
    ]);
}
```

**Workload Identity**

```php
$_SERVER['AZURE_TENANT_ID'] = 'f7ee1891-8c7e-487d-9267-ea5fb5b3a5bc';
$_SERVER['AZURE_CLIENT_ID'] = 'ca76a582-01ea-4c6c-8249-669f79883f2e';
$_SERVER['AZURE_FEDERATED_TOKEN_FILE'] = 'sample/azure-identity-token';

use Azure\Identity\Credential\DefaultAzureCredential;
use Azure\Identity\Exception\CredentialUnavailableException;

$credential = new DefaultAzureCredential();

try {
    $token = $credential->getToken(['https://servicebus.azure.net//.default']);
    var_dump($token);
} catch (CredentialUnavailableException $exception) {
    print_r([
        'message' => $exception->getMessage(),
        'location' => sprintf('%s:%d', $exception->getFile(), $exception->getLine()),
    ]);
}
```
