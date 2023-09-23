<?php
function send_mail(string $from, string $to, string $text): bool
{
//    return mail($to, 'Subscription reminder', $text, "FROM: $from\r\n");
    sleep(rand(1, 10));
    return true;
}