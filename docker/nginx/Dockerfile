FROM nginx:stable-alpine

ARG WORKDIR_SITE
ARG PROJECT_NAME
ARG FASTCGI_READ_TIMEOUT
ARG CONTAINER_GROUP_NAME=$CONTAINER_USER_NAME
ARG CONTAINER_USER_PASSWORD=$CONTAINER_USER_NAME

RUN apk update && apk upgrade && apk add --no-cache bash shadow

COPY ./conf/default.conf /etc/nginx/conf.d/default.conf
COPY ./conf/nginx.conf /etc/nginx/

RUN adduser -u 1010 -D -S -G www-data www-data

RUN sed -i \
    -e "s|\${PROJECT_NAME}|$PROJECT_NAME|" /etc/nginx/conf.d/default.conf \
    -e "s|\${WORKDIR_SITE}|$WORKDIR_SITE|" /etc/nginx/conf.d/default.conf \
    -e "s|\${FASTCGI_READ_TIMEOUT}|$FASTCGI_READ_TIMEOUT|" /etc/nginx/conf.d/default.conf

EXPOSE 80
