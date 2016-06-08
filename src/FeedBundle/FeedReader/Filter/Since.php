<?php

namespace FeedBundle\FeedReader\Filter;

use FeedBundle\FeedReader\FilterInterface;
use FeedBundle\FeedReader\Parser\Item;

class Since implements FilterInterface
{
    /** @var  \DateTime */
    private $date;

    /**
     * Since constructor.
     * @param \DateTime $date
     */
    public function __construct(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    public function isValid(Item $item)
    {
        if ($item->getPublished() instanceof \DateTime) {
            return $item->getPublished() > $this->getDate();
        }

        return false;
    }
}