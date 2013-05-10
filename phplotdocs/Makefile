# $Id$
# Makefile for PHPlot Programmer's Reference Manual (Docbook-xml version)
# This uses xsltproc to generate HTML output.
# And it uses xsltproc, xmllint, and Apache fop for PDF output.

# Base name for some files:
NAME=phplot

# Local: PHP CLI to run examples:
PHP=/opt/php/bin/php

# Local: XSL Transformation Processor program:
XSLTPROC=xsltproc
# Local: XML formatting
XMLFORMAT=xmllint -format
# Local: FOP program:
FOP=/opt/fop/fop

# =====

# Name of this package, for the release target:
PACKAGE=phplotdocs

# XSL Style Sheet for HTML:
XSL_HTML=style.xsl
# XSL Style Sheet for PDF:
XSL_PDF=pdf.xsl

# Primary source file, which includes all the others:
MAIN=main.xml

# List of all source files:
SRC=$(MAIN) $(XSL_HTML) $(XSL_PDF) \
  install.xml quickstart.xml concepts.xml functions.xml \
  examples.xml webform.xml advanced.xml \
  reference.xml developer.xml changelog.xml

# Static image files used by the manual:
STATIC_IMAGES=\
  images/alpha-blending.png \
  images/colors.png \
  images/drop.png \
  images/encodeimage.png \
  images/error-image.png \
  images/fig-autorange-a.png \
  images/fig-autorange-b.png \
  images/fig-autorange-c.png \
  images/fonts.png \
  images/gdinfo.png \
  images/imagemap-bars.png \
  images/imagemap-nonembed.png \
  images/imagemap-pie.png \
  images/labels-bar.png \
  images/labels-horizbar.png \
  images/labels-pie.png \
  images/labels.png \
  images/layout.png \
  images/legend-layout.png \
  images/legendpos1.png \
  images/legendpos2.png \
  images/legendpos3.png \
  images/legendpos4.png \
  images/legendpos5.png \
  images/legendpos6.png \
  images/legendpos7.png \
  images/legendstyle.png \
  images/shape-bowtie.png \
  images/shape-box.png \
  images/shape-circle.png \
  images/shape-cross.png \
  images/shape-delta.png \
  images/shape-diamond.png \
  images/shape-dot.png \
  images/shape-down.png \
  images/shape-halfline.png \
  images/shape-home.png \
  images/shape-hourglass.png \
  images/shape-line.png \
  images/shape-plus.png \
  images/shape-rect.png \
  images/shape-star.png \
  images/shape-target.png \
  images/shape-triangle.png \
  images/shape-trianglemid.png \
  images/shape-up.png \
  images/shape-yield.png \
  images/thumbnail-area.png \
  images/thumbnail-bars.png \
  images/thumbnail-boxes.png \
  images/thumbnail-bubbles.png \
  images/thumbnail-candlesticks.png \
  images/thumbnail-candlesticks2.png \
  images/thumbnail-linepoints.png \
  images/thumbnail-lines.png \
  images/thumbnail-ohlc.png \
  images/thumbnail-pie.png \
  images/thumbnail-points.png \
  images/thumbnail-squared.png \
  images/thumbnail-stackedarea.png \
  images/thumbnail-stackedbars.png \
  images/thumbnail-thinbarline.png \
  images/ticksize.png \
  images/truecolor-formats.png \
  images/typical-grid.png \
  images/typical-legend.png \
  images/typical-titles.png \
  images/webform.png

# Files to add to release, in addition to *.html files:
ALSO=phplotdoc.css examples/ohlcdata.csv

# Release base name:
RELEASE=$(PACKAGE)

# PHP source files for examples:
EXAMPLES_PHP=\
  examples/annotate.php \
  examples/area1.php \
  examples/bars1.php \
  examples/bars2.php \
  examples/bars3.php \
  examples/bars4.php \
  examples/boxplot1.php \
  examples/boxplot2.php \
  examples/bubbles1.php \
  examples/colorcallbackbars.php \
  examples/colorcallbackgradient.php \
  examples/dlexformat.php \
  examples/drawmessage.php \
  examples/encodeimage.php \
  examples/histogram.php \
  examples/horizbar.php \
  examples/horizerror.php \
  examples/horizlinepts.php \
  examples/horizstackedbar.php \
  examples/horizthinbarline.php \
  examples/imagemapbars.php \
  examples/imagemapnonembed.php \
  examples/imagemappie.php \
  examples/legendshape.php \
  examples/legendshape1.php \
  examples/legendshape2.php \
  examples/linepoints1.php \
  examples/linepoints2.php \
  examples/lines1.php \
  examples/lines2.php \
  examples/ohlcbasic.php \
  examples/ohlccandlesticks.php \
  examples/ohlccandlesticks2.php \
  examples/outbreak.php \
  examples/pie1.php \
  examples/pie2.php \
  examples/pie3.php \
  examples/pieangle.php \
  examples/pielabeltype1.php \
  examples/pielabeltype2.php \
  examples/pielabeltype3.php \
  examples/pielabeltype4.php \
  examples/pielabeltype5.php \
  examples/pielabeltypedata.php \
  examples/points1.php \
  examples/points2.php \
  examples/qs1.php \
  examples/qs2.php \
  examples/qs3.php \
  examples/squared1.php \
  examples/stackedarea1.php \
  examples/stackedbars1.php \
  examples/stackedbars2.php \
  examples/stackedbars3.php \
  examples/thinbarline1.php \
  examples/thinbarline2.php \
  examples/twoplot1.php \
  examples/xtickanchor.php \
  examples/ytickanchor.php \
  examples/ytickanchor1.php \


