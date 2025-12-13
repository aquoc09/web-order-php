#!/bin/sh
set -e

# Kill mọi MPM khác (dù có bị inject)
rm -f /etc/apache2/mods-enabled/mpm_event.load \
      /etc/apache2/mods-enabled/mpm_worker.load

# Start Apache đúng cách (chỉ 1 lần)
exec apache2-foreground
