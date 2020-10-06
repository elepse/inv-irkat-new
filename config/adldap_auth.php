<?php
return [
    'usernames' => [
        'ldap' => env('ADLDAP_USER_ATTRIBUTE', 'userprincipalname'), // was just 'userprincipalname'
        'eloquent' => 'username', // was 'email'
    ],

    'sync_attributes' => [
        // 'field_in_local_db' => 'attribute_in_ldap_server',
        'username' => 'samaccountname', // was 'email' => 'userprincipalname',
        'name' => 'cn',
        'memberOf' => 'memberof',
    ],
];
