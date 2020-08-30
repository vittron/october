<?php return [
    'plugin' => [
        'name' => 'sslchecker',
        'description' => '',
    ],
    'columns' => [
        'domain' => 'domain',
        'domains' => 'Domains',
        'inactive_domains' => 'Inaktiv Domains',
        'ipaddress' => 'IP Addresse',
        'valid_from' => 'Gültig seit',
        'valid_to' => 'Gültig bis',
        'diff' => 'Tage verbleibend',
        'active' => 'Aktiv',
        'ok' => 'Ok',
        'warning' => 'Warnung',
        'danger' => 'Achtung',
        'critical' => 'Kritisch',
    ],
    'list' => [
        'deactivate_button' => 'Ausgewähltes deaktivieren',
        'activate_button' => 'Ausgewähltes aktivieren',
        'deactivate_confirm' => 'Ausgewählte Datensätze deaktivieren?',
        'activate_confirm' => 'Ausgewählte Datensätze aktivieren?',
    ],
    'ssl' => [
        'empty_domain_list' => 'Keine Domains gefunden, bitte erstellen Sie einen Domaineintrag.',
        'check_completed_no_errors' => 'SSL-Prüfung abgeschlossen: Es wurden keine Fehler gefunden',
        'check_completed' => 'SSL-Prüfung abgeschlossen: ',
        'check_with_x_errors' => ' Domains ohne SSL gefunden',
        'unavailable' => 'NICHT VERFÜGBAR',
        'expired' => 'ABGELAUFEN',
    ],
];