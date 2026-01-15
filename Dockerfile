FROM webdevops/php-apache:8.2-alpine

# Diretório da aplicação
WORKDIR /app

# Copia o projeto
COPY . /app

# Permissões do Laravel
RUN chown -R application:application \
        /app/storage \
        /app/bootstrap/cache

# Configura cron do Laravel Scheduler
RUN echo "* * * * * php /app/artisan schedule:run >> /dev/null 2>&1" > /etc/crontabs/application

# Configurações padrão da imagem
ENV WEB_DOCUMENT_ROOT=/app/public

EXPOSE 80
