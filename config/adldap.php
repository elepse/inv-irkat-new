<?php
 return [
     'connections' => [
         'default' => [
             'auto_connect' => false,
             'connection' => Adldap\Connections\Ldap::class,
             'schema' => Adldap\Schemas\ActiveDirectory::class, // Adldap\Schemas\OpenLDAP::class, // was
             'connection_settings' => [
                 'account_prefix' => env('ADLDAP_ACCOUNT_PREFIX', ''),
                 'account_suffix' => env('ADLDAP_ACCOUNT_SUFFIX', 'iat.iat'),
                 'domain_controllers' => explode(' ', env('ADLDAP_CONTROLLERS', '10.100.3.1')),
                 'port' => env('ADLDAP_PORT', 389),
                 'timeout' => env('ADLDAP_TIMEOUT', 5),
                 'base_dn' => env('ADLDAP_BASEDN', 'dc=IAT,dc=IAT'),
                 'follow_referrals' => true,
                 'use_ssl' => false,
                 'use_tls' => false,
             ],
         ],
     ],
 ];