#!/usr/bin env

tee /etc/apache2/sites-available/omekas.conf << EOF
<VirtualHost *:80>
    ServerName $(hostname)
    DocumentRoot /home/omekas/omeka-s
    ErrorLog /home/omekas/logs/apache.error.log
    CustomLog /home/omekas/logs/apache.access.log common

    <Directory /home/omekas/omeka-s>
        Require all granted
        AllowOverride all
    </Directory>
</VirtualHost>
EOF

a2ensite omekas
a2dissite 000-default
a2enmod rewrite proxy proxy_fcgi
a2enconf php8.1-fpm
systemctl restart apache2
sed -i -e 's/^user =.*/user = drone/' -e 's/^group =.*/group = drone/' /etc/php/8.1/fpm/pool.d/www.conf
systemctl restart php8.1-fpm
