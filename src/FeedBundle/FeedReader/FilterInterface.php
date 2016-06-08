<?php

namespace FeedBundle\FeedReader;

use FeedBundle\FeedReader\Parser\Item;

/**
 * Interface FilterInterface
 * @package FeedBundle\FeedReader
 */
interface FilterInterface
{
    /**
     * @param Item $item
     * @return mixed
     */
    public function isValid(Item $item);
}