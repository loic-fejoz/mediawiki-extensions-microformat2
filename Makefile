README.html: README.md
	markdown $< > $@

clean:
	rm -f *~ UFMicroFormat2/*~

distclean: clean
	rm -f README.html
