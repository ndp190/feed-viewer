<?php

namespace FeedBundle\FeedReader\Parser;

class Factory
{
    /** @var string */
    private $feedClass = 'FeedBundle\FeedReader\Parser\Feed';

    /** @var string */
    private $itemClass = 'FeedBundle\FeedReader\Parser\Item';

    /**
     * @return Feed
     * @throws \Exception
     */
    public function newFeed()
    {
        $feed = new $this->feedClass();
        if (!$feed instanceof Feed) {
            throw new \Exception("{$this->feedClass} is not a Feed instance");
        }

        return $feed;
    }

    /**
     * @return Item
     * @throws \Exception
     */
    public function newItem()
    {
        $item = new $this->itemClass();
        if (!$item instanceof Item) {
            throw new \Exception("{$this->feedClass} is not a Item instance");
        }

        return $item;
    }

    /**
     * @param $feedClass
     * @return $this
     * @throws \Exception
     */
    public function setFeedClass($feedClass)
    {
        if (!class_exists($feedClass)) {
            throw new \Exception("{$feedClass} does not exist");
        }

        $this->feedClass = $feedClass;

        return $this;
    }

    /**
     * @param $itemClass
     * @return $this
     * @throws \Exception
     */
    public function setItemClass($itemClass)
    {
        if (!class_exists($itemClass)) {
            throw new \Exception("{$itemClass} does not exist");
        }

        $this->itemClass = $itemClass;

        return $this;
    }
}
