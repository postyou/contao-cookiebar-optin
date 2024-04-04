<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-cookiebar-optin.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoCookiebarOptin\Controller;

use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Controller\AbstractController;
use Contao\CoreBundle\Exception\NoLayoutSpecifiedException;
use Contao\CoreBundle\Routing\ResponseContext\CoreResponseContextFactory;
use Contao\CoreBundle\Util\LocaleUtil;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\System;
use Contao\ThemeModel;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\LocaleAwareInterface;

#[Route('/_cookiebarOptin', defaults: ['_scope' => 'frontend'])]
class CookiebarOptinController extends AbstractController
{
    public function __construct(
        private readonly LocaleAwareInterface $translator,

        #[Autowire(service: 'contao.routing.response_context_factory')]
        private readonly CoreResponseContextFactory $responseContextFactory,
    ) {}

    #[Route('/{optinId}', requirements: ['optinId' => '\d+'], methods: ['GET'])]
    public function loadContent(int $optinId, Request $request): Response
    {
        $this->initializeContaoFramework();

        $pageId = $request->query->getInt('pageId');

        $contentModel = ContentModel::findById($optinId);
        $pageModel = PageModel::findWithDetails($pageId);

        if (null === $contentModel || null === $pageModel) {
            return new Response();
        }

        $this->prepare($pageModel, $request);

        $contentModel->useCookiebarOptin = false;

        $content = Controller::getContentElement($optinId);

        return new Response($content);
    }

    private function prepare(PageModel $pageModel, Request $request): void
    {
        // Deprecated since Contao 4.0, to be removed in Contao 6.0
        $GLOBALS['TL_LANGUAGE'] = LocaleUtil::formatAsLanguageTag($pageModel->language);

        $locale = LocaleUtil::formatAsLocale($pageModel->language);

        $request->setLocale($locale);
        $this->translator->setLocale($locale);

        System::loadLanguageFile('default');

        $layoutModel = LayoutModel::findById($pageModel->layout);

        if (null === $layoutModel) {
            throw new NoLayoutSpecifiedException('No layout specified');
        }

        // Store the layout ID
        $pageModel->layoutId = $layoutModel->id;

        // Set the layout template and template group
        $pageModel->template = $layoutModel->template ?: 'fe_page';
        $pageModel->templateGroup = '';

        if ($themeModel = ThemeModel::findById($layoutModel->pid)) {
            $pageModel->templateGroup = $themeModel->templates;
        }

        $this->responseContextFactory->createContaoWebpageResponseContext($pageModel);

        $GLOBALS['objPage'] = $pageModel;
    }
}
