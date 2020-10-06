<?php

namespace ComposerScript;

use Composer\Script\Event;
use TCPDF_FONTS;

class Installer
{
    public static function postUpdate(Event $event)
    {
        $composer = $event->getComposer();

        // do reinit  fonts fo tcpdf
        $fonts = [
            'ComposerScript/files/tcpdf_fonts/times.ttf',
            'ComposerScript/files/tcpdf_fonts/timesb.ttf',
            'ComposerScript/files/tcpdf_fonts/timesi.ttf',
            'ComposerScript/files/tcpdf_fonts/timesbi.ttf'
        ];

        echo "-- Remove old fonts \n";
        $outPath = TCPDF_FONTS::_getfontpath();
        $mask = $outPath . 'times*.*';
        foreach (glob($mask) as $item) {
            unlink($item);
            echo ' --- file ' . $item . " remove\n";
        }
        foreach ($fonts as $font) {
            // remove before create
            echo " -- Font " . $font;
            if (file_exists($font)) {
                TCPDF_FONTS::addTTFfont($font, '', '', 32, $outPath);
                echo " ready";
            } else {
                echo " not exist";
            }
            echo "\n";
        }
// do stuff
    }

    public static function postPackageUpdate(Event $event)
    {
        $packageName = $event->getOperation()
            ->getPackage()
            ->getName();
        echo "$packageName\n";
// do stuff
    }


}