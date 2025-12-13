#!/bin/sh
set -e

#  GỠ SẠCH MPM EVENT + WORKER (CẢ load + conf)
rm -f /etc/apache2/mods-enabled/mpm_event.load \
      /etc/apache2/mods-enabled/mpm_event.conf \
      /etc/apache2/mods-enabled/mpm_worker.load \
      /etc/apache2/mods-enabled/mpm_worker.conf

#  Đảm bảo prefork tồn tại
a2enmod mpm_prefork >/dev/null 2>&1 || true

# (optional) suppress FQDN warning
echo "ServerName localhost" >> /etc/apache2/apache2.conf

#  start apache đúng cách
exec apache2-foreground
