<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SAML idP configuration file
    |--------------------------------------------------------------------------
    |
    | Use this file to configure the service providers you want to use.
    |
     */
    // Outputs data to your laravel.log file for debugging
    'debug' => false,
    // Define the email address field name in the users table
    'email_field' => 'email',
    // The URI to your login page
    'login_uri' => 'login',
    // Log out of the IdP after SLO
    'logout_after_slo' => env('LOGOUT_AFTER_SLO', false),
    // The URI to the saml metadata file, this describes your idP
    'issuer_uri' => 'saml/metadata',
    // Name of the certificate PEM file
    'certname' => 'cert.pem',
    // Name of the certificate key PEM file
    'keyname' => 'key.pem',
    // Encrypt requests and reponses
    'encrypt_assertion' => true,
    // Make sure messages are signed
    'messages_signed' => true,
    // list of all service providers
    'sp' => [
        // Base64 encoded ACS URL
        // 'aHR0cHM6Ly9teWZhY2Vib29rd29ya3BsYWNlLmZhY2Vib29rLmNvbS93b3JrL3NhbWwucGhw' => [
        //     // Your destination is the ACS URL of the Service Provider
        //     'destination' => 'https://myfacebookworkplace.facebook.com/work/saml.php',
        //     'logout' => 'https://myfacebookworkplace.facebook.com/work/sls.php',
        //     'certificate' => '',
        //     'query_params' => false
        // ]
        'aHR0cDovL2xvY2FsaG9zdDo1Njg5L3NhbWwyL2h1ZHVtYWtlbnlhL2Fjcw==' => [
            'destination' => 'http://localhost:5689/saml2/hudumakenya/acs',
            'certificate' => '-----BEGIN CERTIFICATE-----
MIIDxTCCAq2gAwIBAgIUBgg4m6pRb+3UBruNcPbVIVrgq0kwDQYJKoZIhvcNAQEL
BQAwcjELMAkGA1UEBhMCS0UxEDAOBgNVBAgMB05haXJvYmkxEDAOBgNVBAcMB05h
aXJvYmkxFTATBgNVBAoMDEh1ZHVtYSBLZW55YTEMMAoGA1UECwwDSUNUMRowGAYD
VQQDDBFodWR1bWFrZW55YS5nby5rZTAeFw0yMDEyMTQxMTUzNDVaFw00MDEyMDkx
MTUzNDVaMHIxCzAJBgNVBAYTAktFMRAwDgYDVQQIDAdOYWlyb2JpMRAwDgYDVQQH
DAdOYWlyb2JpMRUwEwYDVQQKDAxIdWR1bWEgS2VueWExDDAKBgNVBAsMA0lDVDEa
MBgGA1UEAwwRaHVkdW1ha2VueWEuZ28ua2UwggEiMA0GCSqGSIb3DQEBAQUAA4IB
DwAwggEKAoIBAQDXx5Kg8WL7eU30JbeM/Jf9ADl8UzuWLwjfrdJaJGD/P9f8I7se
N+qI3OxlwkSTygBKKmxhTUZXHctxKjtgz+zJF6vEXXIK18aAntybALuw5UacAWkN
P6KWyie/13sNmWbXqHEWjhj4lNRK0wGkbfhld79SO09+avhv2WrJlWtkC5eKn4b4
vtaBevzx33t5SBdUbvtsqmIKoIb/yIIZuSarQMEa7etQeuQkC1/ccALEkiMbKdL0
n/teP5RDLeVfOgr82Ik1YsGbnxek7m0BaLIzKTV8xRpcCWRizjVpaF7fjfOc1sLx
ZWLxjwfysc5ldHHj88q10APVVrEw8/JVvhYVAgMBAAGjUzBRMB0GA1UdDgQWBBTx
nxf63RG8F9rprJ5zII27RnxwmDAfBgNVHSMEGDAWgBTxnxf63RG8F9rprJ5zII27
RnxwmDAPBgNVHRMBAf8EBTADAQH/MA0GCSqGSIb3DQEBCwUAA4IBAQBZCZ8xoeCO
rK80gCfBYEZOMUsfIBTNZZiKKbSMzauasdPU5Fhm4nedRXkZDpcTjEIMuTdyafK8
KKQn+Pip1/bLS7rFZ0cGIFqKasQ1owNcpnFFOujgWdL7tOHSJZZQafr9wDBLQVqi
8TOesTQ3+HqJFZ6oYS8h3fuMDQet/4VU8Rd2CDwWcvpw0d3z3iuBMvHMxpSNypHC
CeflwPFYMkb4tgeB0kkfK3ET2Grw5MR+bYc/vQAQV51beDQC/kCLctkyK83izUXl
ZkWIZndSmfxGdzSlhfI5pY0m74B7BC+3QBfionVv7k3CSOUZiJAhrbQ4wHZLYesw
0yFwbRoLQpaO
-----END CERTIFICATE-----',
            'logout' => 'http://localhost:5689/saml2/hudumakenya/sls',
        ]
    ],

    // If you need to redirect after SLO depending on SLO initiator
    // key is beginning of HTTP_REFERER value from SERVER, value is redirect path
    'sp_slo_redirects' => [
        // 'https://example.com' => 'https://example.com',
    ],

    // List of guards saml idp will catch Authenticated, Login and Logout events 
    'guards' => ['web']
];
