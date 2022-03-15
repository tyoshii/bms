## Overview

- [Docker Compose入門 \(2\) ～ウェブサーバの開発環境を作るための準備～ \| さくらのナレッジ](https://knowledge.sakura.ad.jp/23632/)
- [docker\-compose を用いて Apache・PHP・MySQL の開発環境を構築してみた \- Qiita](https://qiita.com/sugurutakahashi12345/items/5daf89b2d33ef8d9fa2e)

### Case
- [レガシーなWebサービスをコンテナで動かすまでに考えたこと、およびちょっとしたプラクティス \- Qiita](https://qiita.com/j-un/items/6b622c6d0fe834033897)
- [レガシーシステムをDocker環境へ移行させた話 \- Finatext \- Medium](https://medium.com/finatext/migrating-a-legacy-system-to-a-docker-environment-3dacd99ef0ba)

### Tasks

1. dockr container

[Image Layer Details \- php:5\.6\-apache \- sha256:46d4ecf689a983cfc42f73abb435b0f67ad454964779d66b933b5d0949022fff \- Docker Hub](https://hub.docker.com/layers/php/library/php/5.6-apache/images/sha256-46d4ecf689a983cfc42f73abb435b0f67ad454964779d66b933b5d0949022fff?context=explore)

```bash
$ docker pull php:7.3-apache
```

1. Install docker-compose

[Docker Compose — Docker\-docs\-ja 17\.06 ドキュメント](https://docs.docker.jp/compose/toc.html)


1. Pull nginx docker container

[nginx \- Docker Hub](https://hub.docker.com/_/nginx)

```
$ docker pull nginx:1.19
```


----

```bash
$ php -r 'phpinfo();'
```

see: https://qiita.com/nokachiru/items/a2146a2f49eb5c98896c


### Install php asdf plugin

```
$ PHP_WITHOUT_PEAR=yes asdf install php  7.3.25
```

- [asdf\-community/asdf\-php: PHP plugin for the asdf version manager](https://github.com/asdf-community/asdf-php)



### apache mod_rewrite

- [mod\_rewrite \- Apache HTTP Server Version 2\.4](http://httpd.apache.org/docs/2.4/mod/mod_rewrite.html)
- [Apache2\.4でのRewriteLogの有効化 \- めじなてっく](http://mezina1942.hatenablog.com/entry/2017/09/18/224503)
