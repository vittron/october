<?php return [
    'plugin' => [
        'name' => 'sslchecker',
        'description' => '',
    ],
    'columns' => [
        'domain' => 'Domain',
        'domains' => 'Domains',
        'inactive_domains' => 'Inactive Domains',
        'ipaddress' => 'IP Address',
        'valid_from' => 'Valid Since',
        'valid_to' => 'Valid Till',
        'diff' => 'Days left',
        'active' => 'Active',
        'ok' => 'Ok',
        'warning' => 'Warning',
        'danger' => 'Danger',
        'critical' => 'Critical',
        'last_check_date' => 'Last Check',
    ],
    'list' => [
        'deactivate_button' => 'Deactivate selected',
        'activate_button' => 'Activate selected',
        'deactivate_confirm' => 'deactivate selected records?',
        'activate_confirm' => 'activate selected records?',
    ],
    'ssl' => [
        'empty_domain_list' => 'No domains found, please create a domain entry.',
        'check_completed_no_errors' => 'SSL Check complete: No error\'s found',
        'check_completed' => 'SSL Check complete: ',
        'check_with_x_errors' => ' domains found without SSL',
        'unavailable' => 'UNAVAILABLE',
        'expired' => 'EXPIRED',
    ],
    'settings' => [
        'datamethode' => [
            'label' => 'Data Methode',
            'comment' => 'Select wether the plugin should check all records or only active records.',
        ],
        'jobrepeater' => [
            'label' => 'Job repeater',
            'comment' => 'Select when the job has to be executed, default every 24 hours.',
        ],
    ],
];