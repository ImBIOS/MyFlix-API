<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index()
    {
        $dummyMovieIds = array("413594", "445030");
        $dummyMovies = array("sao.mp4", "ngnl.mp4");
        $dummyTrailers = array("sao-trailer.mp4", "ngnl-trailer.mp4");
        $pickedKey = array_rand($dummyMovieIds);

        $response = Http::post('https://mtflix-tmdb.vercel.app/api/imamdev', [
            'url' => 'https://api.themoviedb.org/3/movie/' . $dummyMovieIds[$pickedKey] . '?imamdev&append_to_response=credits',
        ])->json()['data'];


        $headerData = [
            'id' => $response['id'],
            'title' => $response['title'],
            'overview' => $response['overview'],
            'release_date' => $response['release_date'],
            'runtime' => $response['runtime'],
            'poster_url' => "https://image.tmdb.org/t/p/original" . $response['poster_path'],
            'film_rate' => $response['adult'] ? "R18+" : "RBO-13",
            'director' => Arr::get($response, 'credits.crew.0.name'),
            'trailer_url' => "http://75.101.213.57/movies/" . $dummyTrailers[$pickedKey],
            "video_url" => "http://75.101.213.57/movies/" . $dummyMovies[$pickedKey],
        ];

        $sectionOneResponse = Http::post('https://mtflix-tmdb.vercel.app/api/imamdev', [
            'url' => 'https://api.themoviedb.org/3/discover/movie?imamdev&with_genres=16',
        ])->json()['data'];
        $sectionTwoResponse = Http::post('https://mtflix-tmdb.vercel.app/api/imamdev', [
            'url' => 'https://api.themoviedb.org/3/discover/movie?imamdev&with_genres=28',
        ])->json()['data'];

        $sectionOne = array_map(function ($item) {
            $dummyMovieIds = array("413594", "445030");
            $dummyMovies = array("sao.mp4", "ngnl.mp4");
            $dummyTrailers = array("sao-trailer.mp4", "ngnl-trailer.mp4");
            $pickedKey = array_rand($dummyMovieIds);

            return [
                'id' => $item['id'],
                'title' => $item['title'],
                'overview' => $item['overview'],
                'release_date' => $item['release_date'],
                'poster_url' => "https://image.tmdb.org/t/p/original" . $item['poster_path'],
                'film_rate' => $item['adult'] ? "R18+" : "RBO-13",
                'director' => Arr::get($item, 'credits.crew.0.name'),
                'trailer_url' => "http://75.101.213.57/movies/" . $dummyTrailers[$pickedKey],
                "video_url" => "http://75.101.213.57/movies/" . $dummyMovies[$pickedKey],
            ];
        }, $sectionOneResponse['results']);

        $sectionTwo = array_map(function ($item) {
            $dummyMovieIds = array("413594", "445030");
            $dummyMovies = array("sao.mp4", "ngnl.mp4");
            $dummyTrailers = array("sao-trailer.mp4", "ngnl-trailer.mp4");
            $pickedKey = array_rand($dummyMovieIds);

            return [
                'id' => $item['id'],
                'title' => $item['title'],
                'overview' => $item['overview'],
                'release_date' => $item['release_date'],
                'poster_url' => "https://image.tmdb.org/t/p/original" . $item['poster_path'],
                'film_rate' => $item['adult'] ? "R18+" : "RBO-13",
                'director' => Arr::get($item, 'credits.crew.0.name'),
                'trailer_url' => "http://75.101.213.57/movies/" . $dummyTrailers[$pickedKey],
                "video_url" => "http://75.101.213.57/movies/" . $dummyMovies[$pickedKey],
            ];
        }, $sectionTwoResponse['results']);


        return ResponseFormatter::success([
            'header' => $headerData,
            'sections' => array([
                'section_name' => "Animation",
                'section_id' => 16,
                'contents' => $sectionOne,
            ], [
                'section_name' => "Action",
                'section_id' => 28,
                'contents' => $sectionTwo,
            ]),
        ], 'Home data fetched successfully');;
    }
}
