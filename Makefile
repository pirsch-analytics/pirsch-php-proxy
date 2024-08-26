.PHONY: release

release:
	mkdir -p pirsch
	composer install
	rm -r -f p/scripts
	cp -r vendor pirsch
	cp config.php pirsch
	cp -r p pirsch
	cp index.php pirsch
	zip -r "pirsch_proxy_v$(VERSION).zip" pirsch
	rm -r -f pirsch
