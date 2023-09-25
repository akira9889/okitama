FROM nginx:alpine

ARG UPSTREAM_NAME
ARG FPM_PORT

COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
RUN rm /etc/nginx/conf.d/default.conf

RUN sed -ie "s/{UPSTREAM_NAME}/${UPSTREAM_NAME}/g" /etc/nginx/nginx.conf
RUN sed -ie "s/{FPM_PORT}/${FPM_PORT}/g" /etc/nginx/nginx.conf

EXPOSE 80
