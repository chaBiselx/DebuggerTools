FILTER ?= 

#all test
test : 
	@# Help: lance l'ensemble des test unitaire et fonctionnel
	@if [ -z "$(FILTER)" ]; then \
		php vendor/bin/phpunit; \
	else \
		php vendor/bin/phpunit --filter $(FILTER); \
	fi
