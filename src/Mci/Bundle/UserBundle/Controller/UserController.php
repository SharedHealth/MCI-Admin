<?php

namespace Mci\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function loginAction()
    {
        return $this->render('MciUserBundle:User:login.html.twig');
    }

    public function logoutAction()
    {
        return $this->render('MciUserBundle:User:logout.html.twig');
    }
}
