<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Movie;

class MovieTest extends TestCase
{
    protected $type = 'application/json';
    protected $userToken;
    protected $movie;

    /**
     * Authenticate user.
     *
     */

    protected function authenticate()
    {
        $response = $this->json('POST', '/api/register', [
            'name' => 'test',
            'email' => rand(12345,678910).'test@gmail.com',
            'password' => \Hash::make('secret9874'),
        ], ['Accepts' => $this->type]);

        //Write the response in laravel.log
        \Log::info(1, [$response->getContent()]);

        $response->assertStatus(200);

        $this->assertArrayHasKey('access_token',$response->json());

        // Receive our token
        $this->userToken = $response->json()['access_token'];
    }

    protected function findMovie()
    {
        $this->movie = Movie::latest('created_at')->first();        
    }

    /**
     * test create movie.
     *
     * @return void
     */
    public function test_create_movie()
    {
        $this->authenticate();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->userToken,
            'Accepts' => $this->type
        ])->json('POST','api/movie');

        //Write the response in laravel.log
        \Log::info(1, [$response->getContent()]);

        $response->assertStatus(200);
    }

    /**
     * test get all movies.
     *
     * @return void
     */
    public function test_get_all_movie()
    {        
        $this->authenticate();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->userToken,
            'Accepts' => $this->type
        ])->json('GET','api/movie');

        //Write the response in laravel.log
        \Log::info(1, [$response->getContent()]);

        $response->assertStatus(200);
    }

    /**
     * test find movie.
     *
     * @return void
     */
    public function test_find_movie()
    {        
        $this->authenticate();

        $this->findMovie();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->userToken,
            'Accepts' => $this->type
        ])->json('GET','api/movie/'.$this->movie->id);

        //Write the response in laravel.log
        \Log::info(1, [$response->getContent()]);

        $response->assertStatus(200);
    }

    /**
     * test update movie.
     *
     * @return void
     */
    public function test_update_movie()
    {
        $this->authenticate();

        $this->findMovie();        

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->userToken,
            'Accepts' => $this->type
        ])->json('PUT','api/movie/'.$this->movie->id,[
            'title' => 'Star War Test movie',
            'original_language' => 'urdu',
            'release_date' => "2018-12-07"
        ]);

        //Write the response in laravel.log
        \Log::info(1, [$response->getContent()]);

        $response->assertStatus(200);
    }

    /**
     * test delete movies.
     *
     * @return void
     */
    public function test_delete_movie()
    {        
        $this->authenticate();

        $this->findMovie();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. $this->userToken,
            'Accepts' => $this->type
        ])->json('DELETE','api/movie/'.$this->movie->id);

        //Write the response in laravel.log
        \Log::info(1, [$response->getContent()]);

        $response->assertStatus(200);
    }
}
