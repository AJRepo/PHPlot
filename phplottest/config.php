<?php
# $Id$
# config.php : Site configuration for PHPlot tests
# This file contains system-dependent settings for testing PHPlot.
# The settings are:
#    phplot_test_ttfdir  = A directory with TrueType font files.
#    phplot_test_ttfonts[] = An array of font names. The available
#      array keys are:  (sans|serif|mono)(|bold|italic|bolditalic)

# This sample file has settings for windows (PHP_OS=WINNT) and others.
# The others are assumed to have the Linux/X.org fonts and font path.

# As of 12/2009 use the same font on Linux and Windows in order to be
# able to automatically compare the fonts. The WindowsXP system used for
# testing was found to have "DejaVu" font families installed, which is
# also on Linux. However, it is not known if DejaVu is standard in Windows
# or came in with some other software.

# 8/2010 Ubuntu for some reason puts the fonts into subdirectories,
#  so there is a special case for that.

if (PHP_OS == "WINNT") {

    # Explicitly tell it where to find the fonts:
    $phplot_test_ttfdir = $_SERVER['windir'] . '\\fonts\\';

    $phplot_test_ttfonts = array(
      'sans'            => 'DejaVuSans.ttf',                 # DejaVu Sans
      'sansbold'        => 'DejaVuSansBold.ttf',             # DejaVu Sans Bold
      'sansitalic'      => 'DejaVuSansOblique.ttf',          # DejaVu Sans Oblique
      'sansbolditalic'  => 'DejaVuSansBoldOblique.ttf',      # DejaVu Sans Bold Oblique
      'serif'           => 'DejaVuSerif.ttf',                # DejaVu Serif
      'serifbold'       => 'DejaVuSerifBold.ttf',            # DejaVu Serif Bold
      'serifitalic'     => 'DejaVuSerifItalic.ttf',          # DejaVu Serif Italic
      'serifbolditalic' => 'DejaVuSerifBoldItalic.ttf',      # DejaVu Serif Bold Italic
# Note: Windows font name = DejaVu Sans Mono, but filename is DejaVuMonoSans ?
      'mono'            => 'DejaVuMonoSans.ttf',             # DejaVu Sans Mono
      'monobold'        => 'DejaVuMonoSansBold.ttf',         # DejaVu Sans Mono Bold
      'monoitalic'      => 'DejaVuMonoSansOblique.ttf',      # DejaVu Sans Mono Oblique
      'monobolditalic'  => 'DejaVuMonoSansBoldOblique.ttf',  # DejaVu Sans Mono Bold Oblique
    );

} elseif (file_exists('/usr/bin/ubuntu-support-status')) {
    # Special handling for Ubuntu: (and others?)
    $phplot_test_ttfdir = '/usr/share/fonts/truetype/';

    $phplot_test_ttfonts = array(
      'sans'            => 'ttf-dejavu/DejaVuSans.ttf',
      'sansbold'        => 'ttf-dejavu/DejaVuSans-Bold.ttf',
      'sansitalic'      => 'ttf-dejavu/DejaVuSans-Oblique.ttf',
      'sansbolditalic'  => 'ttf-dejavu/DejaVuSans-BoldOblique.ttf',
      'serif'           => 'ttf-dejavu/DejaVuSerif.ttf',
      'serifbold'       => 'ttf-dejavu/DejaVuSerif-Bold.ttf',
      'serifitalic'     => 'ttf-dejavu/DejaVuSerif-Italic.ttf',
      'serifbolditalic' => 'ttf-dejavu/DejaVuSerif-BoldItalic.ttf',
      'mono'            => 'ttf-dejavu/DejaVuSansMono.ttf',
      'monobold'        => 'ttf-dejavu/DejaVuSansMono-Bold.ttf',
      'monoitalic'      => 'ttf-dejavu/DejaVuSansMono-Oblique.ttf',
      'monobolditalic'  => 'ttf-dejavu/DejaVuSansMono-BoldOblique.ttf',
    );

} else {
    # X.org standard path for TrueType fonts:
    $phplot_test_ttfdir = '/usr/share/fonts/TTF/';

    # See note at top regarding use of DejaVu fonts.
    $phplot_test_ttfonts = array(
      'sans'            => 'DejaVuSans.ttf',                 # DejaVu Sans
      'sansbold'        => 'DejaVuSans-Bold.ttf',            # DejaVu Sans Bold
      'sansitalic'      => 'DejaVuSans-Oblique.ttf',         # DejaVu Sans Oblique
      'sansbolditalic'  => 'DejaVuSans-BoldOblique.ttf',     # DejaVu Sans Bold Oblique
      'serif'           => 'DejaVuSerif.ttf',                # DejaVu Serif
      'serifbold'       => 'DejaVuSerif-Bold.ttf',           # DejaVu Serif Bold
      'serifitalic'     => 'DejaVuSerif-Italic.ttf',         # DejaVu Serif Italic
      'serifbolditalic' => 'DejaVuSerif-BoldItalic.ttf',     # DejaVu Serif Bold Italic
      'mono'            => 'DejaVuSansMono.ttf',             # DejaVu Sans Mono
      'monobold'        => 'DejaVuSansMono-Bold.ttf',        # DejaVu Sans Mono Bold
      'monoitalic'      => 'DejaVuSansMono-Oblique.ttf',     # DejaVu Sans Mono Oblique
      'monobolditalic'  => 'DejaVuSansMono-BoldOblique.ttf', # DejaVu Sans Mono Bold Oblique
    );

}
