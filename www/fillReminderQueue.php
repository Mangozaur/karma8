<?php
require_once 'inc/db.php';

if (!isset($argv[1])) {
    die('Syntax: ' . __FILE__ . " <days_before_expiration>\n");
}
$days = (int)$argv[1];
if ($days < 0) {
    die("Notification for the past is not supported\n");
}

$deadline = new DateTime("+$days days");
$users = get_reminder_users($deadline);

$parts = array_chunk($users, 500);
foreach ($parts as $part) {
    fill_reminder_queue($part);
}
