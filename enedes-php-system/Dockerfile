FROM php:8.1-apache

# Atualiza os pacotes e instala libpq-dev e outras dependências
RUN apt-get update && apt-get install -y libpq-dev && rm -rf /var/lib/apt/lists/*

# Instala extensões PHP necessárias
RUN docker-php-ext-install pdo pdo_pgsql

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Copiar arquivos da aplicação
COPY . /var/www/html/

# Configurar permissões, se necessário
RUN chown -R www-data:www-data /var/www/html/
