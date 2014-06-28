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

## sendmailの設定

* 各自の環境でsendmailの設定を行うこと
* その後以下のコマンドで送信テスト
```
$ php oil r sendmail:test your-address@hoge.com
```

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

# OSS

fuelphp-1.8 http://fuelphp.jp/docs/1.8/license.html
jquery-1.10.2 https://jquery.org/license/
jquery-ui-1.10.4 https://github.com/jquery/jquery-ui/blob/master/MIT-LICENSE.txt
twitter bootstrap-3.0.3 https://github.com/twbs/bootstrap/blob/master/LICENSE
select2-3.4.5 https://github.com/ivaynberg/select2/blob/master/LICENSE
datepicker-2.0 https://github.com/eternicode/bootstrap-datepicker/blob/master/LICENSE 
datatable-1.10.0 http://datatables.net/license/mit

# Sendmail

* Fromには no-reply@bm-s.info を指定するようにする。
