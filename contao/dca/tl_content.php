<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-cookiebar-optin.
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\System;

$GLOBALS['TL_DCA']['tl_content']['fields']['useCookiebarOptin'] = [
    'exclude' => true,
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) COLLATE ascii_bin NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cookieId'] = [
    'exclude' => true,
    'filter' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_cookie.CONCAT(title," (",type,")")',
    'eval' => ['mandatory' => true],
    'sql' => 'int(10)',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cookiebarOptinText'] = [
    'exlude' => true,
    'search' => true,
    'inputType' => 'textarea',
    'eval' => ['mandatory' => true, 'rte' => 'tinyMCE', 'helpWizard' => true],
    'explanation' => 'insertTags',
    'sql' => 'mediumtext NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cookiebarOptinImage'] = [
    'exclude' => true,
    'inputType' => 'fileTree',
    'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr'],
    'load_callback' => [
        ['tl_content', 'setSingleSrcFlags'],
    ],
    'sql' => 'binary(16) NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cookiebarOptinImageSize'] = [
    'exclude' => true,
    'inputType' => 'imageSize',
    'options' => System::getImageSizes(),
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
    'sql' => "varchar(64) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'useCookiebarOptin';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['useCookiebarOptin'] = '
    cookieId,cookiebarOptinText,cookiebarOptinImage,cookiebarOptinImageSize
';

$palettes = array_keys($GLOBALS['TL_DCA']['tl_content']['palettes']);

// Add field to all palettes in tl_content
foreach ($palettes as $palette) {
    if ('__selector__' === $palette) {
        continue;
    }

    PaletteManipulator::create()
        ->addField('useCookiebarOptin', 'protected')
        ->applyToPalette($palette, 'tl_content')
    ;
}
