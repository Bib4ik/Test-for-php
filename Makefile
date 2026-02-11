start:
	docker compose up -d

stop:
	docker compose down

setup:
	docker compose up -d --build

restart:
	docker compose restart

ps:
	docker compose ps