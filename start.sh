#!/bin/sh

# Replace PORT in nginx config
sed -i "s/listen 8080;/listen ${PORT:-8080};/g" /etc/nginx/nginx.conf
sed -i "s/listen \[::\]:8080;/listen [::]:${PORT:-8080};/g" /etc/nginx/nginx.conf

# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf