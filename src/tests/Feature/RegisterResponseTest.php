<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_json_response_after_registration(): void
    {
        $userData = [
            'last_name' => 'Test',
            'first_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->json('POST', '/register', $userData);

        $response->assertStatus(201);

        $responseData = $response->json();

        // 必要なキーと値の検証
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals('Test', $responseData['last_name']);
        $this->assertEquals('User', $responseData['first_name']);

        // 不必要なレスポンスがないかの検証
        $expectedKeys = ['id', 'last_name', 'first_name', 'is_admin', 'is_approved'];
        $actualKeys = array_keys($responseData);
        sort($expectedKeys);
        sort($actualKeys);
        $this->assertEquals($expectedKeys, $actualKeys);
    }

    public function test_redirect_after_registration(): void
    {
        $userData = [
            'last_name' => 'Test',
            'first_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/');
    }
}
