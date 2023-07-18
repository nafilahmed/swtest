<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use App\Http\Resources\MovieResource;
use Validator;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $movies = Movie::where('title', 'like','%'.$request->title.'%')->get();
        return response(['status_code' => 200,'movies' => MovieResource::collection($movies)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $status = 200;

        try{
            $data = [];
            $item = [];

            $responseTMBD = curl_request('https://api.themoviedb.org/3/search/movie?query=starwar&api_key=7097bed6b6d29216211c23d7aebf033d');
            
            $responseSwapi = curl_request('https://swapi.dev/api/films/');

            $response = array_merge($responseTMBD['results'],$responseSwapi['results']);

            foreach ($response as $key => $value) {

                $item['title'] = isset($value['original_title']) ? $value['original_title'] : $value['title'];
                $item['original_language'] = isset($value['original_language']) ? $value['original_language'] : "en";
                $item['release_date'] = $value['release_date'];

                array_push($data, $item);                
            }

            Movie::upsert($data, ['title','release_date'],['title','original_language','release_date']);

            $message = 'Movie stored successfully';


        }catch (\Illuminate\Database\QueryException $qe) {
            $status = 500;
            $message = $qe->getMessage();
        }
        catch(\Exception $ex) {
            $status = 500;            
            $message = $ex->getMessage();
        }
        catch (\Throwable $t) {
            $status = 500;            
            $message = $t->getMessage();
        }

        return response(['status_code' => $status, 'message' => $message]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movie = Movie::find($id);

        return response(['status_code' => 200,'movie' => new MovieResource($movie)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Movie $movie)
    {
        $status = 200;
        try{
            $data = $request->all();

            $validator = Validator::make($data, [
                'title' => 'required|max:255',
                'release_date' => 'required',
            ]);

            if($validator->fails()){
                return response(['error' => $validator->errors(), 'Validation Error']);
            }

            $movie->update($data);

            return response([
                'status_code' => $status,
                'movie' => new MovieResource($movie),
                'message' => 'Movie updated successfully'
            ]);

        }catch (\Illuminate\Database\QueryException $qe) {
            $status = 500;
            $message = $qe->getMessage();
        }
        catch(\Exception $ex) {
            $status = 500;            
            $message = $ex->getMessage();
        }
        catch (\Throwable $t) {
            $status = 500;            
            $message = $t->getMessage();
        }

        return response(['status_code' => $status, 'message' => $message]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function destroy(Movie $movie)
    {
        $movie->delete();

        return response(['status_code' => 200, 'message' => 'Movie deleted successfully']);
    }
}
