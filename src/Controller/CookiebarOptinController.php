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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/_cookiebarOptin", defaults={"_scope": "frontend"})
 */
class CookiebarOptinController extends AbstractController
{
    protected $session;

    public function __construct(ContainerInterface $container)
    {
        $this->session = $container->get('session');
    }

    /**
     * @Route("/{optinId}", requirements={"optinId"="\d+"}, methods={"GET"})
     */
    public function loadContent(int $optinId): Response
    {
        $this->initializeContaoFramework();

        $objPage = $this->session->get('objPage');
        $GLOBALS['objPage'] = $objPage;

        $contentModel = ContentModel::findById($optinId);

        if (null === $contentModel) {
            return new Response();
        }

        $contentModel->useCookiebarOptin = false;

        $res = Controller::getContentElement($optinId);
        $res = Controller::replaceInsertTags($res);
        $res = Controller::replaceDynamicScriptTags($res);

        return new Response($res);
    }
}
