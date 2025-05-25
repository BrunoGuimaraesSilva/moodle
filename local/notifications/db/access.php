<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/notifications:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
        ]
    ],
    'local/notifications:manage' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
        ]
    ],
];
