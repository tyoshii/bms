Baseball Management System
==========================

REQUIRE
=======

php5.5


RELEASE
=======

    # git clone and update submodule
    $ git clone --recursive https://github.com:tyoshii/bms
    $ cd bms
    $ git submodule update

    # copy config file ( and edit )
    $ cp fuel/app/config/_password.php fuel/app/config/password.php
    $ cp fuel/app/config/_salt.php     fuel/app/config/salt.php
    $ cp fuel/app/config/_crypt.php    fuel/app/config/crypt.php

    # database setting
    # - for development
    $ vi fuel/app/config/development/db.php

    # - for production
    $ vi fuel/app/config/production/db.php

    # httpd conf copy and edit
    $ cp conf/virtualhost-bms.conf ${HTTP_CONF_ROOT}/
        ## edit settings:
        ### ServerName
        ### DocumentRoot
        ### SetEnv FUEL_ENV

    # deploy only production ( mkdir/copy/symlink dir/file )
    $ cp deploy
    $ sudo perl deploy.pl 

CAUTION
=======

check it:
    apache user

IGNORE FILES
============

    fuel/app/config/salt.php
    fuel/app/config/password.php
    fuel/app/config/crypt.php
