<?php

namespace FeedBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FeedBundle:Default:index.html.twig');
    }
}
