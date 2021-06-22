<?php
declare(strict_types=1);

namespace App\Tests\Functional\Users;

use App\Tests\Functional\AuthFixture;
use App\Tests\Functional\DbWebTestCase;

class IndexTest extends DbWebTestCase
{
    /**
     * @test
     */
    public function guest(): void
    {
        $this->client->request('GET', '/users');
        $this->client->request('GET', '/users');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $this->assertSame('http://localhost/login', $this->client->getResponse()->headers->get('Location'));
    }

    /**
     * @test
     */
    public function user(): void
    {
        $this->client->setServerParameters(AuthFixture::userCredentials());
        $this->client->request('GET', '/users/');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function admin(): void
    {
        $this->client->setServerParameters(AuthFixture::adminCredentials());
        $crawler = $this->client->request('GET', '/users/');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Users', [$crawler->filter('title')->text()]);
    }
}