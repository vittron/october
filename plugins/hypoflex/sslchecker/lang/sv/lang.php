<?php return [
    'plugin' => [
        'name' => 'sslchecker',
        'description' => '',
    ],
    'columns' => [
        'domain' => 'Domän',
        'domains' => 'Domän',
        'inactive_domains' => 'Inaktiv Domän',
        'ipaddress' => 'IP Address',
        'valid_from' => 'Giltig Sedan',
        'valid_to' => 'Giltig Till',
        'diff' => 'Dagar kvar',
        'active' => 'Aktiv',
        'ok' => 'Ok',
        'warning' => 'Varning',
        'danger' => 'Fara',
        'critical' => 'Kritisk',
    ],
    'list' => [
        'deactivate_button' => 'Avaktivera valet',
        'activate_button' => 'Aktivera valet',
        'deactivate_confirm' => 'Avaktivera valda uppgifter?',
        'activate_confirm' => 'Aktivera valda uppgifter?',
    ],
    'ssl' => [
        'empty_domain_list' => 'Ingen domän funnen, vänligen skapa en domänregistrering.',
        'check_completed_no_errors' => 'SSL Kontroll avslutad: Inga fel hittades',
        'check_completed' => 'SLL Kontroll avslutad: ',
        'check_with_x_errors' => ' domäner funna utan SSL',
        'unavailable' => 'INTE TILLGÄNGLIG',
        'expired' => 'UTGÅNGEN',
    ],
];