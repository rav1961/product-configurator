.PHONY: list setup-dev backend frontend

list:
	@echo "Available commands:"
	@echo "  make setup-dev   - run the development setup script"
	@echo "  make backend     - open a shell inside the backend container"
	@echo "  make frontend    - open a shell inside the frontend container"

setup-dev:
	./scripts/setup-dev.sh

backend:
	docker compose exec php bash

frontend:
	docker compose exec node bash