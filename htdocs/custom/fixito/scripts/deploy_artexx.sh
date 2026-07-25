#!/usr/bin/env bash
# Deploy Fixito on dolibarrcrm.artexxpro.ir (run on server as deploy user)
set -euo pipefail

DOLIBARR_ROOT="${DOLIBARR_ROOT:-/var/www/dolibarrcrm/htdocs}"
BRANCH="${BRANCH:-cursor/fixito-iran-crm}"

echo "==> Pull latest code (${BRANCH})"
cd "${DOLIBARR_ROOT}/.."
git fetch origin
git checkout "${BRANCH}" 2>/dev/null || git checkout develop && git pull origin develop

echo "==> Ensure custom path in conf.php"
CONF="${DOLIBARR_ROOT}/conf/conf.php"
if ! grep -q "dolibarr_main_url_root_alt" "$CONF" 2>/dev/null; then
  cat >> "$CONF" <<'PHP'

// Fixito / external modules (Artexx)
$dolibarr_main_url_root_alt='/custom';
$dolibarr_main_document_root_alt='__DOLIBARR_ROOT__/custom/';
PHP
  sed -i "s|__DOLIBARR_ROOT__|${DOLIBARR_ROOT}|g" "$CONF"
fi

echo "==> Fixito files"
test -f "${DOLIBARR_ROOT}/custom/fixito/core/modules/modFixito.class.php"

echo "==> Done. Open as admin:"
echo "    https://dolibarrcrm.artexxpro.ir/custom/fixito/admin/deploy.php"
echo "    (or /fixito/admin/deploy.php if alt root is configured)"
