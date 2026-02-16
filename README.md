# Freemarket App

---

## 環境構築手順

### 1. リポジトリのクローン

```bash
git clone https://github.com/mao716/freemarket-app.git
cd freemarket-app
```

### 2. Docker コンテナの起動

```bash
docker compose up -d --build
```

### 3. Laravel の依存関係インストール（初回のみ）

すでに `src/` ディレクトリに Laravel 本体は含まれています。
クローン後は以下のコマンドで依存関係（vendor/）をインストールしてください。

```bash
docker compose exec php bash
cd /var/www
composer install
exit
```

### 4. Laravel の環境変数設定

以下のコマンドで `.env` を作成してください。

```bash
cp src/.env.example src/.env
```

その後、 `.env` の該当箇所を以下のように編集し、Docker の MySQL コンテナに接続できるように設定してください。

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### 5. Stripe設定（テストモード）

本アプリは Stripe のテストモードを使用しています。
決済処理を行うには、Stripeアカウントを作成しテスト用APIキーを取得後、 `.env` に設定してください。

```env
STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxxx
```
※ 現在の実装では Webhook 機能（STRIPE_WEBHOOK_SECRET）は使用していません。


### 6. メール認証について

本アプリのメール認証機能は、Mailtrap（メールテストサービス）を利用して動作確認しています。

#### Mailtrap 設定方法

1. Mailtrap に無料登録し、Email Testing の Inbox を作成します。
2. Inbox の「SMTP Settings」から、以下の情報を取得します。

   - Host
   - Port
   - Username
   - Password

3. `src/.env` の `MAIL_〜` を以下のように設定してください。

   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=取得した Username
   MAIL_PASSWORD=取得した Password
   MAIL_ENCRYPTION=null
   MAIL_FROM_ADDRESS="no-reply@example.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```

   - 想定動作:

	1. 会員登録時にメール認証メールがMailtrapに届く
	2. メール認証誘導画面の「認証はこちらから」を押下すると、MailtrapのInboxが開く
	3. メール内のリンクをクリックするとメール認証が完了
	4. 認証後はプロフィール設定画面へ遷移


### 7. Laravel の初期設定

```bash
docker compose exec php bash
cd /var/www

# アプリケーションキーの生成
php artisan key:generate

# ストレージリンクの作成
php artisan storage:link

# マイグレーション実行
php artisan migrate

# シーディング（ダミーデータ投入）
php artisan db:seed

exit
```

### 8. テスト環境構築（PHPUnit）

本アプリでは PHPUnit を用いた Feature テストを実装しています。
テスト実行時は、本番用DBとは別に テスト専用データベース を使用します。

1. テスト用データベースの作成
MySQL コンテナに入り、以下を実行してください。
   ```bash
   docker compose exec mysql mysql -u root -proot
   ```
	ログイン後、以下のSQLを実行します。
	```sql
	CREATE DATABASE laravel_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
	GRANT ALL ON laravel_test.* TO 'laravel_user'@'%';
	FLUSH PRIVILEGES;
	EXIT;
	```

2.  `.env.testing` の作成
src/ ディレクトリ内で `.env.testing` を作成します。
	```bash
	cp src/.env src/.env.testing
	```
	`.env.testing` のDB設定を以下のように変更してください。
	```env
	APP_ENV=testing

	DB_CONNECTION=mysql
	DB_HOST=mysql
	DB_PORT=3306
	DB_DATABASE=laravel_test
	DB_USERNAME=laravel_user
	DB_PASSWORD=laravel_pass
	```
	※ `.env.testing` はGit管理対象外です。

3. テスト用DBのマイグレーション
	```bash
	docker compose exec php bash
	cd /var/www

	php artisan migrate:fresh --env=testing
	```
4. テスト実行
	```bash
	php artisan test
	exit
	```
	すべてのテストが PASS すれば正常に動作しています。
---

## 動作環境

- PHP 8.2（php-fpm）
- Laravel 11
- MySQL 8.0
- Nginx 1.21
- Docker / Docker Compose
- phpMyAdmin

---

## 使用技術

- Laravel Fortify（認証）
- Stripe（カード決済・Checkout）
- Mailtrap（メールテスト、認証メール）
- FormRequest（入力バリデーション）

---

## アクセスURL

- アプリケーション: <http://localhost>
- 会員登録: <http://localhost/register>
- phpMyAdmin: <http://localhost:8080>
  - ユーザー: `laravel_user`\
  - パスワード: `laravel_pass`

---

## ER 図

![ER図](./er_diagram.png)
