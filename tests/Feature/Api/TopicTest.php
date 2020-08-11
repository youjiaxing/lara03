<?php

namespace Tests\Feature\Api;

use App\Models\Topic;
use App\Models\User;
use Tests\TestCase;

class TopicTest extends TestCase
{
    use ApiAuth;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testStoreTopic()
    {
        $data = [
            'title' => 'test title',
            'body' => 'test content',
            'category_id' => 1,
        ];
        $response = $this->prepare()->postJson('/api/v1/topics', $data);

        $assertData = [
            'data' => [
                'category_id' => $data['category_id'],
                'user_id' => $this->user->id,
                'title' => $data['title'],
                'body' => clean($data['body'], 'user_topic_body'),
            ]
        ];

        $response->assertStatus(201)
            ->assertJson($assertData)// ->assertJsonFragment($assertData)
        ;
    }

    public function testUpdateTopic()
    {
        $topic = $this->makeTopic();

        $editData = ['category_id' => 2, 'body' => 'edit_body', 'title' => 'edit_title'];
        $resp = $this->prepare()->patchJson("/api/v1/topics/{$topic->id}", $editData);

        $assertData = ['data' => $editData];
        $assertData['data']['body'] = $this->cleanBody($assertData['data']['body']);

        $resp->assertStatus(200)
            ->assertJson($assertData);
    }

    protected function makeTopic()
    {
        return factory(Topic::class)->create(['user_id' => $this->user->id, 'category_id' => 1]);
    }

    protected function cleanBody($body)
    {
        return clean($body, 'user_topic_body');
    }

    public function testShowTopic()
    {
        $topic = $this->makeTopic();
        $resp = $this->getJson('/api/v1/topics/' . $topic->id);

        $assertData = ['data' => $topic->only('id', 'title', 'body', 'category_id', 'user_id')];
        $assertData['data']['body'] = $this->cleanBody($assertData['data']['body']);
        $resp->assertStatus(200)
            ->assertJson($assertData);
    }

    public function testIndexTopic()
    {
        $resp = $this->json('GET', '/api/v1/topics');

        $resp->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'links']);
    }

    public function testDestroyTopic()
    {
        $topic = $this->makeTopic();
        $resp = $this->prepare()->json('DELETE', '/api/v1/topics/' . $topic->id);

        $resp->assertStatus(204);

        $resp = $this->json('GET', '/api/v1/topics/' . $topic->id);
        $resp->assertStatus(404);
    }
}
