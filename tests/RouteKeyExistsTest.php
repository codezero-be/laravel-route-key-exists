<?php

namespace CodeZero\RouteKeyExists\Tests;

use CodeZero\RouteKeyExists\RouteKeyExists;
use CodeZero\RouteKeyExists\Tests\Stubs\Model;
use Route;

class RouteKeyExistsTest extends TestCase
{
    /**
     * @var \CodeZero\RouteKeyExists\Tests\Stubs\Model
     */
    protected $model;

    public function setUp()
    {
        parent::setUp();

        $this->model = $this->createModel();
    }

    /** @test */
    public function it_resolves_a_model_with_custom_route_key()
    {
        $this->createRoute(
            RouteKeyExists::model(Model::class)
        );

        $this->validate('foo-9999')
            ->assertSessionHasErrors('key');

        $response = $this->validate("foo-{$this->model->id}")
            ->assertStatus(200);

        $response->assertJsonFragment([
            'key' => "foo-{$this->model->id}",
        ]);
    }

    /** @test */
    public function it_replaces_the_route_key_in_the_request_with_the_original_route_key()
    {
        $this->createRoute(
            RouteKeyExists::model(Model::class)->replace()
        );

        $response = $this->validate("foo-{$this->model->id}")
            ->assertStatus(200);

        $response->assertJsonFragment([
            'key' => $this->model->id,
        ]);
    }

    /** @test */
    public function it_replaces_the_route_key_in_the_request_with_the_original_route_key_and_changes_the_attribute_name()
    {
        $this->createRoute(
            RouteKeyExists::model(Model::class)->replace('changed_key')
        );

        $response = $this->validate("foo-{$this->model->id}")
            ->assertStatus(200);

        $response->assertJsonMissing([
            'key' => "foo-{$this->model->id}",
        ]);

        $response->assertJsonMissing([
            'key' => $this->model->id,
        ]);

        $response->assertJsonFragment([
            'changed_key' => $this->model->id,
        ]);
    }

    /** @test */
    public function it_adds_the_original_route_key_to_the_request()
    {
        $this->createRoute(
            RouteKeyExists::model(Model::class)->add('database_key')
        );

        $response = $this->validate("foo-{$this->model->id}")
            ->assertStatus(200);

        $response->assertJsonFragment([
            'key' => "foo-{$this->model->id}",
            'database_key' => $this->model->id,
        ]);
    }

    /**
     * Send a post request to trigger validation.
     *
     * @param string $key
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function validate($key)
    {
        return $this->post('test', [
            'key' => $key,
        ]);
    }

    /**
     * Create a test route.
     *
     * @param RouteKeyExists $rule
     *
     * @return void
     */
    protected function createRoute($rule)
    {
        Route::post('test', function () use ($rule) {
            request()->validate([
                'key' => $rule
            ]);

            // Return the all request attributes so we can inspect it.
            // request()->validate() only returns the validated attributes.
            return request()->all();
        });
    }

    /**
     * Create a test model.
     *
     * For convenience, we are using the built in User model
     * under the hood, which is totally irrelevant for the tests.
     * We just need a model to work with.
     *
     * @return Model
     */
    protected function createModel()
    {
        return Model::create([
            'name' => 'abc',
            'email' => 'abc@axample.com',
            'password' => 'p4ssw0rd',
        ]);
    }
}
