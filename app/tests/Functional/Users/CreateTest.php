<?php
declare(strict_types=1);

namespace App\Tests\Functional\Users;


use App\Tests\Functional\AuthFixture;
use App\Tests\Functional\DbWebTestCase;

class CreateTest extends DbWebTestCase
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
        $this->client->request('GET', '/users/create');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function admin(): void
    {
        $this->client->setServerParameters(AuthFixture::adminCredentials());
        $crawler = $this->client->request('GET', '/users/create');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Users', [$crawler->filter('title')->text()]);
    }

    /**
     * @test
     */
    public function create(): void
    {
        $this->client->setServerParameters(AuthFixture::adminCredentials());
        $this->client->request('GET', '/users/create');

        $this->client->submitForm('Create', [
            'form[firstName]' => 'Tom',
            'form[lastName]' => 'Bent',
            'form[email]' => 'tom-bent@app.test',
        ]);
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Users', [$crawler->filter('title')->text()]);
    }

    /**
     * @test
     */
    public function notValid(): void
    {
        $this->client->setServerParameters(AuthFixture::adminCredentials());
        $this->client->request('GET', '/users/create');

        $crawler = $this->client->submitForm('Create', [
            'form[firstName]' => '',
            'form[lastName]' => '',
            'form[email]' => 'not-email',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertContains('Значение не должно быть пустым.', [$crawler
            ->filter('#form_firstName')->parents()->first()->filter('.form-error-message')->text()]);

        $this->assertContains('Значение не должно быть пустым.', [$crawler
            ->filter('#form_lastName')->parents()->first()->filter('.form-error-message')->text()]);
        $this->assertContains('Значение адреса электронной почты недопустимо.', [$crawler
            ->filter('#form_email')->parents()->first()->filter('.form-error-message')->text()]);
    }

    /**
     * @test
     */
    public function exists(): void
    {
        $this->client->setServerParameters(AuthFixture::adminCredentials());
        $this->client->request('GET', '/users/create');

        $crawler = $this->client->submitForm('Create', [
            'form[firstName]' => 'Tom',
            'form[lastName]' => 'Bent',
            'form[email]' => 'exesting-user@app.test',
        ]);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('User with this email already exists.', [$crawler->filter('.alert.alert-danger')->text()]);
    }
}