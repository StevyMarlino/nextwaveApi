<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class LoginManagementTest
 * @package Tests\Feature
 */
class AuthControllerTest extends TestCase
{
    use RefreshDatabase;


    public function testLogin()
    {
        /**
         * Create a user
         */
        $user = User::factory()->create($this->dataLogin());

        $payload = ['email' => 'stevymarlino@user.com', 'password' => 'password'];

        /**
         * Now we contact the end point to login
         */
        $response = $this->postJson('api/login', $payload);

        // we test the status after try to connect user we may have a status 200
        $response->assertStatus(200);

        /**
         * now we check if the current user is already authenticated
         */
        $this->assertAuthenticatedAs($user);

        /**
         * no errors in session
         */
        $response->assertSessionHasNoErrors();

        /**
         * we attest that we have this structure in the response Json
         */
        $response->assertJsonStructure([
            "status",
            "message",
            "user",
            "token"
        ]);
    }

    /**
     * @test
     */
    public function invalid_login_credentials()
    {
        /**
         * Create a user
         */
        $user = User::factory()->create($this->dataLogin());

        /**
         * we send lose data
         */
        $payload = ['email' => 'stevymarlinouser.com', 'password' => 'password'];

        /**
         * Now we try to connect the user with the the lose data
         */
        $response = $this->postJson('api/login', $payload);

        /**
         * we test the status after try to connect user we may have a status 422
         */
        $response->assertStatus(422);

    }

    public function testRegister()
    {
        $this->postJson('/api/register', $this->dataRegister())
            ->assertStatus(201)
            ->assertSessionHasNoErrors();

        // We check if we have a user on the database
        $this->assertDatabaseHas(User::class, ['email' => 'stevyjoe@gmail.com']);

    }

    public function testLogout()
    {

        /**
         * Create a user
         */
        $user = User::factory()->create($this->dataLogin());

        $payload = ['email' => 'stevymarlino@user.com', 'password' => 'password'];

        /**
         * Now we contact the end point to login
         */
        $response = $this->postJson('api/login', $payload);

        // Now we logout the user just login
        $logout = $this->withHeader('Authorization', 'Bearer ' . $response['token'])
            ->postJson('api/logout')
        ->assertStatus(200);


        /**
         * no errors in session
         */
        $logout->assertSessionHasNoErrors();

        /**
         * we attest that we have this structure in the response Json
         */
        $logout->assertJsonStructure([
            "status",
            "message",
        ]);

    }

    /**
     * @return array
     */
    private function dataLogin()
    {
        return [
            'email' => 'stevymarlino@user.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ];
    }

    private function dataRegister()
    {
        return $payload = [
            'first_name' => 'stevy',
            'last_name' => 'joe',
            'phone' => '237694480473',
            'email' => 'stevyjoe@gmail.com',
            'image' => '',
            'password' => 'password',
            'password_confirmation' => 'password'

        ];
    }
}
