#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive

installpkg(){
    dpkg-query --status $1 >/dev/null || apt-get install -y $1
}

installpkg python-software-properties
add-apt-repository ppa:ondrej/php-7.0
wget -qO - http://packages.elasticsearch.org/GPG-KEY-elasticsearch | sudo apt-key add -
echo "deb http://packages.elasticsearch.org/elasticsearch/1.3/debian stable main" | sudo tee -a /etc/apt/sources.list

PG_REPO_APT_SOURCE=/etc/apt/sources.list.d/pgdg.list
if [ ! -f "$PG_REPO_APT_SOURCE" ]
then
  # Add PG apt repo:
  echo "deb http://apt.postgresql.org/pub/repos/apt/ trusty-pgdg main" > "$PG_REPO_APT_SOURCE"

  # Add PGDG repo key:
  wget --quiet -O - https://apt.postgresql.org/pub/repos/apt/ACCC4CF8.asc | apt-key add -
fi

if [[ ! -f /apt-get-run ]]; then apt-get update && touch /apt-get-run; fi

/etc/init.d/apache2 stop
update-rc.d apache2 disable

#basic staff
installpkg git
installpkg curl
installpkg php7.0
installpkg php7.0-common
installpkg php7.0-cli
installpkg php7.0-fpm
installpkg php7.0-curl
installpkg php7.0-opcache
installpkg php7.0-json
installpkg php-xdebug
installpkg postfix

service php7.0-fpm restart

#composer
curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

#postgres
PQSQL_DB_NAME="friendloc"
PQSQL_DB_USER="friendloc_user"
PQSQL_PASSWORD="123456"
PGSQL_VERSION=9.4

installpkg postgresql-$PGSQL_VERSION
installpkg postgresql-contrib-$PGSQL_VERSION

PG_CONF="/etc/postgresql/$PGSQL_VERSION/main/postgresql.conf"
PG_HBA="/etc/postgresql/$PGSQL_VERSION/main/pg_hba.conf"
PG_DIR="/var/lib/postgresql/$PGSQL_VERSION/main"

# Edit postgresql.conf to change listen address to '*':
sed -i "s/#listen_addresses = 'localhost'/listen_addresses = '*'/" "$PG_CONF"

# Append to pg_hba.conf to add password auth:
echo "host    all             all             all                     md5" >> "$PG_HBA"

# Explicitly set default client_encoding
echo "client_encoding = utf8" >> "$PG_CONF"

# Restart so that all new config is loaded:
service postgresql restart

cat << EOF | su - postgres -c psql
-- Create the database user:
CREATE USER $PQSQL_DB_USER WITH PASSWORD '$PQSQL_PASSWORD';

-- Create the database:
CREATE DATABASE $PQSQL_DB_NAME WITH OWNER=$PQSQL_DB_USER
                                  LC_COLLATE='en_US.utf8'
                                  LC_CTYPE='en_US.utf8'
                                  ENCODING='UTF8'
                                  TEMPLATE=template0;
EOF

installpkg php7.0-pgsql

service postgresql restart

#elasticsearch
installpkg openjdk-7-jre-headless
installpkg elasticsearch
service elasticsearch start

#nginx

installpkg nginx

rm -f /etc/nginx/sites-enabled/default
cp /tmp/friendloc /etc/nginx/sites-available
ln -s -f /etc/nginx/sites-available/friendloc /etc/nginx/sites-enabled/friendloc

/etc/init.d/apache2 stop
service nginx restart

locale-gen ru_RU.UTF-8

cd /home/vagrant/project && composer update