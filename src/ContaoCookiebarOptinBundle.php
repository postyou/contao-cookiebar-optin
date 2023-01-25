<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-cookiebar-optin.
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoCookiebarOptin;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoCookiebarOptinBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
