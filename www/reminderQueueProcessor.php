<?php
require_once 'inc/check_email.php';
require_once 'inc/send_mail.php';

function processEntry(int $userId): void
{
    get_db(true);
    $user = get_user($userId);
    if (!$user) {
        throw new \Exception("No user #$userId found");
    }

    if (!$user['checked']) {
        $isValid = check_email($user['checked']);
        set_user_valid($userId, $isValid);
        if (!$isValid) {
            return;
        }
    }

    send_mail('reminder@karma8.com', $user['email'], "Hello {$user['username']}!\nYour subscription will be ended at " . $user['validts']);
    set_reminder_sent($userId, new DateTime());
}