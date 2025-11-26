#!/bin/bash
set -e

echo "Aguardando o MySQL iniciar..."
until mysqladmin ping -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" --silent; do
  sleep 2
done

echo "MySQL está pronto. Aplicando o dump..."
mysql -h db -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE" < /docker-entrypoint-initdb.d/escola_db.sql

echo "Dump aplicado com sucesso!"
