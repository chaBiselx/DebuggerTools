
docker-test:
	$(MAKE) .check-docker
	@$(MAKE) .run-test-internal CONTAINER_NAME=debuggertools-php73-1 VERSION="PHP 7.3"
	@$(MAKE) .run-test-internal CONTAINER_NAME=debuggertools-php81-1 VERSION="PHP 8.1"
	@$(MAKE) .run-test-internal CONTAINER_NAME=debuggertools-php80-1 VERSION="PHP 8.0"

.check-docker:
	@if [ -f /.dockerenv ] || [ -f /proc/1/cgroup ] && grep -q docker /proc/1/cgroup; then \
		echo "$(RED)Abandon... You're inside a Docker container$(NC)"; \
		exit 1; \
	else \
		echo "$(GREEN)Not running inside a Docker container$(NC)"; \
	fi

.run-test-internal:
	@echo "$(GREEN)=================================================================$(NC)"
	@echo "$(GREEN)          PHP :  $(VERSION)$(NC) : $(CONTAINER_NAME)"
	@echo "$(GREEN)=================================================================$(NC)"
	@-docker exec $(CONTAINER_NAME) php vendor/bin/phpunit