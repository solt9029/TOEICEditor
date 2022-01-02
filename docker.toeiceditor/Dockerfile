FROM centos:7

RUN yum install -y httpd
RUN yum install --enablerepo=epel,remi-php56,remi -y \
  php \
  php-cli \
  php-gd \
  php-mbstring \
  php-mcrypt \
  php-mysqlnd \
  php-pdo \
  php-xml \
  php-xdebug

RUN sed -i -e "s|^;date.timezone =.*$|date.timezone = Asia/Tokyo|" /etc/php.ini

EXPOSE 80
CMD ["/usr/sbin/apachectl", "-D", "FOREGROUND"]

