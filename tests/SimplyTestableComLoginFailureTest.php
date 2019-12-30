<?php
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\PantherSandbox\Tests;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\DomElementIdentifier\ElementIdentifier;

class SimplyTestableComLoginFailureTest extends AbstractBaseTest
{
    public function testOpen()
    {
        self::$client->request('GET', 'https://simplytestable.com');

        $this->assertEquals('Simply Testable professional automated front-end web testing', self::$client->getTitle());
    }

    public function testFollowSignInLink()
    {
        $signInLink = $this->navigator->findOne(new ElementIdentifier(
            '.btn[href="https://gears.simplytestable.com/signin/"]',
            1
        ));

        $signInLink->click();
        self::$crawler = self::$client->refreshCrawler();
        $this->navigator->setCrawler(self::$crawler);

        $this->assertRegExp("/^Sign in /", self::$client->getTitle());
        $this->assertEquals("https://gears.simplytestable.com/signin/", self::$client->getCurrentURL());

        $this->assertTrue($this->navigator->has(new ElementIdentifier(
            'body.sign-in-render',
            1
        )));
    }

    public function testSubmitEmptySignInForm()
    {
        $formIdentifier = new ElementIdentifier('form[action="/signin/"]', 1);

        $emailInput = $this->navigator->findOne(
            (new ElementIdentifier('#email', 1))
                ->withParentIdentifier($formIdentifier)
        );

        $passwordInput = $this->navigator->findOne(
            (new ElementIdentifier('#password', 1))
                ->withParentIdentifier($formIdentifier)
        );

        $submitInput = $this->navigator->findOne(
            (new ElementIdentifier('button[type=submit]', 1))
                ->withParentIdentifier($formIdentifier)
        );

        $this->assertEquals('', $emailInput->getText());
        $this->assertEquals('', $passwordInput->getText());

        $submitInput->click();
        self::$crawler = self::$client->refreshCrawler();
        $this->navigator->setCrawler(self::$crawler);

        $this->assertRegExp("/^Sign in /", self::$client->getTitle());
        $this->assertEquals(
            "https://gears.simplytestable.com/signin/?stay-signed-in=1",
            self::$client->getCurrentURL()
        );

        $alert = $this->navigator->findOne(new ElementIdentifier(
            '.alert',
            1
        ));

        $this->assertEquals(
            "×\nSlow down! You're going to have to enter in your email address to sign in.",
            $alert->getText()
        );
    }
}
