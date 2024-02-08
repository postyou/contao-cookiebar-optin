<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-cookiebar-optin.
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoCookiebarOptin\Controller;

use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Controller\AbstractController;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\PageModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/_cookiebarOptin', defaults: ['_scope' => 'frontend'])]
class CookiebarOptinController extends AbstractController
{
    public function __construct(
        private readonly InsertTagParser $insertTagParser,
    ) {}

    #[Route('/{optinId}', requirements: ['optinId' => '\d+'], methods: ['GET'])]
    public function loadContent(int $optinId, Request $request): Response
    {
        $this->initializeContaoFramework();

        $pageId = $request->query->getInt('pageId');

        $contentModel = ContentModel::findById($optinId);
        $pageModel = PageModel::findByPk($pageId);

        if (null === $contentModel || null === $pageModel) {
            return new Response();
        }

        $GLOBALS['objPage'] = $pageModel;

        $contentModel->useCookiebarOptin = false;

        $content = Controller::getContentElement($optinId);
        $content = $this->insertTagParser->replace($content);
        $content = Controller::replaceDynamicScriptTags($content);

        return new Response($content);
    }
}
