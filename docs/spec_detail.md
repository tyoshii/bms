# BMS（野球成績管理システム）詳細版仕様書

**バージョン:** 1.0
**作成日:** 2026-03-02
**調査基準コミット:** `a02c61d`
**対象URL:** http://b-ms.info
**リポジトリ:** https://github.com/tyoshii/bms

---

## 目次

1. [システム概要](#1-システム概要)
2. [技術スタック](#2-技術スタック)
3. [システムアーキテクチャ](#3-システムアーキテクチャ)
4. [プロジェクト構成](#4-プロジェクト構成)
5. [データベース設計](#5-データベース設計)
6. [機能仕様](#6-機能仕様)
7. [API仕様](#7-api仕様)
8. [認証・認可](#8-認証認可)
9. [画面仕様](#9-画面仕様)
10. [メール通知](#10-メール通知)
11. [データエクスポート](#11-データエクスポート)
12. [インフラ・デプロイ構成](#12-インフラデプロイ構成)
13. [CI/CD パイプライン](#13-cicd-パイプライン)
14. [設定・環境管理](#14-設定環境管理)
15. [セキュリティ](#15-セキュリティ)

---

## 1. システム概要

### 1.1 目的

BMS（Baseball Management System）は、草野球（アマチュア野球）チームを対象とした**Webベースの野球成績管理システム**です。選手個人が自分の試合成績を入力し、チーム・大会単位での統計を自動集計・閲覧することを目的としています。

### 1.2 主要ユースケース

| ユースケース | 概要 |
|---|---|
| チーム運営 | チームを作成し、メンバーを管理する |
| 試合記録 | 試合を登録し、スコアと参加選手を記録する |
| 成績入力 | 選手が自分の打撃・投球成績を入力する |
| 統計閲覧 | 個人・チーム・大会の成績統計を閲覧する |
| 大会管理 | 複数チームが参加するリーグ・大会を運営する |
| 管理者操作 | ユーザー・チーム・システム設定を管理する |

### 1.3 対象ユーザー

- **草野球チームのメンバー** - 自分の成績を入力・閲覧する一般ユーザー
- **チームの管理者** - チームの試合・メンバーを管理するチーム管理者
- **大会主催者** - リーグ・大会を管理する管理者
- **システム管理者** - アプリケーション全体を管理する管理者

---

## 2. 技術スタック

### 2.1 バックエンド

| 項目 | バージョン | 用途 |
|---|---|---|
| PHP | 5.6+ | サーバーサイド言語 |
| FuelPHP | 1.7 | MVCフレームワーク |
| MySQL | 5.6 | リレーショナルデータベース |
| Fuel ORM | - | オブジェクトリレーショナルマッパー |
| SimpleAuth | - | 認証パッケージ |
| Opauth | - | OAuth認証（Google/Facebook） |
| PHPExcel | 1.7.x | Excelファイル生成 |
| Monolog | 1.5.x | ログ管理 |
| PHPSecLib | 2.0.0 | 暗号化ライブラリ |

### 2.2 フロントエンド

| 項目 | バージョン | 用途 |
|---|---|---|
| Twig | 1.40.0 | テンプレートエンジン |
| Twitter Bootstrap | - | UIフレームワーク |
| jQuery | - | DOM操作・Ajax |
| Select2 | - | 検索可能なセレクトボックス |
| DataTables | - | インタラクティブなテーブル |
| jQuery UI | - | UIコンポーネント |
| Datepicker | - | 日付入力 |

### 2.3 インフラ・ツール

| 項目 | 用途 |
|---|---|
| Docker | コンテナ化・ローカル開発環境 |
| Docker Compose | マルチコンテナ管理 |
| AWS EC2 | クラウドホスティング |
| AWS ECR | コンテナレジストリ |
| Apache httpd | Webサーバー（mod_rewrite） |
| Ansible | インフラ自動化 |
| GitHub Actions | CI/CD |
| PHPUnit | 単体テスト |
| PHP-CS-Fixer | コードスタイル修正 |
| Hadolint | Dockerfileリンター |

---

## 3. システムアーキテクチャ

### 3.1 全体構成

```
┌──────────────────────────────────────────────────────────┐
│                    クライアント層                          │
│    ブラウザ（PC）         スマートフォン                    │
└───────────────────────────┬──────────────────────────────┘
                             │ HTTPS
┌───────────────────────────┴──────────────────────────────┐
│                  Webサーバー層                             │
│    Apache httpd + mod_rewrite (.htaccess)                 │
└───────────────────────────┬──────────────────────────────┘
                             │
┌───────────────────────────┴──────────────────────────────┐
│              アプリケーション層（FuelPHP）                  │
│                                                          │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────┐  │
│  │ Controller   │  │    Model    │  │      View       │  │
│  │（ビジネスロ │  │  (Fuel ORM) │  │ (Twig テンプレ │  │
│  │  ジック）    │  │             │  │  ート)          │  │
│  └──────┬──────┘  └──────┬──────┘  └─────────────────┘  │
│         │                 │                               │
│  ┌──────┴─────────────────┴───────┐                      │
│  │     共通コンポーネント           │                      │
│  │  Auth / Email / Log / Mydb     │                      │
│  └────────────────────────────────┘                      │
└───────────────────────────┬──────────────────────────────┘
                             │
┌───────────────────────────┴──────────────────────────────┐
│                  データベース層                             │
│                MySQL 5.6                                  │
└──────────────────────────────────────────────────────────┘
```

### 3.2 MVCパターン

FuelPHPのMVCパターンに従い、以下の役割分担で実装されています。

| 層 | 場所 | 役割 |
|---|---|---|
| **Controller** | `fuel/app/classes/controller/` | HTTPリクエスト処理、バリデーション、レスポンス生成 |
| **Model** | `fuel/app/classes/model/` | データアクセス、ORM、ビジネスルール |
| **View** | `fuel/app/views/` | Twigテンプレートによる画面描画 |

### 3.3 マルチ環境対応

| 環境 | 用途 |
|---|---|
| `development` | ローカル開発（詳細ログ、ローカルDB） |
| `staging` | 本番相当の検証環境 |
| `production` | 本番環境（最適化、ログ制限） |
| `test` | CI/CD用の分離テスト環境 |

---

## 4. プロジェクト構成

```
bms/
├── fuel/
│   └── app/
│       ├── classes/
│       │   ├── controller/         # コントローラー
│       │   │   ├── api/            # REST APIコントローラー
│       │   │   │   ├── convention/ # 大会API
│       │   │   │   ├── download/   # ダウンロードAPI
│       │   │   │   ├── game/       # 試合API
│       │   │   │   ├── mail/       # メールAPI
│       │   │   │   └── stats/      # 統計API
│       │   │   ├── team/           # チーム画面
│       │   │   ├── convention/     # 大会画面
│       │   │   ├── admin.php       # 管理者画面
│       │   │   ├── auth.php        # 認証（OAuth含む）
│       │   │   └── base.php        # 基底コントローラー
│       │   ├── model/              # モデル
│       │   │   ├── stats/          # 成績関連モデル
│       │   │   │   ├── hitting.php
│       │   │   │   ├── pitching.php
│       │   │   │   ├── fielding.php
│       │   │   │   ├── player.php
│       │   │   │   └── award.php
│       │   │   ├── user.php
│       │   │   ├── team.php
│       │   │   ├── game.php
│       │   │   ├── player.php
│       │   │   ├── league.php
│       │   │   └── convention.php
│       │   ├── common.php          # 共通ユーティリティ
│       │   ├── common/
│       │   │   ├── email.php       # メールユーティリティ
│       │   │   └── form.php        # フォームユーティリティ
│       │   ├── log.php             # カスタムログ
│       │   └── mydb.php            # DBユーティリティ
│       ├── config/                 # 設定ファイル
│       │   ├── routes.php          # URLルーティング
│       │   ├── db.php              # DB設定
│       │   ├── auth.php            # 認証設定
│       │   ├── simpleauth.php      # SimpleAuth設定
│       │   ├── opauth.php          # OAuth設定
│       │   ├── bms.php             # BMS固有設定
│       │   └── {env}/             # 環境別設定
│       ├── views/                  # Twigテンプレート
│       │   ├── layout/
│       │   ├── team/
│       │   ├── game/
│       │   ├── convention/
│       │   ├── admin/
│       │   ├── include/            # パーシャルテンプレート
│       │   └── smartphone/         # モバイル専用テンプレート
│       ├── migrations/             # DBマイグレーション（40+）
│       └── tests/                  # PHPUnitテスト
├── public/                         # Webルート
│   ├── index.php                   # エントリーポイント
│   ├── css/                        # スタイルシート
│   ├── js/                         # JavaScript
│   └── .htaccess                   # Apacheリライトルール
├── docker/                         # Docker設定
│   ├── docker-compose.yml
│   └── web/Dockerfile.legacy
├── ansible/                        # インフラ自動化
├── deploy/                         # デプロイスクリプト
│   ├── deploy.pl                   # Perlデプロイスクリプト
│   ├── bms.list                    # 本番デプロイリスト
│   └── bms_staging.list            # ステージングデプロイリスト
├── sql/                            # カスタムSQLクエリ
│   ├── goro_king/
│   ├── best9/
│   └── pull_spray/
├── misc/
│   └── bms-ER-diagram.*            # ER図
├── .github/workflows/              # GitHub Actions
├── composer.json
└── CHANGELOG.md
```

---

## 5. データベース設計

### 5.1 エンティティ関連図（概略）

```
users ──────── players ─────── teams
                  │                │
                  │                └─── games ──── stats_players
                  │                         │      stats_hittings
                  └─────────────────────────┤      stats_pitchings
                                            │      stats_fieldings
                                            │      games_runningscores
                                            └──── games_teams

conventions ── conventions_teams (teams)
           └── conventions_games (games)
```

### 5.2 主要テーブル定義

#### users（ユーザー）

| カラム | 型 | 説明 |
|---|---|---|
| id | INT PK | ユーザーID |
| username | VARCHAR | ユーザー名（ログインID） |
| password | VARCHAR | ハッシュ化パスワード |
| email | VARCHAR | メールアドレス |
| group | INT | グループ（権限レベル） |
| last_login | DATETIME | 最終ログイン日時 |
| profile_fields | TEXT | プロフィール情報（JSON） |
| created_at | DATETIME | 作成日時 |
| updated_at | DATETIME | 更新日時 |

#### teams（チーム）

| カラム | 型 | 説明 |
|---|---|---|
| id | INT PK | チームID |
| name | VARCHAR | チーム名 |
| url_path | VARCHAR UNIQUE | チーム専用URLパス |
| regulation_at_bats | INT | 規定打席数 |
| status | INT | ステータス（-1:削除, 1:有効） |
| created_at | DATETIME | 作成日時 |
| updated_at | DATETIME | 更新日時 |

#### players（選手）

| カラム | 型 | 説明 |
|---|---|---|
| id | INT PK | 選手ID |
| team_id | INT FK | チームID |
| name | VARCHAR | 選手名 |
| number | VARCHAR | 背番号 |
| username | VARCHAR | ユーザー名（users.usernameとの紐づけ） |
| status | INT | ステータス（-1:削除, 0:無効, 1:有効） |
| role | INT | 役割（1:一般, 50:チーム管理者） |
| created_at | DATETIME | 作成日時 |
| updated_at | DATETIME | 更新日時 |

#### games（試合）

| カラム | 型 | 説明 |
|---|---|---|
| id | INT PK | 試合ID |
| date | DATE | 試合日 |
| start_time | TIME | 開始時刻 |
| stadium | VARCHAR | 球場名 |
| memo | TEXT | メモ |
| game_status | INT | 試合ステータス |
| top_status | INT | 先攻チームの入力ステータス |
| bottom_status | INT | 後攻チームの入力ステータス |
| created_at | DATETIME | 作成日時 |
| updated_at | DATETIME | 更新日時 |

#### stats_hittings（打撃成績）

| カラム | 型 | 説明 |
|---|---|---|
| id | INT PK | 打撃成績ID |
| player_id | INT FK | 選手ID |
| game_id | INT FK | 試合ID |
| team_id | INT FK | チームID |
| TPA | INT | 打席数 |
| AB | INT | 打数 |
| H | INT | 安打数 |
| 2B | INT | 二塁打 |
| 3B | INT | 三塁打 |
| HR | INT | 本塁打 |
| SO | INT | 三振 |
| BB | INT | 四球 |
| HBP | INT | 死球 |
| SAC | INT | 犠打 |
| SF | INT | 犠飛 |
| RBI | INT | 打点 |
| R | INT | 得点 |
| SB | INT | 盗塁 |
| created_at | DATETIME | 作成日時 |
| updated_at | DATETIME | 更新日時 |

#### stats_pitchings（投球成績）

| カラム | 型 | 説明 |
|---|---|---|
| id | INT PK | 投球成績ID |
| player_id | INT FK | 選手ID |
| game_id | INT FK | 試合ID |
| team_id | INT FK | チームID |
| W | INT | 勝利 |
| L | INT | 敗戦 |
| H | INT | ホールド |
| SV | INT | セーブ |
| IP | DECIMAL | 投球回数 |
| HA | INT | 被安打 |
| SO | INT | 奪三振 |
| BB | INT | 四球 |
| HBP | INT | 死球 |
| ER | INT | 自責点 |
| created_at | DATETIME | 作成日時 |
| updated_at | DATETIME | 更新日時 |

#### conventions（大会）

| カラム | 型 | 説明 |
|---|---|---|
| id | INT PK | 大会ID |
| name | VARCHAR | 大会名 |
| published | INT | 公開設定（0:非公開, 1:公開） |
| status | INT | ステータス |
| created_at | DATETIME | 作成日時 |
| updated_at | DATETIME | 更新日時 |

### 5.3 ステータス値の定義

**試合ステータス（game_status）:**
| 値 | 意味 |
|---|---|
| 0 | 下書き |
| 1 | 進行中 |
| 2 | 完了 |

**成績入力ステータス（top_status / bottom_status）:**
| 値 | 意味 |
|---|---|
| 0 | 未入力 |
| 1 | 入力中（下書き） |
| 2 | 入力完了 |

**選手・チームステータス:**
| 値 | 意味 |
|---|---|
| -1 | 削除済み（ソフトデリート） |
| 0 | 無効 |
| 1 | 有効 |

---

## 6. 機能仕様

### 6.1 ユーザー管理

#### 6.1.1 アカウント登録

- メールアドレス・パスワードによる新規登録
- Google/FacebookのOAuthによるソーシャルログイン
- ユーザー名（ログインID）の一意性チェック
- パスワードはSHA-1 + ソルト + 10,000イテレーションでハッシュ化

#### 6.1.2 ログイン・ログアウト

- SimpleAuthによるセッション管理
- OAuthプロバイダー経由のログイン（Google, Facebook）
- `/force_login/:username` による管理者強制ログイン

#### 6.1.3 プロフィール管理

- プロフィール情報の更新
- Gravatarによるプロフィール画像の自動取得

### 6.2 チーム管理

#### 6.2.1 チーム作成・設定

| 項目 | 仕様 |
|---|---|
| チーム名 | 必須、ユニーク |
| URLパス | 必須、ユニーク（例: `/team/my-team`） |
| 規定打席数 | チーム打率計算に使用 |

#### 6.2.2 チームメンバー管理

- 選手の追加・編集・削除（ソフトデリート）
- 選手とユーザーアカウントの紐づけ（任意）
- チーム管理者権限の付与
- 参加招待メールの送信

### 6.3 試合管理

#### 6.3.1 試合登録

| 項目 | 仕様 |
|---|---|
| 試合日 | 必須 |
| 開始時刻 | 任意 |
| 球場名 | 任意 |
| メモ | 任意 |
| 対戦相手 | チーム名（フリー入力）または登録チームから選択 |
| 大会紐づけ | 大会試合として登録可能 |

#### 6.3.2 試合参加選手登録

- 先攻・後攻チームの参加選手をリストから選択
- 打順の設定
- 登録後に成績入力が可能になる

#### 6.3.3 スコア記録

- イニング別得点（得点経過）の記録
- 合計得点の自動計算

### 6.4 成績入力

#### 6.4.1 打撃成績入力

成績入力フローは以下の通りです。

```
試合参加選手登録 → 打撃成績入力（下書き保存可能）→ 完了マーク
```

| 入力項目 | 型 | 説明 |
|---|---|---|
| 打席数(TPA) | 整数 | 打席に立った回数 |
| 打数(AB) | 整数 | 四球・犠打等を除く |
| 安打(H) | 整数 | ヒット総数 |
| 二塁打(2B) | 整数 | |
| 三塁打(3B) | 整数 | |
| 本塁打(HR) | 整数 | |
| 三振(SO) | 整数 | |
| 四球(BB) | 整数 | |
| 死球(HBP) | 整数 | |
| 犠打(SAC) | 整数 | |
| 犠飛(SF) | 整数 | |
| 打点(RBI) | 整数 | |
| 得点(R) | 整数 | |
| 盗塁(SB) | 整数 | |

#### 6.4.2 投球成績入力

| 入力項目 | 型 | 説明 |
|---|---|---|
| 勝利(W) | 整数 | |
| 敗戦(L) | 整数 | |
| ホールド(H) | 整数 | |
| セーブ(SV) | 整数 | |
| 投球回(IP) | 小数 | |
| 被安打(HA) | 整数 | |
| 奪三振(SO) | 整数 | |
| 四球(BB) | 整数 | |
| 死球(HBP) | 整数 | |
| 自責点(ER) | 整数 | |

#### 6.4.3 打撃詳細入力

- 打席ごとの結果記録（安打種別、三振、四球等）
- 打順の整合性チェック

#### 6.4.4 成績バリデーション

- 打席数の整合性チェック（打数 + 四球 + 死球 + 犠打 + 犠飛 = 打席数）
- 安打の整合性チェック（2B + 3B + HR ≤ H）
- 打順の重複チェック
- 成績入力完了時の一括バリデーション

### 6.5 統計・分析

#### 6.5.1 個人成績

- 全試合の成績集計
- 打率、出塁率、長打率などの計算指標
- 年度別フィルタリング
- 試合ごとの成績履歴

#### 6.5.2 チーム成績

- チーム打率・防御率の計算
- 選手ランキング（打点、本塁打等）
- 年度別集計
- 規定打席以上の選手フィルタリング

#### 6.5.3 大会成績

- 大会参加チームの成績比較
- 大会内個人成績ランキング

### 6.6 大会管理

| 機能 | 説明 |
|---|---|
| 大会作成 | 大会名・公開設定で大会を作成 |
| チーム追加 | 参加チームを大会に登録 |
| 試合追加 | 大会内に試合を紐づけ |
| 公開/非公開 | 大会情報の公開設定 |
| 大会管理者 | 大会ごとの管理者権限 |

### 6.7 管理者機能

| 機能 | 説明 |
|---|---|
| ユーザー管理 | ユーザーの作成・編集・停止・グループ変更 |
| チーム管理 | 全チームの閲覧・設定変更 |
| 選手管理 | 選手情報の管理 |
| 大会管理 | 全大会の管理 |
| アクティビティログ | システムの操作履歴確認 |

---

## 7. API仕様

### 7.1 試合API

**ベースURL:** `/api/game/`

| エンドポイント | メソッド | 説明 |
|---|---|---|
| `/api/game/updateStatus` | POST | 試合ステータスの更新 |
| `/api/game/updateScore` | POST | 得点経過（イニングスコア）の更新 |
| `/api/game/updatePlayer` | POST | 参加選手リストの更新 |
| `/api/game/updateBatter` | POST | 打撃成績の更新 |
| `/api/game/updatePitcher` | POST | 投球成績の更新 |
| `/api/game/updateOther` | POST | その他試合データの更新 |

### 7.2 統計API

| エンドポイント | メソッド | 説明 |
|---|---|---|
| `/api/stats/check` | GET | 成績の整合性チェック |

### 7.3 メールAPI

| エンドポイント | メソッド | 説明 |
|---|---|---|
| `/api/mail/remind` | GET | 成績入力リマインダーメールの送信 |

### 7.4 ダウンロードAPI

| エンドポイント | メソッド | 説明 |
|---|---|---|
| `/api/download/stats/itleague` | GET | ITリーグ成績のExcelエクスポート |
| `/api/download/stats/team` | GET | チーム成績のExcelエクスポート |

### 7.5 大会API

| エンドポイント | メソッド | 説明 |
|---|---|---|
| `/api/convention/team/add` | POST | 大会へのチーム追加 |
| `/api/convention/team/remove` | POST | 大会からのチーム削除 |

### 7.6 レスポンスフォーマット

APIレスポンスはJSON形式で返されます。

```json
{
  "status": "success" | "error",
  "message": "処理結果メッセージ",
  "data": {}
}
```

---

## 8. 認証・認可

### 8.1 認証方式

#### 8.1.1 SimpleAuth（パスワード認証）

- ユーザー名とパスワードによる認証
- パスワードはSHA-1ハッシュ + ソルト + 10,000イテレーション
- セッションベースの認証状態管理

#### 8.1.2 Opauth（OAuth認証）

| プロバイダー | 説明 |
|---|---|
| Google | Google OAuth 2.0によるログイン |
| Facebook | Facebook OAuth 2.0によるログイン |

- OAuthログイン後、既存アカウントへの紐づけまたは新規登録
- 1ユーザーに複数のOAuthプロバイダーを紐づけ可能

### 8.2 権限レベル

| グループ値 | 名称 | 権限 |
|---|---|---|
| 100 | システム管理者 | 全機能・全データへのアクセス |
| 50 | チーム管理者/モデレーター | チーム管理・試合管理・成績管理 |
| 1 | 一般ユーザー | 自チームの閲覧・自分の成績入力 |
| 0 | ゲスト | 公開情報の閲覧のみ |
| -1 | 停止済み | アクセス不可 |

### 8.3 ロールと権限マッピング

| ロール | グループ | 許可アクション |
|---|---|---|
| `admin` | 100 | 全アクション |
| `moderator` | 50, 100 | 試合管理、チーム管理、成績編集 |
| `user` | 1, 50, 100 | 自成績入力、プロフィール管理 |

### 8.4 チームレベル権限制御

- **チーム管理者（role=50）**: チーム設定変更、試合登録、全メンバー成績編集
- **チームメンバー（role=1）**: 自分の成績のみ編集可能
- 権限チェックは `Model_Player::has_team_admin()` で実施

### 8.5 アクセス制御フロー

```
リクエスト受信
    ↓
セッション確認（ログイン状態）
    ↓
グループ/ロール確認（Auth::has_access()）
    ↓
チーム権限確認（必要な場合）
    ↓
処理実行 or 403/302リダイレクト
```

---

## 9. 画面仕様

### 9.1 画面一覧

| 画面 | URL | 説明 |
|---|---|---|
| トップ | `/` | チーム一覧・ログインフォーム |
| ログイン | `/login` | ログイン画面 |
| ユーザー登録 | `/register` | 新規ユーザー登録 |
| チーム一覧 | `/team` | 全チームの一覧 |
| チームホーム | `/team/{url_path}` | チーム情報・成績サマリー |
| チーム設定 | `/team/{url_path}/setting` | チーム設定（管理者のみ） |
| 試合一覧 | `/team/{url_path}/game` | チームの試合一覧 |
| 試合詳細 | `/team/{url_path}/game/{id}` | 試合スコア・参加選手・成績 |
| 成績入力 | `/team/{url_path}/game/{id}/input` | 打撃・投球成績入力 |
| 選手一覧 | `/team/{url_path}/player` | チームメンバー一覧 |
| 選手詳細 | `/team/{url_path}/player/{id}` | 個人成績・履歴 |
| 大会一覧 | `/convention` | 全大会の一覧 |
| 大会詳細 | `/convention/{id}` | 大会情報・参加チーム・試合 |
| 管理画面 | `/admin` | システム管理（管理者のみ） |

### 9.2 レスポンシブデザイン

- **PC画面**: フル機能のBootstrapレイアウト
- **スマートフォン画面**: 専用Twigテンプレート（`views/smartphone/`）
  - Agent クラスによるデバイス検出
  - タッチ操作に最適化したUI
  - スライドインナビゲーション

### 9.3 共通UIコンポーネント

| コンポーネント | 使用ライブラリ | 用途 |
|---|---|---|
| データテーブル | DataTables | ソート・検索可能な成績表 |
| 選択ボックス | Select2 | 選手・チーム選択 |
| 日付入力 | jQuery UI Datepicker | 試合日・年度選択 |
| 時刻入力 | Timepicker | 試合開始時刻 |

---

## 10. メール通知

### 10.1 送信メール一覧

| メール種別 | トリガー | 受信者 |
|---|---|---|
| 参加招待通知 | チームへの招待時 | 招待された選手 |
| 成績入力リマインダー | 定期バッチまたは手動実行 | 成績未入力の選手 |
| 試合参加通知 | 試合への参加登録時 | 参加選手 |

### 10.2 メール設定

- SMTP経由での送信
- 開発・テスト環境では指定アドレスへのリダイレクト（`bms.php`の`test_email`設定）
- `Common_Email` クラスによる共通化されたメール処理

---

## 11. データエクスポート

### 11.1 Excelエクスポート

| エクスポート種別 | エンドポイント | 形式 |
|---|---|---|
| チーム成績 | `/api/download/stats/team` | Excel（.xlsx） |
| ITリーグ成績 | `/api/download/stats/itleague` | Excel（.xlsx） |

- PHPExcel 1.7.x を使用
- 年度フィルタリング対応
- チーム成績・個人成績の両方を含む

### 11.2 カスタムSQLクエリ

`sql/` ディレクトリに特定の分析・集計クエリが含まれています。

| フォルダ | 用途 |
|---|---|
| `goro_king/` | ゴローキング（特定の賞）集計 |
| `best9/` | ベストナイン選出計算 |
| `pull_spray/` | 引っ張り・流し打ち分析 |

---

## 12. インフラ・デプロイ構成

### 12.1 Dockerコンテナ構成

```yaml
# docker-compose.yml（概略）
services:
  web:
    image: php:5.6-apache
    build: docker/web/Dockerfile.legacy
    ports:
      - "80:80"
    volumes:
      - ./:/var/www/html
    depends_on:
      - db

  db:
    image: mysql:5.6
    environment:
      MYSQL_DATABASE: bms
      MYSQL_ROOT_PASSWORD: {password}
    ports:
      - "3306:3306"
```

### 12.2 本番環境

| 項目 | 構成 |
|---|---|
| サーバー | AWS EC2 |
| Webサーバー | Apache httpd + mod_rewrite |
| PHP | 5.6 |
| データベース | MySQL 5.6 |
| コンテナ管理 | Docker + ECR |

### 12.3 デプロイ方法

| 方法 | 用途 |
|---|---|
| Perl スクリプト（`deploy/deploy.pl`） | ファイルベースのデプロイ |
| Ansible プレイブック（`ansible/`） | インフラ自動化 |
| Docker | コンテナベースのデプロイ |

### 12.4 Apache設定

- `mod_rewrite` によるURLリライト
- FuelPHPのフロントコントローラー（`public/index.php`）へのルーティング
- HTTPS対応（本番環境）

---

## 13. CI/CD パイプライン

### 13.1 GitHub Actions ワークフロー

#### PHP テスト（`php.yaml`）

```
トリガー: push (master), pull_request, schedule (月次)
┌─────────────────────────────────────────┐
│ 1. PHP 5.6 環境セットアップ              │
│ 2. Composer による依存関係インストール    │
│ 3. MySQL サービス起動                    │
│ 4. データベースマイグレーション実行       │
│ 5. PHPUnit によるテスト実行              │
│    (xdebug カバレッジ計測)               │
│ 6. PHP-CS-Fixer によるコードスタイル確認 │
└─────────────────────────────────────────┘
```

#### Docker リンター（`hadolint.yaml`）

```
トリガー: pull_request
┌────────────────────────────┐
│ Hadolint で Dockerfile 検証 │
└────────────────────────────┘
```

### 13.2 Dependabot

- GitHub Actions の依存関係を自動アップデート
- PRを自動作成してアップデートを通知

---

## 14. 設定・環境管理

### 14.1 主要設定ファイル

| ファイル | 用途 |
|---|---|
| `fuel/app/config/config.php` | アプリケーション基本設定 |
| `fuel/app/config/db.php` | データベース接続設定 |
| `fuel/app/config/auth.php` | 認証設定 |
| `fuel/app/config/simpleauth.php` | SimpleAuth グループ/ロール設定 |
| `fuel/app/config/opauth.php` | OAuth プロバイダー設定 |
| `fuel/app/config/bms.php` | BMS固有設定（メンテナンスモード等） |
| `fuel/app/config/routes.php` | URLルーティング定義 |

### 14.2 環境別設定

環境ごとに`fuel/app/config/{env}/`ディレクトリで上書き設定が可能です。

| 環境 | ディレクトリ |
|---|---|
| 開発 | `config/development/` |
| ステージング | `config/staging/` |
| 本番 | `config/production/` |
| テスト | `config/test/` |

### 14.3 BMS固有設定（`bms.php`）

| 設定項目 | 説明 |
|---|---|
| `maintenance` | メンテナンスモード（true/false） |
| `moderator_team_ids` | モデレーター権限を持つチームのID一覧 |
| `test_email` | テスト環境でのメール送信先 |

---

## 15. セキュリティ

### 15.1 認証セキュリティ

- パスワードのハッシュ化（SHA-1 + ソルト + 10,000イテレーション）
- CSRF対策（FuelPHP組み込み機能）
- セッション管理（サーバーサイドセッション）

### 15.2 入力バリデーション

- フォーム入力のサーバーサイドバリデーション（FuelPHP Fieldset）
- ORMモデルレベルのバリデーションルール
- SQLインジェクション対策（ORM経由のパラメータバインド）

### 15.3 アクセス制御

- ロールベースアクセス制御（RBAC）
- `Auth::has_access()` によるアクション単位の権限チェック
- チームレベルの所有権チェック

### 15.4 ネットワークセキュリティ

- `etc/iptables` によるファイアウォール設定
- `etc/hosts.allow/deny` によるネットワークアクセス制御
- SSH接続設定（`etc/ssh/`）

### 15.5 ソフトデリート

- ユーザー・選手・チームは物理削除ではなく`status=-1`でソフトデリート
- データの誤削除防止と監査証跡の確保

---

## 付録

### A. 計算指標一覧

| 指標 | 計算式 |
|---|---|
| 打率(AVG) | H ÷ AB |
| 出塁率(OBP) | (H + BB + HBP) ÷ (AB + BB + HBP + SF) |
| 長打率(SLG) | (H + 2B + 2×3B + 3×HR) ÷ AB |
| OPS | OBP + SLG |
| 防御率(ERA) | (ER × 9) ÷ IP |

### B. 用語集

| 用語 | 説明 |
|---|---|
| 草野球 | アマチュア（社会人）野球チーム |
| 規定打席 | 打率ランキングに必要な最低打席数 |
| ゴローキング | 特定の打球方向（ゴロ）での成績賞 |
| ベストナイン | 各ポジションの優秀選手選出 |
| TPA | Total Plate Appearances（打席数） |

### C. 変更履歴

変更履歴は `CHANGELOG.md` を参照してください。
