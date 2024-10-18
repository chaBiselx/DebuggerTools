
docker-build:
	@# Help: lance l'ensemble des test unitaire et fonctionnel de tous les container docker
	@$(MAKE) .check-env-local
	@$(MAKE) .docker-check-existing-contanier
	@$(MAKE) .docker-launch-build


.check-env-local:
	@# Help: check if .env.local exist
	@if [ ! -f .env.local ]; then \
		touch .env.local; \
		echo "###> docker ###" >> .env.local; \
		echo "#TIMEZONE=" >> .env.local; \
		echo "" >> .env.local; \
		echo "###> NGINX ###" >> .env.local; \
		echo "#PHP_EXT_PORT_1=" >> .env.local; \
		echo "#PHP_EXT_PORT_2=" >> .env.local; \
		echo "" >> .env.local; \
		echo "###> BDD ###" >> .env.local; \
		echo "#BDD_EXT_PORT=" >> .env.local; \
		echo "" >> .env.local; \
		echo "$(YEllOW).env.local file created and variables added.$(NC)"; \
	else \
		echo "$(GREEN).env.local file already exists.$(NC)"; \
	fi

.docker-check-existing-contanier:
	@if [ -z "$$(docker ps -q -f name=$(CONTAINER_NAME_PHP))" ]; then \
		echo ""; \
	else \
		$(MAKE) .confim-build; \
	fi

.confim-build:
	@echo 
	@echo "$(YEllOW) $(CONTAINER_NAME_PHP) existing' : are you sure you want to restart the build ? (y/N)$(NC)";
	@read confirm; \
	if [ "$$confirm" != "y" ]; then \
		echo "$(RED)Abandon...$(NC)"; \
		exit 1; \
	fi
	@echo ''

.docker-launch-build:
	@echo "$(GREEN)Launching the docker build $(NC) "
	@docker compose --env-file .env.local up --build --force-recreate -d