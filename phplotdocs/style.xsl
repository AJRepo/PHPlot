<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                version='1.0'
                xmlns="http://www.w3.org/TR/xhtml1/transitional"
                exclude-result-prefixes="#default">
<!-- $Id$ -->
<!-- Docbook XML Style sheet for PHPlot Reference Manual -->

<!-- Load the DocBook XML Style sheet for separate (chunked) XHTML files.
     If your XML catalog system is working, this URL will map into a local
     path with the latest XSL stylesheets.
     If not, replace the http:// URL with a file:/// URL.
-->
<xsl:import href="http://docbook.sourceforge.net/release/xsl/current/xhtml/chunk.xsl"/>

<!-- Encoding now defaults to utf-8; change it back to normal. -->
<xsl:param name="chunker.output.encoding" select="'ISO-8859-1'" />

<!-- Use meaningful output filenames -->
<xsl:param name="use.id.as.filename" select="'1'" />

<!-- Reference the HTML stylesheet -->
<xsl:param name="html.stylesheet" select="'phplotdoc.css'" />

<!-- Number sections, and include parent number -->
<xsl:param name="section.autolabel" select="'1'" />
<xsl:param name="section.label.includes.component.label" select="'1'" />

<!-- Other style controls -->
<xsl:param name="callout.graphics" select="'0'" />
<xsl:param name="generate.index" select="0" />
    <!-- The default anyway for XHTML? -->
<xsl:param name="make.valid.html" select="1" />
<xsl:param name="refentry.xref.manvol" select="0" />
<!-- These put the function name, not NAME, at the top of the ref page -->
<xsl:param name="refentry.generate.name" select="0" />
<xsl:param name="refentry.generate.title" select="1" />

<!-- TOC controls
  Not all of these are used. We want a book TOC and reference TOC, but
  no chapter or section TOCs.
-->
<xsl:param name="generate.toc">
appendix  toc,title
book      toc,title
chapter   nop
part      nop
component toc
preface   toc,title
reference toc,title
sect1     nop
sect2     nop
sect3     nop
sect4     nop
sect5     nop
section   toc
division  toc
</xsl:param>
<!-- Include sect1 in the TOC, but not sect2 -->
<xsl:param name="toc.section.depth" select="1" />
<!-- Don't include the section number in the hyperlink -->
<xsl:param name="autotoc.label.in.hyperlink" select="0" />

</xsl:stylesheet>
