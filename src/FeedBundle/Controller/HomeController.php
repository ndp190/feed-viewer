<?php

namespace FeedBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        // get feeds
        $feedRepo = $this->getDoctrine()->getRepository('FeedBundle:Feeds');
        $feedCategories = $feedRepo->getFeedCategories();

        return $this->render(
            'FeedBundle:Home:index.html.twig',
            array(
                'feed_categories' => $feedCategories
            )
        );
    }

    /**
     * @param Request $request
     * @param $name
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categoryDetailAction(Request $request, $name)
    {
        // get feeds
        $feedRepo = $this->getDoctrine()->getRepository('FeedBundle:Feeds');
        $feeds = $feedRepo->findByCategory($name);

        return $this->render(
            'FeedBundle:Home:category-detail.html.twig',
            array(
                'feeds' => $feeds
            )
        );
    }
}
