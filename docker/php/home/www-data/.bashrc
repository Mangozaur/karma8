export APP_ENV='local'
alias artisan='php /local/www/artisan'

export PS1="\[\033[m\]\[\033[36m\]\u\[\033[m\]@\[\033[36m\]pantheon.dev\[\033[m\]:\[\033[34m\]\w\[\033[m\] "
export PATH="/var/www/.composer/vendor/bin:$PATH"

function checkService()
{
    if (( $(ps -ef | grep -v grep | grep $1 | wc -l) > 0 ))
    then
        :
    else
        echo -e "$1 \033[91mDOWN\033[0m"
    fi
}

#checkService "mysql"
#checkService "memcache"

# PHPUNIT
punit() { cd /local/www;php vendor/bin/phpunit "$@"; }
alias pu=punit
alias pu.all='cd /local/www;php vendor/bin/phpunit'
alias pu.func='cd /local/www;php vendor/bin/phpunit --testsuite=Feature'
alias pu.unit='cd /local/www;php vendor/bin/phpunit --testsuite=Unit'

cd /local/www/