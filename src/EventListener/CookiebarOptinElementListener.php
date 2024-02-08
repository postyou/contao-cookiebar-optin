<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-cookiebar-optin.
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoCookiebarOptin\EventListener;

use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Image\ImageFactoryInterface;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsHook('getContentElement')]
class CookiebarOptinElementListener
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ScopeMatcher $scopeMatcher,
        private readonly ImageFactoryInterface $imageFactory,
        private readonly string $projectDir,
    ) {}

    public function __invoke(ContentModel $contentModel, string $buffer, $element): string
    {
        $request = $this->requestStack->getCurrentRequest();

        // Don't block the element in backend preview
        if (!$request || $this->scopeMatcher->isBackendRequest($request)) {
            return $buffer;
        }

        if (!$contentModel->useCookiebarOptin) {
            return $buffer;
        }

        $GLOBALS['TL_CSS'][] = 'bundles/contaocookiebaroptin/cookiebar_optin.css|static';

        $template = new FrontendTemplate('cookiebar_optin');

        $template->id = $contentModel->id;
        $template->cookieId = $contentModel->cookieId;
        $template->text = $contentModel->cookiebarOptinText;
        $template->backgroundImage = null;
        $template->pageId = $request->attributes->get('pageModel')->id;

        if ($contentModel->cookiebarOptinImage) {
            $uuid = StringUtil::binToUuid($contentModel->cookiebarOptinImage);
            $file = FilesModel::findByUuid($uuid);

            $image = $this->imageFactory->create(
                "{$this->projectDir}/{$file->path}",
                StringUtil::deserialize($contentModel->cookiebarOptinImageSize)
            );

            $template->backgroundImage = $image->getUrl($this->projectDir, '/');
        }

        return $template->parse();
    }
}
