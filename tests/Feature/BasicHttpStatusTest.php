<?php

namespace Tests\Feature;

use Symfony\Component\HttpFoundation\Response;
use Tests\CreatesApplication;
use Tests\TestCase;

/**
 * Class BasicHttpStatusTest.
 */
class BasicHttpStatusTest extends TestCase
{
    use CreatesApplication;

    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    const BASE_URI = '/api';
    const ENDPOINT = self::BASE_URI.'/';
    const FACTORY = '';

    const CORRECT_HEADERS = ['Accept' => 'application/vnd.coffective.v1+json'];

    const INCORRECT_HEADERS = [
        'no headers' => [[]],
        'accept application/json' => [['Accept' => 'application/json']],
    ];

    /**
     * TestCase constructor.
     *
     * @param null|string $name
     * @param array       $data
     * @param string      $dataName
     */
    public function __construct(string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->createApplication();
    }

    /**
     * Incorrect entity data, which an application should not save.
     *
     * Data provider
     *
     * @return array
     */
    public function incorrectEntitiesDataPostPut(): array
    {
        $result = [
            'no name' => [$this->correctEntityDataPostPut(['name' => null])],
        ];

        return $result;
    }

    /**
     * Id of a correct and public entity, which can be used in GET and PUT methods.
     *
     * @return string
     */
    protected function correctExistingEntityIdGetPut(): string
    {
        return factory(static::FACTORY)->create()->id;
    }

    /**
     * Correct entity data, which can be used in POST or PUT.
     *
     * @param array $predefinedValues
     *
     * @return array
     */
    protected function correctEntityDataPostPut(array $predefinedValues = []): array
    {
        $correctEntity = factory(static::FACTORY)->make($predefinedValues);

        return $correctEntity->toArray();
    }

    /**
     * Data provider. As opposite to correctExistingEntityId we can delete not published, draft entities.
     *
     * @return array
     */
    public function existingEntityIdsToDelete(): array
    {
        return [
            'correct entity' => [$this->correctExistingEntityIdGetPut()],
        ];
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function incorrectEntityIds(): array
    {
        return ['not existing entity' => ['3f333df6-90a4-4fda-8dd3-9485d27cee36']];
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function incorrectHeaders(): array
    {
        return static::INCORRECT_HEADERS;
    }

    public function testGetIndexSuccess(): void
    {
        $response = $this->getJson(static::ENDPOINT, static::CORRECT_HEADERS);

        $response->assertOk();
    }

    /**
     * @dataProvider incorrectHeaders
     *
     * @param array $incorrectHeaders
     */
    public function testGetIndexBadRequest(array $incorrectHeaders): void
    {
        $response = $this->getJson(static::ENDPOINT, $incorrectHeaders);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testPostStoreSuccess(): void
    {
        $entity = $this->correctEntityDataPostPut();

        $response = $this->postJson(static::ENDPOINT, $entity, static::CORRECT_HEADERS);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /**
     * @dataProvider incorrectEntitiesDataPostPut
     * @expectedException \Illuminate\Validation\ValidationException
     *
     * @param array $entity
     */
    public function testPostStoreUnprocessableEntity(array $entity): void
    {
        $response = $this->postJson(static::ENDPOINT, $entity, static::CORRECT_HEADERS);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetShowSuccess(): void
    {
        $existingEntityId = $this->correctExistingEntityIdGetPut();

        $response = $this->getJson(static::ENDPOINT.'/'.$existingEntityId, static::CORRECT_HEADERS);

        $response->assertOk();
    }

    /**
     * @dataProvider incorrectEntityIds
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @param string $entityId
     */
    public function testGetShowNotFound(string $entityId): void
    {
        $response = $this->getJson(static::ENDPOINT.'/'.$entityId, static::CORRECT_HEADERS);

        $response->assertNotFound();
    }

    public function testPutUpdateSuccess(): void
    {
        $entity = $this->correctEntityDataPostPut();
        $existingCorrectEntityId = $this->correctExistingEntityIdGetPut();

        $response = $this->putJson(static::ENDPOINT.'/'.$existingCorrectEntityId, $entity, static::CORRECT_HEADERS);

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * @dataProvider incorrectEntitiesDataPostPut
     * @expectedException \Illuminate\Validation\ValidationException
     *
     * @param array $entity
     */
    public function testPutUpdateUnprocessableEntity(array $entity): void
    {
        $existingEntityId = $this->correctExistingEntityIdGetPut();

        $response = $this->putJson(static::ENDPOINT.'/'.$existingEntityId, $entity, static::CORRECT_HEADERS);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @dataProvider incorrectEntityIds
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @param string $entityId
     */
    public function testPutUpdateNotFound(string $entityId): void
    {
        $entity = $this->correctEntityDataPostPut();

        $response = $this->putJson(static::ENDPOINT.'/'.$entityId, $entity, static::CORRECT_HEADERS);

        $response->assertNotFound();
    }

    /**
     * @dataProvider existingEntityIdsToDelete
     *
     * @param string $entityId
     */
    public function testDeleteDestroySuccess(string $entityId): void
    {
        $response = $this->deleteJson(static::ENDPOINT.'/'.$entityId, [], static::CORRECT_HEADERS);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     * @dataProvider incorrectEntityIds
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @param string $entityId
     */
    public function testDeleteDestroyNotFound(string $entityId): void
    {
        $responseNotExists = $this->deleteJson(static::ENDPOINT.'/'.$entityId, [], static::CORRECT_HEADERS);

        $responseNotExists->assertNotFound();
    }
}
