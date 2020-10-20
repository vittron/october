<?php return [
    'plugin' => [
        'name' => 'sslchecker',
        'description' => '',
    ],
    'columns' => [
        'domain' => 'Verkkotunnus',
        'domains' => 'verkkotunnukset',
        'inactive_domains' => 'Inaktiv verkkotunnukset',
        'ipaddress' => 'IP Osoite',
        'valid_from' => 'Pätevä ajasta',
        'valid_to' => 'Pätevä aikaan',
        'diff' => 'Päiviä jäljellä',
        'active' => 'Toimiva',
        'ok' => 'Ok',
        'warning' => 'Varoitus',
        'danger' => 'Vaara',
        'critical' => 'Kriittinen',
    ],
    'list' => [
        'deactivate_button' => 'feactivoi valitut',
        'activate_button' => 'activoi valitut',
        'deactivate_confirm' => 'deactivoi valitut arkistot?',
        'activate_confirm' => 'activoi valitut arkistot?',
    ],
    'ssl' => [
        'empty_domain_list' => 'Verkkotunnuksia ei löytynyt, kiitos luo verkkotunnus.',
        'check_completed_no_errors' => 'SSL Tarkistus valmis: Ei virheitä löytynyt',
        'check_completed' => 'SSL Tarkistus valmis: ',
        'check_with_x_errors' => ' verkkotunnuksia löytynyt ilman SSL',
        'unavailable' => 'EI SAATAVILLA',
        'expired' => 'UMPEUTUNUT',
    ],
];