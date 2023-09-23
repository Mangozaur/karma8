<?php
function check_email(string $email): int
{
    sleep(rand(1, 60));
    return rand(0, 1);
}