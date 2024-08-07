FROM nginx:latest

ENV NGNIX_PATH=/etc/nginx

COPY nginx.conf ${NGNIX_PATH}
COPY host.conf ${NGNIX_PATH}/conf.d/dfs.local.conf

EXPOSE 80
EXPOSE 443

WORKDIR /var/www/