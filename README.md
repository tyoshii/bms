Baseball Management System
==========================

# REQUIRE

* php5.X
    * fuelphp 1.8
* mysql5.X

# メンテナンスモードの切り替え
* php oil r service:out
* php oil r service:in

* adminユーザーはメンテナンスモードでも画面を見ることが出来ます。

# 開発環境構築

## git clone and update submodule

    $ git clone --recursive https://github.com:tyoshii/bms
    $ cd bms
    $ git submodule init
    $ git submodule update

## copy config file ( and edit appropriate value)
    $ cp fuel/app/config/_password.php fuel/app/config/password.php
    $ cp fuel/app/config/_salt.php     fuel/app/config/salt.php
    $ cp fuel/app/config/_crypt.php    fuel/app/config/crypt.php

## database setting
    
### for development
    $ vi fuel/app/config/development/db.php

### for production
    $ vi fuel/app/config/production/db.php

## httpd conf copy and edit
    $ cp conf/virtualhost-bms.conf ${HTTP_CONF_ROOT}/
        ## edit settings follow:
        ### ServerName
        ### DocumentRoot
        ### SetEnv FUEL_ENV

## composer
    $ php composer.phar update

## database
    $ mysqladmin create -u root -p bms
    $ php oil r migrate:current
    $ php oil refine dbinit:batter_result

## add admin account
    $ php oil console
    >>> Auth::create_user('admin', 'password', 'admin@yahoo.co.jp', 100);
    

# Deploy

## production

* masterへpushするとgithubのweb-wookからapi/deployをコール
* api/deployが以下を実施
    * git pull origin master
    * php oil r migrate:current
    * deploy.pl の実行
        $ cd deploy
        $ perl deploy.pl bms.list
* 自力Deployは上記コマンドを本番機で実施
