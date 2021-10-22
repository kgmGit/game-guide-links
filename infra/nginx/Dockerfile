FROM nginx:1.20-alpine

COPY ./backend/public /work/public
COPY ./infra/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./infra/nginx/*.pem /etc/nginx/conf.d/
