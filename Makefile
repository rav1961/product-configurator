setup-dev:
	./scripts/setup-dev.sh

backend:
	docker compose exec backend bash

frontend:
	docker compose exec frontend bash
