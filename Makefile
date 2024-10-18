# flags 
MAKEFLAGS += --no-print-directory

# Couleurs for echo
GREEN = \033[0;32m
YEllOW = \033[0;33m
RED = \033[0;31m
NC = \033[0m  # No Color (reinit)

help:
	@printf "%-20s %s\n" "Target" "Description"
	@printf "%-20s %s\n" "------" "-----------"
	@make -pqR : 2>/dev/null \
        | awk -v RS= -F: '/^# File/,/^# Finished Make data base/ {if ($$1 !~ "^[#.]") {print $$1}}' \
        | sort \
        | egrep -v -e '^[^[:alnum:]]' -e '^$@$$' \
        | xargs -I _ sh -c 'printf "%-20s " _; make _ -nB | (grep -i "^# Help:" || echo "") | tail -1 | sed "s/^# Help: //g"'


include makeFileFolder/Install.mk
include makeFileFolder/Test.mk
include makeFileFolder/DockerTest.mk