# Example XML files, generated from PHP scripts:
EXAMPLES_XML=$(EXAMPLES_PHP:.php=.xml)

# Example image files. Most generate PNG but there are exception(s).
# Also some example scripts (in EXAMPLES_PHP) do not get run (because
# they don't generate images, for example). These are not included here.
EXAMPLES_IMAGES=\
  examples/annotate.png \
  examples/area1.png \
  examples/bars1.png \
  examples/bars2.png \
  examples/bars3.png \
  examples/bars4.png \
  examples/boxplot1.png \
  examples/boxplot2.png \
  examples/bubbles1.png \
  examples/colorcallbackbars.png \
  examples/colorcallbackgradient.png \
  examples/dlexformat.png \
  examples/drawmessage.png \
  examples/histogram.jpg \
  examples/horizbar.png \
  examples/horizerror.png \
  examples/horizlinepts.png \
  examples/horizstackedbar.png \
  examples/horizthinbarline.png \
  examples/legendshape.png \
  examples/legendshape1.png \
  examples/legendshape2.png \
  examples/linepoints1.png \
  examples/linepoints2.png \
  examples/lines1.png \
  examples/lines2.png \
  examples/ohlcbasic.png \
  examples/ohlccandlesticks.png \
  examples/ohlccandlesticks2.png \
  examples/outbreak.png \
  examples/pie1.png \
  examples/pie2.png \
  examples/pie3.png \
  examples/pieangle.png \
  examples/pielabeltype1.png \
  examples/pielabeltype2.png \
  examples/pielabeltype3.png \
  examples/pielabeltype4.png \
  examples/pielabeltype5.png \
  examples/points1.png \
  examples/points2.png \
  examples/qs1.png \
  examples/qs2.png \
  examples/qs3.png \
  examples/squared1.png \
  examples/stackedarea1.png \
  examples/stackedbars1.png \
  examples/stackedbars2.png \
  examples/stackedbars3.png \
  examples/thinbarline1.png \
  examples/thinbarline2.png \
  examples/twoplot1.png \
  examples/xtickanchor.png \
  examples/ytickanchor.png \
  examples/ytickanchor1.png \


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
pdf: $(NAME).pdf

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

# Build PDF Manual.
# This uses xsltproc and xmllint for the XML to FO conversion, even
# though fop can do that, because fop gives useless messages if it
# does the XML processing ("context not available").
# Also xsltproc is better about using XML catalogs.
$(NAME).pdf: $(SRC) $(ALSO) version.xml $(EXAMPLE_LIST) $(VARIABLE_LIST)
	$(XSLTPROC) $(XSL_PDF) $(MAIN) > $(NAME).fo0
	$(XMLFORMAT) $(NAME).fo0 > $(NAME).fo
	-rm $(NAME).fo0
	$(FOP) -catalog -fo $(NAME).fo -pdf $(NAME).pdf

# === Additional Targets ===

# Cleanup: deletes intermediate files, leaves manual.
clean:
	-rm -f html.mark version.xml
	-rm -f $(EXAMPLES_XML) $(EXAMPLE_LIST) $(VARIABLE_LIST)
	-rm -f $(NAME).fo

# All cleanup: Remove all generated files. Caution: Removes all HTML and PDF files!
allclean: clean
	-rm -f *.html *.pdf
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
	@{ echo -n '<programlisting><![CDATA['; \
      expand --tabs=8 $<; \
      echo ']]></programlisting>'; } > $@

# These rules run an example script through PHP to get an image file:
%.png: %.php
	$(PHP) $< > $@
%.jpg: %.php
	$(PHP) $< > $@

# Special cases: These examples call others so must be rebuilt if they change.
examples/ytickanchor1.png: examples/ytickanchor.php examples/ytickanchor1.php
examples/legendshape1.png: examples/legendshape.php examples/legendshape1.php
examples/pielabeltype1.png: examples/pielabeltype1.php examples/pielabeltypedata.php
examples/pielabeltype2.png: examples/pielabeltype2.php examples/pielabeltypedata.php
examples/pielabeltype3.png: examples/pielabeltype3.php examples/pielabeltypedata.php
examples/pielabeltype4.png: examples/pielabeltype4.php examples/pielabeltypedata.php
examples/pielabeltype5.png: examples/pielabeltype5.php examples/pielabeltypedata.php

# === For building the variable list documentation ===

$(VARIABLE_LIST): variables.list gen.vardoc 
	./gen.vardoc < variables.list > $(VARIABLE_LIST)

# ===
