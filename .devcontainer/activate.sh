#!/usr/bin/env bash

set -eu

# Activate the plugin.
cd "/app"
echo "Activating plugin..."
if ! wp plugin is-active woocommerce-local-pickup-time-select 2>/dev/null; then
	wp plugin activate woocommerce-local-pickup-time-select --quiet
fi

echo "Done!"
