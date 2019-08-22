<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace webignition\PantherSandbox\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use webignition\SymfonyDomCrawlerNavigator\Model\ElementLocator;
use webignition\SymfonyDomCrawlerNavigator\Model\LocatorType;
use webignition\SymfonyDomCrawlerNavigator\Navigator;

class GoogleComTest extends TestCase
{
    /**
     * @var Navigator
     */
    private $domCrawlerNavigator;

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
        $this->domCrawlerNavigator = Navigator::create(self::$crawler);
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

        $input = $this->domCrawlerNavigator->findElement(new ElementLocator(
            LocatorType::CSS_SELECTOR,
            '.gLFyf.gsfi',
            1
        ));

        $searchButton = $this->domCrawlerNavigator->findElement(new ElementLocator(
            LocatorType::CSS_SELECTOR,
            '.FPdoLc.VlcLAe input[name=btnK]',
            1
        ));

        $input->sendKeys('example');

        $searchButton->submit();
        self::$crawler = self::$client->getCrawler();
        $this->domCrawlerNavigator->setCrawler(self::$crawler);

        $this->assertEquals('example - Google Search', self::$client->getTitle());
    }
}
