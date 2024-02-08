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

use Contao\BackendUser;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Image\ImageSizes;
use Contao\System;
use Symfony\Bundle\SecurityBundle\Security;

class CookiebarOptinCallbackListener
{
    public function __construct(
        private readonly ImageSizes $imageSizes,
        private readonly Security $security,
    ) {}

    #[AsCallback('tl_content', 'fields.cookieId.load')]
    public function loadLanguageFile($value)
    {
        System::loadLanguageFile('tl_cookie');

        return $value;
    }

    #[AsCallback('tl_content', 'fields.cookiebarOptinImageSize.options')]
    public function imageSizeOptions(): array
    {
        $user = $this->security->getUser();

        if (!$user instanceof BackendUser) {
            return [];
        }

        return $this->imageSizes->getOptionsForUser($user);
    }
}
