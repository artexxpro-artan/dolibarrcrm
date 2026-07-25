#!/usr/bin/env bash
# Fixito deploy hook for dolibarrcrm (run on Artexx host: fixito-deploy-app.sh dolibarrcrm)
set -euo pipefail

APP_SLUG="${FIXITO_APP_SLUG:-dolibarrcrm}"
WORKDIR="${FIXITO_WORKDIR:-/opt/cicd/apps/${APP_SLUG}}"
BRANCH="${FIXITO_GIT_BRANCH:-develop}"
COMPOSE_FILE="${FIXITO_COMPOSE_FILE:-deploy/artexxpro/docker-compose.fixito.yml}"
ENV_FILE="${FIXITO_ENV_FILE:-.env.docker.artexxpro}"
CONTAINER_WEB="${FIXITO_CONTAINER_WEB:-dolibarrcrm}"

log() { echo "[fixito-deploy:${APP_SLUG}] $*"; }

if [[ ! -d "${WORKDIR}" ]]; then
	log "ERROR: workdir ${WORKDIR} not found"
	exit 1
fi

cd "${WORKDIR}"

if [[ -d .git ]]; then
	log "git pull ${BRANCH}"
	git fetch origin "${BRANCH}"
	git checkout "${BRANCH}"
	git pull --ff-only origin "${BRANCH}"
else
	log "WARN: no .git in ${WORKDIR}; skipping pull"
fi

if [[ ! -f "${ENV_FILE}" ]]; then
	log "ERROR: missing ${ENV_FILE} (copy from deploy/artexxpro/.env.docker.artexxpro.example)"
	exit 1
fi

if [[ ! -f "${COMPOSE_FILE}" ]]; then
	log "ERROR: missing ${COMPOSE_FILE}"
	exit 1
fi

log "docker compose up (${COMPOSE_FILE})"
docker compose -f "${COMPOSE_FILE}" --env-file "${ENV_FILE}" pull --quiet 2>/dev/null || true
docker compose -f "${COMPOSE_FILE}" --env-file "${ENV_FILE}" up -d --remove-orphans

log "wait for web container"
for _ in $(seq 1 30); do
	if docker exec "${CONTAINER_WEB}" curl -fsS http://127.0.0.1/ >/dev/null 2>&1; then
		break
	fi
	sleep 3
done

if docker exec "${CONTAINER_WEB}" test -f /var/www/html/custom/fixito/scripts/iranize_crm.php; then
	log "iranize CRM (modules, IRR, landing page)"
	docker exec "${CONTAINER_WEB}" php /var/www/html/custom/fixito/scripts/iranize_crm.php || log "WARN: iranize exited non-zero"
else
	log "WARN: fixito module not mounted yet"
fi

log "done — https://dolibarrcrm.artexxpro.ir/fixito/fixitoindex.php"
