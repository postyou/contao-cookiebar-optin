<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-cookiebar-optin.
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoCookiebarOptin\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\System;

/**
 * @Callback(table="tl_content", target="fields.cookieId.load")
 */
class CookiebarOptinCallbackListener
{
    public function __invoke($value)
    {
        System::loadLanguageFile('tl_cookie');

        return $value;
    }
}