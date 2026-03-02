# BMS Backend 詳細設計（Java + Quarkus, 2026）

## 1. 目的とスコープ
- 本書は BMS リライトにおける Backend の詳細設計を定義する
- 対象は以下
1. API/ドメイン/データモデル
2. 認証認可
3. 入力機能・閲覧機能（プロトタイプ）
4. 運用・監視・品質保証
- 大会機能（Convention）は対象外（廃止）
- Excelエクスポートは低優先度機能として後段実装
- SQS 利用は低優先度機能として後段実装

## 2. 採用技術
- 言語: Java 21
- フレームワーク: Quarkus 3.x
- REST: `quarkus-rest`
- OpenAPI: `quarkus-smallrye-openapi` + Swagger UI
- ORM: Hibernate ORM with Panache
- Migration: Flyway
- DB: PostgreSQL 17
- Cache: Redis
- Queue: AWS SQS（後段導入）
- 認証連携: Auth.js (Frontend) + OIDCトークン検証（Backend）
- 観測: OpenTelemetry

## 3. システム構成
- Frontend（Next.js）から BFF API を呼び出し
- BFF API は Quarkus 単一アプリ（モジュラーモノリス）として稼働
- 主データは PostgreSQL、短期キャッシュは Redis
- プロトタイプでは同期処理中心で実装し、SQS を使う非同期処理は後段で追加

## 4. モジュール構成
- `auth`: トークン検証、ユーザー解決、権限判定
- `team`: チーム作成・更新・メンバー管理
- `player`: 選手管理、背番号/ポジション管理
- `game`: 試合情報、参加者、スコア管理
- `stats`: 打撃/投球成績入力、集計
- `dashboard`: チーム/個人成績表示用の集約API
- `admin`: システム管理者向け管理機能
- `export`: Excel生成（後段実装）
- `shared`: 例外、レスポンス、監査ログ、共通ユーティリティ

## 5. レイヤ設計
- `resource`:
RESTエンドポイント。DTO入出力とHTTP責務のみ
- `application`:
ユースケース実装。トランザクション境界、権限チェック呼び出し
- `domain`:
エンティティ/値オブジェクト/ドメインサービス
- `infra`:
Repository、外部サービス接続（Redis/S3等）

## 6. API設計方針
- APIファースト。OpenAPIを唯一の契約として管理
- エンドポイント命名はリソース指向（`/api/v1/teams/{teamId}` など）
- 非互換変更は `/v2` を新設
- 一覧APIはカーソルページングを標準化
- エラー形式を統一

```json
{
  "code": "GAME_NOT_FOUND",
  "message": "game not found",
  "traceId": "01H...",
  "details": []
}
```

## 7. OpenAPI駆動開発
- Quarkus拡張を利用
1. `quarkus-rest`（REST実装）
2. `quarkus-smallrye-openapi`（OpenAPI/Swagger UI）
- 設計フロー
1. Backend は Code First とし、Resource Interface/DTO を Java で先行実装
2. Quarkus 拡張で Java ソースから `openapi.yaml` を生成
3. CIで OpenAPI lint と破壊的変更チェックを実施
4. Frontend は生成済み `openapi.yaml` を入力にクライアント処理を実装
- OpenAPI は PR レビュー必須（Backend + Frontend 両承認）

## 8. 認証・認可
- 認証
1. Frontend(Auth.js)でログイン
2. OIDC/JWT を Backend に Bearer 送信
3. Backend で署名・有効期限・issuer/audience を検証
- 認可（RBAC）
1. `SYSTEM_ADMIN`（旧100）
2. `TEAM_ADMIN`（旧50）
3. `USER`（旧1）
4. `GUEST`（旧0）
- 認可方針
1. チーム単位リソースは team scope を必須チェック
2. 書き込みAPIは `TEAM_ADMIN` 以上
3. 監査対象APIは操作ログを必須出力

## 9. データモデル（主要）
- `users`
- `teams`
- `team_members`
- `players`
- `games`
- `game_participants`
- `hitting_stats`
- `pitching_stats`
- `game_running_scores`
- `audit_logs`

