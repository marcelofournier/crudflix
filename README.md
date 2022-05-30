Autor: Marcelo Fournier
Data: 30/05/2022

Aplicativo de teste/demonstração tem o objetivo de servir como base de cadastro para filmes.

Ferramentas utilizadas:
	1. Linguagem de programação: php 7.4
	2. Framework: Adianti Framework
	3. Banco de dados: SQLite.
	4. Ssistema operacional: Ubuntu 20.04 LTS
	5. Servidor web: Apache


Instalação do gtk2:	
	
# Como usuário root:
$ cd /usr/local
$ tar -xzvf php-gtk2xx.tar.gz
$ chmod 777 php-gtk2 -Rf

# Execute o comando abaixo:
$ cp -R /usr/local/php-gtk2/share/gtksourceview-1.0/ /usr/share/

# Após, vamos construir um link para facilitar a execução:
$ ln -sf /usr/local/php-gtk2/bin/php /usr/bin/php-gtk2


Recomendações básicos para Apache e PHP:

sudo apt-get update
sudo apt-get install apache2 php libapache2-mod-php
sudo apt-get install php-soap php-xml php-curl php-opcache php-gd php-sqlite3 php-mbstring
sudo apt-get install rpl zip unzip git vim curl

Em seguida, habilitaremos os módulos do apache, com destaque para o prefork:

a2dismod mpm_event
a2dismod mpm_worker
a2enmod  mpm_prefork
a2enmod  rewrite
a2enmod  php8.1


	
	

