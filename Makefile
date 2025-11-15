prepush:
	@echo "CMD: make prepush"
	@echo "USAGE: Runs bun prepush and composer prepush"
	docker compose exec -it php composer prepush && bun prepush

solo:
	@echo "CMD: make solo"
	@echo "USAGE: Start Solo from the php container"
	docker compose exec -it php php artisan solo

up:
	@echo "CMD: make up"
	@echo "USAGE: Start up local stack of services"
	docker compose up -d

down:
	@echo "CMD: make down"
	@echo "USAGE: Stop local stack of services"
	docker compose down

terminal_php:
	@echo "CMD: make terminal_php"
	@echo "USAGE: Start a terminal in the php container"
	docker compose exec -it php /bin/bash

stub_generate:
	@echo "CMD: make stub_generate"
	@echo "USAGE: Generates JSON Object Stubs from Enums"
	docker compose exec php composer generate-json-objects
