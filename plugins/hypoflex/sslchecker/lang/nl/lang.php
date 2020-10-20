<?php return [
    'plugin' => [
        'name' => 'sslchecker',
        'description' => '',
    ],
    'columns' => [
        'domain' => 'Domein',
        'domains' => 'Domeinen',
        'inactive_domains' => 'Inactieven Domeinen',
        'ipaddress' => 'IP Adres',
        'valid_from' => 'Geldig sinds',
        'valid_to' => 'Geldig tot',
        'diff' => 'Resterend',
        'active' => 'Actief',
        'ok' => 'Oke',
        'warning' => 'Waarschuwing',
        'danger' => 'Gevaarlijk',
        'critical' => 'Kritiek',
        'last_check_date' => 'Laatste Check',
    ],
    'list' => [
        'deactivate_button' => 'Deactiveer geselecteerde',
        'activate_button' => 'Activeer geselecteerde',
        'deactivate_confirm' => 'Geselecteerde items deactiveren?',
        'activate_confirm' => 'Geselecteerde items activeren?',
    ],
    'ssl' => [
        'empty_domain_list' => 'Geen domein gevonden, maak eerst een domein aan.',
        'check_completed_no_errors' => 'SSL Check compleet: geen fouten gevonden',
        'check_completed' => 'SSL Check compleet: ',
        'check_with_x_errors' => ' domeinen gevonden zonder SSL',
        'unavailable' => 'NIET BESCHIKBAAR',
        'expired' => 'VERLOPEN',
    ],
    'settings' => [
        'datamethode' => [
            'label' => 'Data Methode',
            'comment' => 'Selecteer of de plugin alle regels moet scannen of alleen actieve.',
        ],
        'jobrepeater' => [
            'label' => 'Opdracht herhalen',
            'comment' => 'Selecteer wanneer de taak opnieuw moet worden uitgevoerd, Standaard elke 24 uur.',
        ],
    ],
];