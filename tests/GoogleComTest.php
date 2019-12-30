<?php
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace webignition\PantherSandbox\Tests;

use webignition\BaseBasilTestCase\AbstractBaseTest;
use webignition\DomElementIdentifier\ElementIdentifier;

class GoogleComTest extends AbstractBaseTest
{
    public function testOpen()
    {
        $this->setName('open https://www.google.com/');

        self::$client->request('GET', 'https://www.google.com/');

        $this->assertEquals('Google', self::$client->getTitle());
    }

    public function testQuery()
    {
        $this->setName('query "example"');

        $input = $this->navigator->findOne(new ElementIdentifier(
            '.gLFyf.gsfi',
            1
        ));

        $searchButton = $this->navigator->findOne(new ElementIdentifier(
            '.FPdoLc.tfB0Bf input[name=btnK]',
            1
        ));

        $input->sendKeys('example');

        $searchButton->submit();
        self::$crawler = self::$client->getCrawler();
        $this->navigator->setCrawler(self::$crawler);

        $this->assertEquals('example - Google Search', self::$client->getTitle());
    }
}
