<?php
declare(strict_types=1);

namespace App\Tests\Functional\Api\Auth;

use App\Tests\Functional\DbWebTestCase;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

class SignUpTest extends DbWebTestCase
{
    use ArraySubsetAsserts;
    private const URI = '/api/auth/signup';

    public function testGet(): void
    {
        $this->client->request('GET', self::URI);

        self::assertEquals(405, $this->client->getResponse()->getStatusCode());
    }

    public function testSuccess(): void
    {
        $this->client->request('POST', self::URI, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'test-john@app.test',
            'password' => 'password',
        ]));

        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = json_decode($content, true);

        self::assertEquals([], $data);
    }

    public function testNotValid(): void
    {
        $this->client->request('POST', self::URI, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'first_name' => '',
            'last_name' => '',
            'email' => 'not-email',
            'password' => 'short',
        ]));

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = json_decode($content, true);

        self::assertArraySubset([
            'violations' => [
                ['propertyPath' => 'first_name', 'title' => 'Значение не должно быть пустым.'],
                ['propertyPath' => 'last_name', 'title' => 'Значение не должно быть пустым.'],
                ['propertyPath' => 'email', 'title' => 'Значение адреса электронной почты недопустимо.'],
                ['propertyPath' => 'password', 'title' => 'Значение слишком короткое. Должно быть равно 6 символам или больше.'],
            ],
        ], $data);
    }

    public function testExists(): void
    {
        $this->client->request('POST', self::URI, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'first_name' => 'Tom',
            'last_name' => 'Bent',
            'email' => 'exesting-user@app.test',
            'password' => 'password',
        ]));

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
        self::assertJson($content = $this->client->getResponse()->getContent());

        $data = json_decode($content, true);

        self::assertArraySubset([
            'error' => [
                'message' => 'User already exists.',
            ]
        ], $data);
    }
}