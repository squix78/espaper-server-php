FROM docker.io/debian:stretch

RUN apt-get update && apt-get -y install php7.0-cli php-xml

EXPOSE 8080

CMD cd /espaper-server-php && php -S 0.0.0.0:8080
