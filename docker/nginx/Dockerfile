FROM nginx
# NOTE. THIS IMAGE MUST BE COMPILED FROM THE PARENT DIRECTORY USING
# docker build -t $IMAGE_TAG -f docker/nginx/Dockerfile .

COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY . /var/www/html
COPY docker/nginx/conf.d/* /etc/nginx/conf.d/
COPY docker/nginx/fastcgi_params /etc/nginx/fastcgi_params

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]