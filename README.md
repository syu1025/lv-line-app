# Docker Laravel Project

このプロジェクトはDocker composeを用いてLaravelの環境構築を行います。以下にコマンドの説明とそれぞれの役割について解説します。

## 環境構築手順

**新規のプロジェクトの場合**
1. `cp .env.example .env`
1. `make create-project`

**既存のプロジェクトをクローンする場合**
1. `cp .env.example .env`
1. このdockerプロジェクトディレクトリで`git clone [対象リポジトリ]`
1. クローンしたプロジェクトのディレクトリ名を`backend`に変更　(ex. xxxxxx-project => backend)
1. `make init`

## VScode Setting

拡張機能のインストール

```
code --install-extension　streetsidesoftware.code-spell-checker
code --install-extension　eamodio.gitlens
code --install-extension　austenc.laravel-blade-spacer
code --install-extension　editorconfig.editorconfig
code --install-extension　open-southeners.laravel-pint
...
```

## 使用可能なmakeコマンド

- `make up`: Dockerコンテナをバックグラウンドで起動します。
- `make build`: Dockerイメージを強制的に新規作成します。キャッシュは使用せず、作成後の不要な中間コンテナも削除します。
- `make create-project`: 新規プロジェクトを作成します。これには、イメージのビルド、コンテナの起動、Laravelのインストール、キーの生成、ストレージリンクの作成、権限の設定、データベースの初期化などが含まれます。
- `make init`: コンテナを初期化します。これには、ビルドと起動、composerのインストール、環境変数ファイルの作成、キーの生成、ストレージリンクの作成、データベースのマイグレーション、権限の設定などが含まれます。
- `make remake`: プロジェクトを再作成します。これはコンテナの削除と初期化を行います。
- `make stop`: Dockerコンテナを停止します。
- `make down`: Dockerコンテナを停止し、ネットワーク、ボリューム、イメージを削除します。
- `make restart`: Dockerコンテナを再起動します。これはコンテナの停止と起動を行います。
- `make destroy`: Dockerコンテナを停止し、ネットワーク、ボリューム、イメージを全て削除します。
- `make destroy-volumes`: Dockerコンテナを停止し、ネットワークとボリュームを削除します。
- `make ps`: Dockerコンテナの状態を表示します。
- `make logs`: Dockerコンテナのログを表示します。
- `make logs-watch`: Dockerコンテナのログをリアルタイムで表示します。
- `make log-web`, `make log-app`, `make log-db`: 特定のコンテナ（web, app, db）のログを表示します。
- `make log-web-watch`, `make log-app-watch`, `make log-db-watch`: 特定のコンテナ（web, app, db）のログをリアルタイムで表示します。
- `make web`, `make app`: 特定のコンテナ（web, app）にシェル接続します。
- `make migrate`: データベースマイグレーションを実行します。
- `make fresh`: データベースをリフレッシュ

## サービス概要

### app

appサービスは、Laravelを動かすPHP環境を構築します。ビルド設定は `./infra/docker/php/Dockerfile` にあります。 

### web

webサービスは、Laravelアプリケーションを公開するためのNginx環境を構築します。ビルド設定は `./infra/docker/nginx/Dockerfile` にあります。

ポート設定:
- ホストのポート `8000` をコンテナのポート `80` にバインドします。

### db

dbサービスは、LaravelアプリケーションのためのMySQL環境を構築します。ビルド設定は `./infra/docker/mysql/Dockerfile` にあります。

ポート設定:
- ホストのポート `3306` をコンテナのポート `3306` にバインドします。

### phpmyadmin

phpMyAdminサービスは、MySQLデータベースを管理するためのWebインターフェースを提供します。

ポート設定:
- ホストのポート `4040` をコンテナのポート `80` にバインドします。
