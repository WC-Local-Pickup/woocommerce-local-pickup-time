#!/usr/bin/env bash

set -eu

# true is shell command and always return 0
# false always return 1
if [ -z "${CODESPACES}" ] ; then
	SITE_HOST="http://localhost:8080"
else
	SITE_HOST="https://${CODESPACE_NAME}-8080.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"
fi

PLUGIN_DIR=/workspaces/woocommerce-local-pickup-time

# Attempt to make ipv4 traffic have a higher priority than ipv6.
sudo sh -c "echo 'precedence ::ffff:0:0/96 100' >> /etc/gai.conf"

# Install Composer dependencies.
cd "${PLUGIN_DIR}"
echo "Installing Composer dependencies..."
COMPOSER_NO_INTERACTION=1 COMPOSER_ALLOW_XDEBUG=0 COMPOSER_MEMORY_LIMIT=-1 composer install --no-progress --quiet

# Install NPM dependencies.
cd "${PLUGIN_DIR}"
if [ ! -d "node_modules" ]; then
	echo "Installing NPM dependencies..."
	npm ci
fi

# Add a wait to ensure the database is up and available.
sleep 5

# Setup the WordPress environment.
cd "/app"
if ! wp core is-installed 2>/dev/null; then
	echo "Setting up WordPress at $SITE_HOST"
	wp core install --url="$SITE_HOST" --title="WC Local Pickup Development" --admin_user="admin" --admin_email="admin@example.com" --admin_password="password" --skip-email --quiet
	echo "Importing WooCommerce sample products..."
	# Activate required plugins for content import.
	wp plugin activate action-scheduler woocommerce wordpress-importer
	# Import sample products.
	wp import wp-content/plugins/woocommerce/sample-data/sample_products.xml --authors=create
fi

if wp core is-installed 2>/dev/null; then
	echo "Activating required development plugins.."
	wp --quiet plugin activate \
		action-scheduler \
		debug-bar \
		debug-bar-actions-and-filters-addon \
		display-environment-type \
		health-check \
		query-monitor \
		transients-manager \
		woo-order-test \
		woocommerce \
		wordpress-importer \
		wp-mail-logging

	echo "Activating required development theme.."
	wp --quiet theme activate storefront
fi

echo "Done!"
