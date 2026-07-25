#!/usr/bin/env bash
# Deploy Fixito on dolibarrcrm.artexxpro.ir (run on server as deploy user)
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "${SCRIPT_DIR}/../../../.." && pwd)"

if [[ -f "${REPO_ROOT}/deploy/artexxpro/deploy.sh" ]]; then
	exec env FIXITO_WORKDIR="${DOLIBARR_ROOT:-/opt/cicd/apps/dolibarrcrm}" \
		FIXITO_GIT_BRANCH="${BRANCH:-develop}" \
		bash "${REPO_ROOT}/deploy/artexxpro/deploy.sh"
fi

DOLIBARR_ROOT="${DOLIBARR_ROOT:-/var/www/dolibarrcrm/htdocs}"
BRANCH="${BRANCH:-develop}"

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
echo "    https://dolibarrcrm.artexxpro.ir/fixito/admin/deploy.php"
