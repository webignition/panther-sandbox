<?php

namespace webignition\PantherSandbox\Tests;

use Facebook\WebDriver\Remote\RemoteWebElement;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;

class GoogleComTest extends TestCase
{
    /**
     * @var Client
     */
    private static $client;

    /**
     * @var Crawler
     */
    private static $crawler;

    public static function setUpBeforeClass(): void
    {
        self::$client = Client::createChromeClient();
        self::$client->start();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        self::$client->quit();
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::$crawler = self::$client->refreshCrawler();
    }

    public function testOpen()
    {
        $this->setName('open https://www.google.com/');

        self::$client->request('GET', 'https://www.google.com/');

        $this->assertEquals('Google', self::$client->getTitle());
    }

    public function testQuery()
    {
        $this->setName('query "example"');

        /* @var RemoteWebElement $input */
        $input = self::$crawler->filter('.gLFyf.gsfi')->getElement(0);
        $this->assertInstanceOf(RemoteWebElement::class, $input);

        /* @var RemoteWebElement $searchButton */
        $searchButton = self::$crawler->filter('.FPdoLc.VlcLAe input[name=btnK]')->getElement(0);
        $this->assertInstanceOf(RemoteWebElement::class, $searchButton);

        if ($input instanceof RemoteWebElement) {
            $input->sendKeys('example');
        }

        if ($searchButton instanceof RemoteWebElement) {
            $searchButton->submit();
        }

        $this->assertEquals('example - Google Search', self::$client->getTitle());
    }
}
