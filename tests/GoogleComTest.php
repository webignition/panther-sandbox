<?php

namespace webignition\CreateTaskCollectionPayload\Tests;

use Facebook\WebDriver\Remote\RemoteWebElement;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Panther\Client;

class GoogleComTest extends TestCase
{
    public function testQuery()
    {
        $client = Client::createChromeClient();
        $crawler = $client->request('GET', 'https://www.google.com');

        $this->assertEquals('Google', $client->getTitle());

        /* @var RemoteWebElement $input */
        $input = $crawler->filter('.gLFyf.gsfi')->getElement(0);
        $this->assertInstanceOf(RemoteWebElement::class, $input);

        /* @var RemoteWebElement $searchButton */
        $searchButton = $crawler->filter('.FPdoLc.VlcLAe input[name=btnK]')->getElement(0);
        $this->assertInstanceOf(RemoteWebElement::class, $searchButton);

        $input->sendKeys("example");
        $searchButton->submit();

        $this->assertEquals('example - Google Search', $client->getTitle());
    }
}
