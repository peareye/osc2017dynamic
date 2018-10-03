#!/bin/bash

echo "127.0.0.1 localhost localhost.localdomain $(hostname)" >> /etc/hosts

yes Y | /usr/sbin/sendmailconfig

service apache2 restart

# To avoid closing the process, run this indefinitely
/bin/bash
