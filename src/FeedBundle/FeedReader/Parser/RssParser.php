<?php

namespace FeedBundle\FeedReader\Parser;
use FeedBundle\FeedReader\FilterInterface;

/**
 * Class RssParser
 * @package FeedBundle\Parser
 */
class RssParser
{
    private $factory;

    /**
     * RssParser constructor.
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->dateFormats = array(
            \DateTime::RSS
        );
        $this->factory = $factory;
    }


    /**
     * Parse xml to feed data
     *
     * @param \SimpleXMLElement $xmlContent
     * @param Feed $feed
     * @param array $filters
     * @return Feed
     */
    public function parse(\SimpleXMLElement $xmlContent, Feed $feed, array $filters)
    {
        $feed->setTitle($xmlContent->channel->title);
        $feed->setUrl($xmlContent->channel->link);
        if (isset($xmlContent->channel->lastBuildDate)) {
            $this->setFeedLastUpdate($feed, $xmlContent->channel->lastBuildDate);
        } else if (isset($xmlContent->channel->pubDate)) {
            $this->setFeedLastUpdate($feed, $xmlContent->channel->pubDate);
        }

        foreach ($xmlContent->channel->item as $xmlElement) {
            $item = $this->factory->newItem();

            $item->setTitle($xmlElement->title);
            $item->setLink($xmlElement->link);
            $item->setDescription($xmlElement->description);

            if (isset($xmlElement->pubDate)) {
                $itemDate = $xmlElement->pubDate;
                $format = isset($format) ? $format : $this->guessDateFormat($itemDate);
                $itemDate = \DateTime::createFromFormat($format, $itemDate);
                $item->setPublished($itemDate);
            }

            $this->addValidItem($feed, $item, $filters);
        }

        return $feed;
    }

    /**
     * @param Feed $feed
     * @param $dateString
     */
    private function setFeedLastUpdate(Feed $feed, $dateString)
    {
        $format = $this->guessDateFormat($dateString);
        $date = \DateTime::createFromFormat($format, $dateString);
        $feed->setLastUpdate($date);
    }

    /**
     * @param $date
     * @return mixed
     */
    private function guessDateFormat($date)
    {
        foreach ($this->dateFormats as $format) {
            $test = \DateTime::createFromFormat($format, $date);
            if ($test instanceof \DateTime) {
                return $format;
            }
        }

        throw new \RuntimeException('Can guess date format : '.$date);
    }

    /**
     * @param Feed $feed
     * @param Item $item
     * @param array $filters
     * @return $this
     */
    private function addValidItem(Feed $feed, Item $item, array $filters = array())
    {
        if ($this->isValid($item, $filters)) {
            $feed->addItem($item);
        }

        return $this;
    }

    /**
     * @param Item $item
     * @param array $filters
     * @return bool
     */
    private function isValid(Item $item, array $filters = array())
    {
        $valid = true;
        foreach ($filters as $filter) {
            if ($filter instanceof FilterInterface) {
                $valid = $filter->isValid($item) ? $valid : false;
            }
        }

        return $valid;
    }
}