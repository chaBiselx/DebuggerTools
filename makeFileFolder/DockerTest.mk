
docker-test:
	@# Help: lance l'ensemble des test unitaire et fonctionnel de tous les container docker
	$(MAKE) .check-docker
	@for container in $$(docker ps --filter "name=$(CONTAINER_NAME_PHP)" --format "{{.Names}}"); do \
		make .run-test-internal CONTAINER_NAME=$$container; \
    done


.check-docker:
	@if [ -f /.dockerenv ] || [ -f /proc/1/cgroup ] && grep -q docker /proc/1/cgroup; then \
		echo "$(RED)Abandon... You're inside a Docker container$(NC)"; \
		exit 1; \
	else \
		echo "$(GREEN)Not running inside a Docker container$(NC)"; \
	fi

.run-test-internal:
	@echo "$(GREEN)=================================================================$(NC)"
	@VERSION=$$(echo $(CONTAINER_NAME) | sed -E 's/.*php([0-9]*)_([0-9]*)-.*$$/\1.\2/'); \
		echo "$(GREEN)          PHP : $$VERSION $(NC) : $(CONTAINER_NAME)"
	@echo "$(GREEN)=================================================================$(NC)"
	@-docker exec $(CONTAINER_NAME) make test