### 9.1 制約
- 全テーブルに `id`, `created_at`, `updated_at`, `version` を持つ
- 論理削除対象は `deleted_at` を持つ
- 重複防止
1. `players(team_id, uniform_number)` unique
2. `team_members(team_id, user_id)` unique
3. `hitting_stats(game_id, player_id)` unique
4. `pitching_stats(game_id, player_id)` unique

### 9.2 大会系テーブル
- `conventions*` は新DBに作成しない
- 旧DBからの移行対象外

## 10. 成績入力・集計設計
- 入力
1. 試合単位のバルク更新APIを提供
2. 楽観ロック（`version`）で同時更新競合を検出
3. 保存時に整合性チェック（例: 打数 >= 安打）
- 集計
1. 即時計算: 試合詳細表示に必要な軽量集計
2. DBクエリ最適化: シーズン集計、ランキングはインデックスと集約SQLで対応
3. プロトタイプでは非同期再計算は導入しない
4. 高負荷化した場合に SQS ベースの再計算ジョブを後段で導入

## 11. キャッシュ戦略
- 対象
1. チームサマリー
2. 選手ランキング
3. ダッシュボード集計
- キー例
1. `team:{teamId}:summary:{season}`
2. `team:{teamId}:ranking:{type}:{season}`
- 失効
1. 試合・成績更新時に関連キーをイベント駆動で削除
2. TTL は 5〜15分を基本

## 12. 非同期処理（後段・低優先度）
- Queue: SQS Standard（プロトタイプでは未導入）
- Worker: Quarkus scheduled/consumer（後段導入）
- ジョブ種別
1. `stats.recalculate`
2. `audit.export`（将来）
3. `excel.generate`（後段）
- リトライ
1. 指数バックオフ
2. DLQ へ退避
3. 運用アラート連携

## 13. トランザクション方針
- 原則: 1 API リクエスト = 1 トランザクション
- 外部I/O（Redis）は DBコミット後に実行
- 複数集約にまたがる更新は Application 層で順序制御

## 14. 監査ログ
- 対象: 書き込みAPI全て
- 記録項目
1. actor_user_id
2. team_id
3. action
4. target_type/target_id
5. request_id/trace_id
6. diff(before/afterの要約)

## 15. セキュリティ
- 入力検証: Jakarta Validation + 業務バリデーション
- SQLインジェクション対策: ORM + パラメータバインディング
- レート制限: IP + user 単位
- 秘密情報: AWS Secrets Manager
- HTTPセキュリティヘッダとCORSの最小許可設定

## 16. 可観測性
- 全リクエストに traceId を付与
- メトリクス
1. API latency p50/p95/p99
2. 4xx/5xx rate
3. DB query latency
4. Queue lag/DLQ件数（SQS導入後）
- ログ
1. JSON構造化ログ
2. PIIマスキング

## 17. テスト戦略
- Unit: Domain/Application の業務ルール
- Integration: Resource + DB + Security
- Contract: OpenAPI 準拠テスト
- E2E: 主要業務フロー（試合登録、成績入力、統計閲覧）
- 性能: k6 で負荷試験（p95 300ms目標）

## 18. ディレクトリ案
```text
backend/
  src/main/java/com/example/bms/
    shared/
    auth/
    team/
    player/
    game/
    stats/
    dashboard/
    admin/
    export/
  src/main/resources/
    application.yml
    db/migration/
  docs/openapi/
    bms.v1.yaml
```

## 19. リリース・移行（並行稼働なし）
- 事前に移行リハーサルを最低3回実施
- 本番移行手順
1. 旧システム停止
2. データエクスポート
3. 変換/インポート
4. 整合性検証（件数、主要集計）
5. 新システム起動
- ロールバック方針
1. 切替判定前は旧システムへ戻す
2. 判定後は新システム継続、差分修正で対応

## 20. 未決事項
1. OpenAPI生成物（`openapi.yaml`）のリポジトリ管理方針（コミット運用 or CI生成配布）
2. SQS導入の判定基準（負荷閾値、運用コスト、整合性要件）
3. Excelエクスポートの実装時期（正式版+1スプリントを想定）
4. 旧大会データの参照アーカイブ提供方式
