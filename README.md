[![Build Status](https://travis-ci.org/tyoshii/bms.svg)](https://travis-ci.org/tyoshii/bms)

Baseball Management System
==========================

# REQUIRE

* php5.X
    * fuelphp 1.8
* mysql5.X

# メンテナンスモードの切り替え
```
php oil r service:out
php oil r service:in
```

* adminユーザーはメンテナンスモードでも画面を見ることが出来ます。

# 開発環境構築

https://github.com/tyoshii/bms/wiki/%E9%96%8B%E7%99%BA%E7%92%B0%E5%A2%83%E6%A7%8B%E7%AF%89

# Deploy

## production

* masterへpushするとgithubのweb-wookからapi/deployをコール
https://github.com/tyoshii/bms/blob/staging/fuel/app/classes/controller/api/deploy.php#L35-L61

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
