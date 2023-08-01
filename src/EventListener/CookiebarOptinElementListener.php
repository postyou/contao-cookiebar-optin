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
use Contao\CoreBundle\Image\ImageFactoryInterface;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @Hook("getContentElement")
 */
class CookiebarOptinElementListener
{
    private $requestStack;
    private $scopeMatcher;
    private $imagefactory;
    private $projectDir;

    public function __construct(
        RequestStack $requestStack,
        ScopeMatcher $scopeMatcher,
        ImageFactoryInterface $imageFactory,
        string $projectDir
    ) {
        $this->requestStack = $requestStack;
        $this->scopeMatcher = $scopeMatcher;
        $this->imagefactory = $imageFactory;
        $this->projectDir = $projectDir;
    }

    public function __invoke(ContentModel $contentModel, string $buffer, $element): string
    {
        // Don't block the element in backend preview
        if ($this->scopeMatcher->isBackendRequest($this->requestStack->getCurrentRequest())) {
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

        if ($contentModel->cookiebarOptinImage) {
            $uuid = StringUtil::binToUuid($contentModel->cookiebarOptinImage);
            $file = FilesModel::findByUuid($uuid);

            $image = $this->imagefactory->create(
                "{$this->projectDir}/{$file->path}",
                StringUtil::deserialize($contentModel->cookiebarOptinImageSize)
            );

            $template->backgroundImage = $image->getUrl($this->projectDir, '/');
        }

        return $template->parse();
    }
}
