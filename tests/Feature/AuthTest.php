<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    protected $type = 'application/json';

    protected $email = 'test@example.com';
    protected $password  = '123456789';

    public function testRegister()
    {
        $response = $this->json('POST', '/api/register', [
            'name'  =>  'Test',
            'email'  =>  $this->email,
            'password'  => $this->password,
        ], ['Accepts' => $this->type]);

        //Write the response in laravel.log
        \Log::info(1, [$response->getContent()]);

        $response->assertStatus(200);

        // Receive our token
        $this->assertArrayHasKey('access_token',$response->json());

    }

    public function testLogin()
    {
        // Simulated landing
        $response = $this->json('POST','/api/login',[
            'email' => $this->email,
            'password' => $this->password,
        ], ['Accepts' => $this->type]);

        //Write the response in laravel.log
        \Log::info(1, [$response->getContent()]);

        // Determine whether the login is successful and receive token 
        $response->assertStatus(200);

        // Delete users
        User::where('email',$this->email)->delete();
    }
}
