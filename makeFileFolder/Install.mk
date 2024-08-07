MYDIR=./vendor/chabiselx/debuggertools
BRANCH=master

#all test
install : 
	@# Help: install in venodr to simulate composer install
	rm -rf $(MYDIR)
	mkdir -p $(MYDIR)
	git clone --branch $(BRANCH) https://github.com/chaBiselx/DebuggerTools.git $(MYDIR)

