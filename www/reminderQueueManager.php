<?php
require_once 'inc/db.php';
require_once 'reminderQueueProcessor.php';

const LOCK_PATH = '/local/www/lock';
const MAX_BATCH = 10;
const MAX_THREADS = 10;
const MAX_ERRCNT = 5; // approx 1h total delay

// multilaunch protection
$fp = fopen(LOCK_PATH, "w+");
if (!flock($fp, LOCK_EX)) {
    die("Can't get lock");
}

$count = 0;
$now = new DateTime();

while ($queue = get_queue(MAX_BATCH, MAX_ERRCNT, $now)) {
    foreach ($queue as $entry) {
        mark_queue_entry($entry['id']);
        $pid = pcntl_fork();
        if ($pid == -1) {
            $errCode = pcntl_get_last_error();
            $errMsg = pcntl_strerror($errCode);
            die("pcntl error: ($errCode) $errMsg");
        } else if ($pid) {
            // parent
            $count++;
            if ($count >= MAX_THREADS) {
                $pid = pcntl_wait($status);
            }
        } else {
            // child
            processEntry($entry['user_id']);
            remove_queue_entry($entry['id']);
            return;
        }
    }
}

while ($count) {
    $pid = pcntl_wait($status);
    $count--;
}
