<?php

namespace webignition\PantherSandbox\Tests;

use Facebook\WebDriver\Remote\RemoteWebElement;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Panther\Client;

class SimplyTestableComLoginFailureTest extends TestCase
{
    public function testFoo()
    {
        $client = Client::createChromeClient();
        $client->request('GET', 'https://simplytestable.com');

        $this->assertEquals('Simply Testable professional automated front-end web testing', $client->getTitle());

        $crawler = $client->getCrawler();

        $signInLink = $crawler->filter(".btn[href='https://gears.simplytestable.com/signin/']")->getElement(0);

        if ($signInLink instanceof RemoteWebElement) {
            $signInLink->click();
        }

        $this->assertRegExp("/^Sign in /", $client->getTitle());
        $this->assertEquals("https://gears.simplytestable.com/signin/", $client->getCurrentURL());

        $crawler = $client->refreshCrawler();

        $this->assertCount(1, $crawler->filter('body.sign-in-render'));

        $form = $crawler->filter('form[action="/signin/"]');
        $emailInput = $form->filter('#email');
        $passwordInput = $form->filter('#password');
        $submitInput = $form->filter('button[type=submit]');

        $this->assertEmpty($emailInput->getText());
        $this->assertEmpty($passwordInput->getText());

        $submitInput->click();

        $this->assertRegExp("/^Sign in /", $client->getTitle());
        $this->assertEquals("https://gears.simplytestable.com/signin/?stay-signed-in=1", $client->getCurrentURL());

        $crawler = $client->refreshCrawler();

        $alert = $crawler->filter('.alert');
        $this->assertEquals(
            "×\nSlow down! You're going to have to enter in your email address to sign in.",
            $alert->text()
        );
    }
}
