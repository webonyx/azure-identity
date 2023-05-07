<?php

namespace Azure\Utils;

interface Constants
{
    const AZURE_PUBLIC_CLOUD = 'https://login.microsoftonline.com';
    const DEFAULT_AUTHORITY_HOST = 'login.microsoftonline.com';
    const AAD_INSTANCE_DISCOVERY_ENDPT = 'https://login.microsoftonline.com/common/discovery/instance?api-version=1.1&authorization_endpoint=';
    const AAD_TENANT_DOMAIN_SUFFIX = '.onmicrosoft.com';
    const INVALID_INSTANCE = 'invalid_instance';
    const OIDC_DEFAULT_SCOPES = [
        'openid',
        'profile',
        'offline_access'
    ];

    const OIDC_SCOPES = [
        ...self::OIDC_DEFAULT_SCOPES,
        'email',
    ];
}