<?php

namespace FeedBundle\FeedReader;

use FeedBundle\FeedReader\Filter\Since;
use FeedBundle\FeedReader\Parser\Factory;
use FeedBundle\FeedReader\Parser\RssParser;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FeedReader
 * @package FeedBundle\FeedReader
 */
class FeedReader
{
    /** @var  Factory */
    private $factory;

    /** @var  RssParser */
    private $rssParser;

    /**
     * FeedReader constructor.
     * @param Factory $factory
     * @param RssParser $rssParser
     */
    public function __construct(Factory $factory, RssParser $rssParser)
    {
        $this->factory = $factory;
        $this->rssParser = $rssParser;
    }

    /**
     * @param string $url
     * @param mixed $arg
     * @return Parser\Feed
     */
    public function getFeed($url, $arg = null)
    {
        if ($arg instanceof \DateTime) {
            return $this->getFeedSince($url, $arg);
        }

        return $this->getFilteredFeed($url, array());
    }

    /**
     * Get feed since date
     *
     * @param string $url
     * @param \DateTime $since
     * @return Parser\Feed
     */
    public function getFeedSince($url, \DateTime $since)
    {
        $filters = array(
            new Since($since)
        );

        return $this->getFilteredFeed($url, $filters);
    }

    /**
     * Get feed with filter conditions
     *
     * @param $url
     * @param array $filters
     * @return Parser\Feed
     * @throws \Exception
     */
    public function getFilteredFeed($url, array $filters)
    {
        $responseBody = $this->getResponseBody($url);
        $xmlContent =  new \SimpleXMLElement($responseBody);

        return $this->rssParser->parse($xmlContent, $this->factory->newFeed(), $filters);
    }

    /**
     * Get response body from url request
     *
     * @param string $url
     * @return string
     */
    private function getResponseBody($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_TIMECONDITION, CURL_TIMECOND_IFMODSINCE);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
        curl_setopt($curl, CURLOPT_MAXREDIRS, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($curl);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headerCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $body = substr($response, $headerSize);
        curl_close($curl);

        if (!in_array($headerCode, array(
            Response::HTTP_OK,
            Response::HTTP_MOVED_PERMANENTLY,
            Response::HTTP_FOUND
        ))) {
            throw new \RuntimeException('Can not get feed from url');
        }

        return $body;
    }
}