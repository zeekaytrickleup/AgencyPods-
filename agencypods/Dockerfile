FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

# Copy project files
COPY . .

# Install Node.js & NPM, then compile the Vite assets
RUN apk update && apk add --no-cache nodejs npm
RUN npm install && npm run build

# Configure Nginx public directory and error logging
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Environment variables for Laravel production optimization
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

EXPOSE 80
