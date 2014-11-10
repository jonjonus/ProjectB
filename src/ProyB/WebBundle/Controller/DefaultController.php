<?php

namespace ProyB\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function testAction()
    {
        return $this->render('ProyBWebBundle::test.html.twig');
    }
}
