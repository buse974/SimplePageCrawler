<?php

/*
 * This file is part of the SimplePageCrawler package.
 * @copyright Copyright (c) 2012 Blanchon Vincent - France (http://developpeur-zend-framework.fr - blanchon.vincent@gmail.com)
 */

namespace SimplePageCrawler;

use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Stdlib\Exception\InvalidArgumentException;

class PageCrawler
{
    /**
     * The http client
     * @var Client
     */
    protected $httpClient;

    /**
     * Crawl & parse the uri
     * @param string $uri
     * @return Response
     */
    public function get($uri)
    {
        if($uri instanceof Request) {
            $uri = $uri->getUri();
        }
        if(!is_string($uri)) {
            throw new InvalidArgumentException(
                'Uri must a string or instance of HttpRequest'
            );
        }

        $httpClient = $this->getHttpClient();
        $httpClient->setUri($uri);
        $source = $httpClient->send();
        $charset = $source->getHeaders()->get('content-type')->getCharset();
        $charset = (!empty($charset))?$charset:'utf-8';
        $content = $source->getBody();
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', $charset);
        $response = PageParser::fromPageSource($content, $uri);
        return $response;
    }

    /**
     * Get the http client
     * @return Client
     */
    public function getHttpClient()
    {
        if(null === $this->httpClient) {
           $this->setHttpClient(new Client());
        }
        return $this->httpClient;
    }

    /**
     * Set the http client
     * @param Client $httpClient
     * @return PageCrawler
     */
    public function setHttpClient(Client $httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }
}
