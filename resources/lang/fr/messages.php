<?php

return [
    'title' => 'Mes demandes de recours',

    'fields' => [
        'reason' => 'Motif',
    ],

    'statuses' => [
        'pending' => 'En attente',
        'reviewing' => 'À l\'étude',
        'accepted' => 'Acceptée',
        'rejected' => 'Refusée',
    ],

    'create' => [
        'title' => 'Déposer une demande de recours',
        'reason' => 'Motif de la demande',
        'reason_placeholder' => 'Choisissez un motif',
        'explanation' => 'Expliquez votre situation',
        'submit' => 'Envoyer la demande',
        'no_sanction_detected' => 'Aucune sanction active n\'a été détectée automatiquement. Vous pouvez tout de même déposer votre demande.',
        'multiple_sanctions' => 'Vous avez plusieurs sanctions actives. Choisissez celle que vous souhaitez contester.',
        'choose_sanction' => 'Sanction à contester',
    ],

    'index' => [
        'create' => 'Déposer une demande',
        'view_active' => 'Voir ma demande en cours',
        'blocked_until' => 'Nouvelle demande possible le :date',
        'no_appeals' => 'Vous n\'avez déposé aucune demande.',
    ],

    'show' => [
        'title' => 'Détail de la demande',
        'staff_response' => 'Réponse du staff',
    ],

    'edit' => [
        'title' => 'Modifier ma demande',
    ],

    'sanctions' => [
        'title' => 'Sanction concernée',
        'reason' => 'Raison',
        'staff' => 'Sanctionné par',
        'duration' => 'Durée',
        'permanent' => 'Permanente',
        'types' => [
            'ban' => 'Banni',
            'mute' => 'Muet',
        ],
        'unknown_reason' => 'Raison inconnue',
        'unknown_staff' => 'Staff inconnu',
        'unknown_date' => 'Date inconnue',
        'unknown_duration' => 'Durée inconnue',
    ],

    'errors' => [
        'already_active' => 'Vous avez déjà une demande en cours de traitement.',
        'too_soon' => 'Vous pourrez déposer une nouvelle demande le :date.',
        'no_active_sanction' => 'Aucune sanction active n\'a été trouvée sur votre compte. Vous ne pouvez pas déposer de recours.',
        'sanction_already_decided' => 'Cette sanction a déjà fait l\'objet d\'un recours tranché par le staff. Vous ne pouvez pas la contester à nouveau.',
    ],

    'created' => 'Votre demande a bien été envoyée.',
    'updated' => 'Votre demande a bien été mise à jour.',

    'notifications' => [
        'pending' => 'Votre demande de recours est en attente de traitement.',
        'reviewing' => 'Votre demande de recours est à l\'étude.',
        'accepted' => 'Votre demande de recours a été acceptée.',
        'rejected' => 'Votre demande de recours a été refusée.',
    ],
];
