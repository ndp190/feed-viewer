<?php

namespace FeedBundle\Command;

use FeedBundle\Entity\Feeds;
use FeedBundle\Entity\Items;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FetchRssCommand
 * @author Phuc NguyenD <ndp190@gmail.com>
 * @package FeedBundle\Command
 */
class FetchRssCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        // configure URL and category parameters
        $this->setName('rss:fetch')
            ->setDescription('Fetch rss from url')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'URL Location to fetch rss feeds'
            )
            ->addArgument(
                'category',
                InputArgument::REQUIRED,
                'Define your category name'
            )
            ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        // fetch rss
        $reader = $this->getContainer()->get('feed.reader');
        $url = $input->getArgument('url');
        $category = $input->getArgument('category');
        $category = $this->slugify($category);

        // get category date if category already in database
        $feedRepo = $em->getRepository('FeedBundle:Feeds');
        $feedEnt = $feedRepo->findOneByCategory($category);

        if ($feedEnt instanceof Feeds) {
            // set date to fetch latest items
            $dateToFeed = $feedEnt->getLastUpdate();
        } else {
            // set date to null to fetch all items
            $dateToFeed = null;
            // create new feed entity if not exists in database
            $feedEnt = new Feeds();
        }

        // fetch feed content
        $feed = $reader->getFeed($url, $dateToFeed);
        $output->writeln("Get feed content");

        // set feed entity properties
        $feedEnt->setUrl($url);
        $feedEnt->setCategory($category);
        $feedEnt->setTitle($feed->getTitle());
        $feedEnt->setLastUpdate($feed->getLastUpdate());

        // get feed's items
        $items = $feed->getItems();

        // loop through items to create entities
        $output->writeln("Insert feed's items..");
        foreach ($items as $key => $item) {
            // create item entity and set properties
            $itemEnt = new Items();
            $itemEnt->setFeed($feedEnt);
            $itemEnt->setTitle($item->getTitle());
            $itemEnt->setDescription($item->getDescription());
            $itemEnt->setLink($item->getLink());
            $itemEnt->setPublished($item->getPublished());
            // add item to feed
            $feedEnt->addItem($itemEnt);
        }

        // persist feed entity
        $em->persist($feedEnt);
        $em->flush();
        $em->clear();

        $totalItemsCount = count($items);

        // print command summary
        $output->writeln("{$totalItemsCount} inserted to {$feedEnt->getTitle()} feed (category: {$feedEnt->getCategory()}).");
        $output->writeln("Command done.");
    }

    /**
     * Slugify string
     *
     * @param $string
     * @return string
     */
    private function slugify($string)
    {
        // replace special utf8 character
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

        // strip unknown character
        $string = str_replace('?', '', $string);

        // replace space with dash
        $string = str_replace(' ', '-', $string);

        return strtolower($string);
    }
}