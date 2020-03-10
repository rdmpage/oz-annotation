
# https://stackoverflow.com/a/51081949

SOURCES ?= $(wildcard *.pdf)

%.txt: %.pdf
	pdftotext -enc UTF-8 $< $@

%.xml: %.pdf
	./pdftoxml/pdftoxml -blocks $<	
	#./pdftoxml/pdftoxml -cutPages -blocks $<	


all: $(SOURCES:%.pdf=%.txt) $(SOURCES:%.pdf=%.xml)

clean:
	rm -f *.txt
	rm -f *.xml
	rm -rf *.xml_datapea2crab
	
	
        