# $Id$
# Makefile for PHPlot Programmer's Reference Manual (Docbook-xml version)
# This uses xsltproc to generate HTML output.
# The examples production assumes all examples/*.php are source files, and
# each produces a PNG image to stdout when run.

# Local: PHP CLI to run examples:
PHP=/opt/php/bin/php

# Local: XSL Transformation Processor program:
XSLTPROC=xsltproc

# =====

# Name of this package, for the release target:
PACKAGE=phplotdocs

# XSL Style Sheet for HTML:
XSL_HTML=style.xsl

# Primary source file, which includes all the others:
MAIN=main.xml

# List of all source files:
SRC=$(MAIN) $(XSL_HTML) \
  install.xml quickstart.xml concepts.xml functions.xml \
  examples.xml webform.xml callbacks.xml \
  reference.xml developer.xml changelog.xml

# Static image files used by the manual:
STATIC_IMAGES=\
  images/alpha-blending.png \
  images/colors.png \
  images/error-image.png \
  images/fonts.png \
  images/gdinfo.png \
  images/labels-pie.png \
  images/labels.png \
  images/layout.png \
  images/legend-layout.png \
  images/legendstyle.png \
  images/ticksize.png \
  images/typical-grid.png \
  images/typical-legend.png \
  images/typical-titles.png \
  images/webform.png \

# Files to add to release, in addition to *.html files:
ALSO=phplotdoc.css

# Release base name:
RELEASE=$(PACKAGE)

# All PHP example source files:
EXAMPLES_PHP=$(wildcard examples/*.php)

# Example XML files, generated from PHP scripts:
EXAMPLES_XML=$(EXAMPLES_PHP:.php=.xml)

# Example image files, also generated from PHP scripts (must be PNG):
EXAMPLES_IMAGES=$(EXAMPLES_PHP:.php=.png)

# Entity list file for examples:
# This is referenced by the manual source files, and is the file used
# for testing if the examples are up-to-date:
EXAMPLE_LIST=examples/list.xml

# Variables list: This is a generated file, referenced in main.xml
VARIABLE_LIST=variablelist.xml

# =====

# Targets:

all: html
html: html.mark
# Special version of the manual with required logo for
# Sourceforge project web pages. See the XSLT style sheet for details.
sfhtml:
	$(MAKE) -$(MAKEFLAGS) XSLTPROCFLAGS='--param footerlogo 1' html

# Version date stamp file:
version.xml:
	echo "<!ENTITY version \"`date +%Y-%m-%d`\">" > $@

# Build (X)HTML Manual. The html.mark file is just used for dependency.
html.mark: $(SRC) $(ALSO) version.xml $(EXAMPLE_LIST) $(VARIABLE_LIST)
	$(XSLTPROC) $(XSLTPROCFLAGS) $(XSL_HTML) $(MAIN)
	touch html.mark

# Just build the example image files. (Can't use 'examples' as a target,
# because there is a directory by that name which interferes.)
exampleimages: $(EXAMPLES_IMAGES)

# Cleanup: deletes intermediate files, leaves manual.
clean:
	-rm -f html.mark version.xml
	-rm -f $(EXAMPLES_XML) $(EXAMPLE_LIST) $(VARIABLE_LIST)

# All cleanup: Remove all generated files. Caution: Removes all HTML files!
allclean: clean
	-rm -f *.html
	-rm -f $(EXAMPLES_IMAGES)

# Make a release of the HTML documentation only:
release: html
	@if [ -d $(RELEASE) ]; then echo "Error: Release directory $(RELEASE) already exists"; exit 1; fi
	$(MAKE) -$(MAKEFLAGS) DESTDIR=$(RELEASE) install
	zip -r $(RELEASE).zip $(RELEASE)
	rm -rf $(RELEASE)
	@echo Created release $(RELEASE) as $(RELEASE).zip

# Install the manual somewhere. This is used by 'make release', and also
# for building the manual to read or upload to the web site.
# This requires DESTDIR be defined, e.g. make DESTDIR=/path/to install
# Note: If the manual isn't already built, this will do 'make html'
# (not sfhtml), so it is better to build the manual first.
install: html
	@if [ x$(DESTDIR) = x ]; then echo "Error: Must set DESTDIR for make install"; exit 1; fi
	mkdir -p $(DESTDIR) $(DESTDIR)/images $(DESTDIR)/examples
	cp *.html $(ALSO) $(DESTDIR)
	cp $(STATIC_IMAGES) $(DESTDIR)/images
	cp $(EXAMPLES_IMAGES) $(DESTDIR)/examples

# === For building the examples ===

# Create the example entity list file.
# The XML and IMAGES files are listed as a dependency so the whole thing
# will be rebuilt if an example source changes.
$(EXAMPLE_LIST): $(EXAMPLES_PHP) $(EXAMPLES_XML) $(EXAMPLES_IMAGES)
	@echo "  $@"
	@{ echo "<!-- Automatically generated: entity list file -->"; \
      for f in $(notdir $(EXAMPLES_PHP:.php=)); do \
        echo "<!ENTITY $$f SYSTEM \"$$f.xml\">"; done; } > $@

# Rules for examples:

# This rule turns a PHP file into an XML file for inclusion as an example.
%.xml: %.php
	@echo "  $@"
	@{ echo '<programlisting><![CDATA['; \
      expand --tabs=8 $<; \
      echo ']]></programlisting>'; } > $@

# This rule runs an example through PHP to get an image file:
%.png: %.php
	$(PHP) $< > $@

# === For building the variable list documentation ===

$(VARIABLE_LIST): variables.list gen.vardoc 
	./gen.vardoc < variables.list > $(VARIABLE_LIST)

# ===
