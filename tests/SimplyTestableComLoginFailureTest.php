<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace webignition\PantherSandbox\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use webignition\DomElementLocator\ElementLocator;
use webignition\SymfonyDomCrawlerNavigator\Navigator;

class SimplyTestableComLoginFailureTest extends TestCase
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
        self::$client->request('GET', 'https://simplytestable.com');

        $this->assertEquals('Simply Testable professional automated front-end web testing', self::$client->getTitle());
    }

    public function testFollowSignInLink()
    {
        $signInLink = $this->domCrawlerNavigator->findOne(new ElementLocator(
            '.btn[href="https://gears.simplytestable.com/signin/"]',
            1
        ));

        $signInLink->click();
        self::$crawler = self::$client->refreshCrawler();
        $this->domCrawlerNavigator->setCrawler(self::$crawler);

        $this->assertRegExp("/^Sign in /", self::$client->getTitle());
        $this->assertEquals("https://gears.simplytestable.com/signin/", self::$client->getCurrentURL());

        $this->assertTrue($this->domCrawlerNavigator->has(new ElementLocator(
            'body.sign-in-render',
            1
        )));
    }

    public function testSubmitEmptySignInForm()
    {
        $formLocator = new ElementLocator(
            'form[action="/signin/"]',
            1
        );

        $emailInput = $this->domCrawlerNavigator->findOne(
            new ElementLocator(
                '#email',
                1
            ),
            $formLocator
        );

        $passwordInput = $this->domCrawlerNavigator->findOne(
            new ElementLocator(
                '#password',
                1
            ),
            $formLocator
        );

        $submitInput = $this->domCrawlerNavigator->findOne(
            new ElementLocator(
                'button[type=submit]',
                1
            ),
            $formLocator
        );

        $this->assertEquals('', $emailInput->getText());
        $this->assertEquals('', $passwordInput->getText());

        $submitInput->click();
        self::$crawler = self::$client->refreshCrawler();
        $this->domCrawlerNavigator->setCrawler(self::$crawler);

        $this->assertRegExp("/^Sign in /", self::$client->getTitle());
        $this->assertEquals(
            "https://gears.simplytestable.com/signin/?stay-signed-in=1",
            self::$client->getCurrentURL()
        );

        $alert = $this->domCrawlerNavigator->findOne(new ElementLocator(
            '.alert',
            1
        ));

        $this->assertEquals(
            "×\nSlow down! You're going to have to enter in your email address to sign in.",
            $alert->getText()
        );
    }
}
