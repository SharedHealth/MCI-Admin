<?php

namespace Mci\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MciCoreBundle:Default:index.html.twig');
    }
}
