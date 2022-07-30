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
        $response = Http::post('https://mtflix-tmdb.vercel.app/api/imamdev', [
            'url' => 'https://api.themoviedb.org/3/movie/445030?imamdev&append_to_response=credits',
        ])->json()['data'];

        $dummyMovies = array("sao.mp4", "ngnl.mp4");
        $dummyTrailers = array("sao-trailer.mp4", "ngnl-trailer.mp4");
        $pickedKey = array_rand($dummyMovies);

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


        return ResponseFormatter::success([
            'header' => $headerData,
            'section' => array([
                'section_name' => "Sedang Trend Sekarang",
                'contents' => array($headerData, $headerData, $headerData),
            ], [
                'section_name' => "Sedang Trend Sekarang",
                'contents' => array($headerData, $headerData, $headerData),
            ]),
        ], 'Home data fetched successfully');;
    }
}
