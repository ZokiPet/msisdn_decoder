#FROM php:7.0-apache
#COPY . /var/www/msisdn_decoder/

FROM ulsmith/alpine-apache-php7
MAINTAINER You <you@youremail.com>

COPY . /app/public
RUN chown -R apache:apache /app
