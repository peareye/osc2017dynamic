FROM 1and1internet/ubuntu-16-apache-php-7.2
COPY apache-host.conf /etc/apache2/sites-available/000-default.conf
RUN apt-get update && apt-get install nano 

# Configure sendmail
RUN apt-get install -y sendmail
COPY configmail.sh /
RUN chmod +x /configmail.sh
CMD /configmail.sh
