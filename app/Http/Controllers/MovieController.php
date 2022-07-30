<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MovieController extends Controller
{
    /**
     * Get movie details
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $movieId = $request->route('movieId');

        $dummyMovieIds = array("413594", "445030");
        $dummyMovies = array("sao.mp4", "ngnl.mp4");
        $dummyTrailers = array("sao-trailer.mp4", "ngnl-trailer.mp4");
        $pickedKey = array_rand($dummyMovieIds);

        $response = Http::post('https://mtflix-tmdb.vercel.app/api/imamdev', [
            'url' => 'https://api.themoviedb.org/3/movie/' . $movieId . '?imamdev&append_to_response=credits',
        ])->json()['data'];


        $data = [
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
            "category" => array_column($response['genres'], 'name'),
            "cast" => array_column($response['credits']['cast'], 'name'),
        ];


        return ResponseFormatter::success($data, 'Home data fetched successfully');
    }

    public function addWatchlist(Request $request)
    {
        $movieId = $request->movie_id;

        // Check if user already added this movie to watchlist
        $watchlist = Watchlist::where('user_id', Auth::id())
            ->where('movie_id', $movieId)
            ->first();

        if ($watchlist) {
            return ResponseFormatter::error(['message' => 'Movie already added to watchlist'], 'Movie already added to watchlist');
        }

        // Save watchlist
        $watchlist = new Watchlist();
        $watchlist->user_id = Auth::id();
        $watchlist->movie_id = $movieId;
        $watchlist->save();

        $dummyMovieIds = array("413594", "445030");
        $dummyMovies = array("sao.mp4", "ngnl.mp4");
        $dummyTrailers = array("sao-trailer.mp4", "ngnl-trailer.mp4");
        $pickedKey = array_rand($dummyMovieIds);

        $response = Http::post('https://mtflix-tmdb.vercel.app/api/imamdev', [
            'url' => 'https://api.themoviedb.org/3/movie/' . $movieId . '?imamdev&append_to_response=credits',
        ])->json()['data'];


        $data = [
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
            "category" => array_column($response['genres'], 'name'),
            "cast" => array_column($response['credits']['cast'], 'name'),
        ];

        return ResponseFormatter::success($data, 'Movie Added to Watchlist');
    }

    public function getWatchlist()
    {
        $watchlist = Watchlist::where('user_id', Auth::id())
            ->get();

        $dummyMovieIds = array("413594", "445030");
        $dummyMovies = array("sao.mp4", "ngnl.mp4");
        $dummyTrailers = array("sao-trailer.mp4", "ngnl-trailer.mp4");
        $pickedKey = array_rand($dummyMovieIds);

        $data = [];
        foreach ($watchlist as $movie) {
            $response = Http::post('https://mtflix-tmdb.vercel.app/api/imamdev', [
                'url' => 'https://api.themoviedb.org/3/movie/' . $movie->movie_id . '?imamdev&append_to_response=credits',
            ])->json()['data'];

            $data[] = [
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
        }

        return ResponseFormatter::success($data, 'Watchlist fetched successfully');
    }
}
