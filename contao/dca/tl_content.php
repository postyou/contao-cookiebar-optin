<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-cookiebar-optin.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'useCookiebarOptin';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['useCookiebarOptin'] = 'cookieId,cookiebarOptinText,cookiebarOptinImage,cookiebarOptinImageSize';

$GLOBALS['TL_DCA']['tl_content']['fields']['useCookiebarOptin'] = [
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => "char(1) COLLATE ascii_bin NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cookieId'] = [
    'filter' => true,
    'inputType' => 'picker',
    'relation' => [
        'type' => 'hasOne',
        'load' => 'lazy',
        'table' => 'tl_cookie',
    ],
    'eval' => ['mandatory' => true],
    'sql' => 'int(10)',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cookiebarOptinText'] = [
    'search' => true,
    'inputType' => 'textarea',
    'eval' => ['mandatory' => true, 'basicEntities' => true, 'rte' => 'tinyMCE', 'helpWizard' => true],
    'explanation' => 'insertTags',
    'sql' => 'mediumtext NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cookiebarOptinImage'] = [
    'inputType' => 'fileTree',
    'eval' => ['filesOnly' => true, 'fieldType' => 'radio', 'tl_class' => 'clr'],
    'load_callback' => [
        ['tl_content', 'setSingleSrcFlags'],
    ],
    'sql' => 'binary(16) NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cookiebarOptinImageSize'] = [
    'inputType' => 'imageSize',
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
    'sql' => "varchar(128) COLLATE ascii_bin NOT NULL default ''",
];

$pm = PaletteManipulator::create()
    ->addField('useCookiebarOptin', 'protected')
;

// Add field to all palettes in tl_content
foreach ($GLOBALS['TL_DCA']['tl_content']['palettes'] as $name => $palette) {
    if ('__selector__' === $name) {
        continue;
    }

    $pm->applyToPalette($name, 'tl_content');
}

unset($name, $palette, $pm);
