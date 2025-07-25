# Use PHP 8.1 com Apache
FROM php:8.1-apache

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP necessárias
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    zip

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos da aplicação
COPY . .

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expor porta 80
EXPOSE 80

# Comando para iniciar Apache
CMD ["apache2-foreground"]
