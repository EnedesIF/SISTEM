FROM php:8.1-apache

# Atualiza o apt e instala libpq-dev para suporte ao PostgreSQL + limpa cache para manter a imagem leve
RUN apt-get update && apt-get install -y libpq-dev && rm -rf /var/lib/apt/lists/*

# Instala as extensões pdo e pdo_pgsql necessárias para conexão com PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Copia todo o conteúdo da aplicação para o diretório padrão do Apache
COPY . /var/www/html/

# Exponha a porta padrão do Apache
EXPOSE 80

# Comando padrão para rodar o Apache em primeiro plano
CMD ["apache2-foreground"]
