#!/usr/bin/env bash

DOCKER_DEV_DOMAIN=${DOCKER_DEV_DOMAIN-localhost}
DOCKER_DEV_PORT=${DOCKER_DEV_PORT-8080}

# Install WordPress.
wp core install \
  --title="WooCommerce Local Pickup Time" \
  --admin_user="wordpress" \
  --admin_password="wordpress" \
  --admin_email="admin@example.com" \
  --url="http://${DOCKER_DEV_DOMAIN}:${DOCKER_DEV_PORT}" \
  --skip-email

# Update permalink structure.
wp option update permalink_structure "/%year%/%monthnum%/%postname%/" --skip-themes --skip-plugins

# Activate WooCommerce.
wp plugin activate woocommerce

# Activate plugin.
wp plugin activate woocommerce-local-pickup-time-select
