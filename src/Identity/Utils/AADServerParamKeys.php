<?php

namespace Azure\Identity\Utils;

interface AADServerParamKeys
{
    const CLIENT_ID = 'client_id';
    const GRANT_TYPE = 'grant_type';
    const CLAIMS = 'claims';
    const SCOPE = 'scope';
    const CLIENT_ASSERTION = 'client_assertion';
    const CLIENT_ASSERTION_TYPE = 'client_assertion_type';
    const TOKEN_TYPE = 'token_type';
}