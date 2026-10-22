<?php

return [
    'title' => 'My appeals',

    'fields' => [
        'reason' => 'Reason',
    ],

    'statuses' => [
        'pending' => 'Pending',
        'reviewing' => 'Under review',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
    ],

    'create' => [
        'title' => 'Submit an appeal',
        'reason' => 'Reason for the appeal',
        'reason_placeholder' => 'Select a reason',
        'explanation' => 'Explain your situation',
        'submit' => 'Submit the appeal',
        'no_sanction_detected' => 'No active sanction could be detected automatically. You can still submit your appeal.',
        'multiple_sanctions' => 'You have several active sanctions. Choose the one you want to appeal.',
        'choose_sanction' => 'Sanction to appeal',
    ],

    'index' => [
        'create' => 'Submit an appeal',
        'view_active' => 'View my pending appeal',
        'blocked_until' => 'You can appeal again on :date',
        'no_appeals' => 'You have not submitted any appeal.',
    ],

    'show' => [
        'title' => 'Appeal details',
        'staff_response' => 'Staff response',
        'history' => 'History',
    ],

    'edit' => [
        'title' => 'Edit my appeal',
    ],

    'sanctions' => [
        'title' => 'Related sanction',
        'reason' => 'Reason',
        'staff' => 'Sanctioned by',
        'duration' => 'Duration',
        'permanent' => 'Permanent',
        'types' => [
            'ban' => 'Banned',
            'mute' => 'Muted',
        ],
        'unknown_reason' => 'Unknown reason',
        'unknown_staff' => 'Unknown staff',
        'unknown_date' => 'Unknown date',
        'unknown_duration' => 'Unknown duration',
    ],

    'errors' => [
        'already_active' => 'You already have an appeal being processed.',
        'too_soon' => 'You will be able to submit a new appeal on :date.',
        'no_active_sanction' => 'No active sanction was found on your account. You cannot submit an appeal.',
        'sanction_already_decided' => 'This sanction already had an appeal decided by the staff. You cannot contest it again.',
    ],

    'created' => 'Your appeal has been submitted.',
    'updated' => 'Your appeal has been updated.',

    'notifications' => [
        'pending' => 'Your appeal is waiting to be processed.',
        'reviewing' => 'Your appeal is under review.',
        'accepted' => 'Your appeal has been accepted.',
        'rejected' => 'Your appeal has been rejected.',
    ],

    'banner' => [
        'sanctioned' => 'You have an active sanction on this server.',
        'pending' => 'Your appeal is currently being processed.',
        'appeal' => 'Submit an appeal',
        'view' => 'View my appeal',
    ],
];
