<?php
function get_db($forceReconnect = false): PDO
{
    static $db;

    if (empty($db) || $forceReconnect) {
        $db = new PDO("mysql:host=mysql;dbname=Karma8", 'root', 'root');
    }
    return $db;
}

/**
 * Возвращает список юзеров для рассылки, у которых заканчивается подписка в указанный день
 * @param DateTime $deadline
 * @return int[]
 * @throws Exception
 */
function get_reminder_users(DateTime $deadline): array
{
    $sql = get_db()->prepare(
        'SELECT id FROM Karma8.Users WHERE 
        (validts BETWEEN ? and ?) 
        AND (validts > reminder_sent_at)
        AND (confirmed = 1 OR checked = 0 OR valid = 1)'
    );
    $result = $sql->execute(
        [
            $deadline->format('Y-m-d 00:00:00'),
            $deadline->format('Y-m-d 23:59:59'),
         ]
    );
    db_error_check($result);
    $users = $sql->fetchAll(PDO::FETCH_COLUMN);
    return $users;
}

function db_error_check(mixed $result)
{
    if ($result === false) {
        $errInfo =  get_db()->errorInfo();
        throw new Exception("PDO Error: ({$errInfo[0]}) {$errInfo[2]}");
    }
}

/**
 * @param int[] $userIds
 * @return void
 */
function fill_reminder_queue(array $userIds)
{
    $userIds = array_map('intval', $userIds);
    $userIds = array_filter($userIds);
    if (empty($userIds)) {
        return;
    }
    $result = get_db()->query('INSERT IGNORE INTO Karma8.ReminderQueue (user_id) VALUES (' . join('), (', $userIds) . ')');
    db_error_check($result);
    $result->closeCursor();
}

function get_queue(int $limit, int $maxErrCnt, \DateTime $nowDateTime): array
{
    $sql = get_db(true)->prepare(
        'SELECT * FROM Karma8.ReminderQueue 
         WHERE next_processing_time <= :now AND errcnt < :errcnt 
         ORDER BY next_processing_time
         LIMIT :limit'
    );
    $sql->bindParam('errcnt', $maxErrCnt, PDO::PARAM_INT);
    $sql->bindParam('limit', $limit, PDO::PARAM_INT);
    $date = $nowDateTime->format('Y-m-d H:i:s');
    $sql->bindParam('now', $date);
    $result = $sql->execute();
    db_error_check($result);
    return $sql->fetchAll(PDO::FETCH_ASSOC);
}

function mark_queue_entry(int $entryId): void
{
    $sql = get_db(true)->prepare(
        'UPDATE Karma8.ReminderQueue SET errcnt = errcnt + 1, next_processing_time = DATE_ADD(NOW(), INTERVAL 2^errcnt MINUTE) WHERE id = :id'
    );
    $sql->bindParam('id', $entryId, PDO::PARAM_INT);
    $result = $sql->execute();
    db_error_check($result);
    $sql->closeCursor();
}

function remove_queue_entry(int $entryId): void
{
    $sql = get_db(true)->prepare(
        'DELETE FROM Karma8.ReminderQueue WHERE id = :id'
    );
    $sql->bindParam('id', $entryId, PDO::PARAM_INT);
    $result = $sql->execute();
    db_error_check($result);
    $sql->closeCursor();
}

function get_user(int $userId): array
{
    $sql = get_db()->prepare(
        'SELECT * FROM Karma8.Users WHERE id = :id'
    );
    $sql->bindParam('id', $userId, PDO::PARAM_INT);
    $result = $sql->execute();
    db_error_check($result);
    return $sql->fetchAll(PDO::FETCH_ASSOC)[0] ?? [];
}

function set_user_valid(int $userId, bool $isValid): void
{
    $sql = get_db()->prepare(
        'UPDATE Karma8.Users SET valid = :valid, checked = 1 WHERE id = :id'
    );
    $sql->bindParam('id', $userId, PDO::PARAM_INT);
    $valid = (int)$isValid;
    $sql->bindParam('valid', $valid, PDO::PARAM_INT);
    $result = $sql->execute();
    db_error_check($result);
    $sql->closeCursor();
}

function set_reminder_sent(int $userId, \DateTime $sentAt): void
{
    $sql = get_db()->prepare(
        'UPDATE Karma8.Users SET reminder_sent_at = :sent_at WHERE id = :id'
    );
    $date = $sentAt->format('Y-m-d H:i:s');
    $sql->bindParam('sent_at', $date);
    $sql->bindParam('id', $userId, PDO::PARAM_INT);
    $result = $sql->execute();
    db_error_check($result);
    $sql->closeCursor();
}