<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class ApiResource extends TestCase
{

    use RefreshDatabase;

    protected $baseUrl;  // Define the base URL for the resource (e.g., '/api/users')
    protected $model;    // Define the model class (e.g., User::class)
    protected $table;
    protected $updateRequest;
    protected $storeRequest;

    /**
     * A basic feature test example.
     */
    public function testIndex(): void
    {

        $count  = $this->model::count();
        $models = $this->model::factory(3)->create();
        $table  = app($this->model)->getTable();

        $this->getJson("{$this->baseUrl}")
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                    ]
                ]
            ])->assertJsonMissing([
                'deleted_at' => $models[0]->deleted_at
            ]);

        $this->assertDatabaseCount($table, 3 + $count);

    }

    public function testStore()
    {

        $data  = $this->model::factory()->make()->toArray();

        $this->postJson("{$this->baseUrl}", $data)
            ->assertStatus(201)
            ->assertJson([
                'data' => $data
            ]);

    }

    public function testValidationOnStore()
    {

        $rules     = array_keys($this->storeRules);

        $blankData = $this->model::factory()->blank()->make()->toArray();

        foreach ($rules as $rule) {
            $this->postJson("{$this->baseUrl}", $blankData)
                ->assertStatus(422)
                ->assertInvalid($rule);
        }

    }

    public function testUpdate()
    {

        $model = $this->model::factory()->create();
        $data  = $this->model::factory()->make()->toArray();

        $this->putJson("{$this->baseUrl}/{$model->id}", $data)
            ->assertOk()
            ->assertJson([
                'data' => $data
            ]);

    }

    public function testValidationOnUpdate()
    {

        $rules     = array_keys($this->storeRules);
        $model     = $this->model::factory()->create();
        $blankData = $this->model::factory()->blank()->make(['id' => $model->id])->toArray();

        foreach ($rules as $rule) {
            $this->putJson("{$this->baseUrl}/{$blankData['id']}", $blankData)
                ->assertStatus(422)
                ->assertInvalid($rule);
        }

    }

    public function testDestroy()
    {

        $model = $this->model::factory()->create();

        $this->deleteJson("{$this->baseUrl}/{$model->id}")
            ->assertStatus(204);

        $this->assertSoftDeleted($model);
        $this->getJson("{$this->baseUrl}/{$model->id}")->assertNotFound();

    }

    public function testShow(): void
    {

        $model = $this->model::factory()->create();

        $this->getJson("{$this->baseUrl}/{$model->id}")
            ->assertOk()
            ->assertJson([
                'data' => $model->toArray()
            ]);

    }

}
