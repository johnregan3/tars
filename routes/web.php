<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
	return Inertia::render('Home', [
		'canLogin' => Route::has('login'),
		'canRegister' => Route::has('register'),
	]);
})->name('home');

Route::middleware([
	'auth:sanctum',
	config('jetstream.auth_session'),
	'verified',
])->group(function () {
	Route::get('/dashboard', function () {
		return Inertia::render('Dashboard');
	})->name('dashboard');
});

// @todo ensure the user is logged in before they can view the chat.
Route::resource('chat', ChatController::class);

Route::get('/test', function () {

	// Define the prompts as an array
	$prompts = [
		"I want to learn more about artificial intelligence.",
		"Can you tell me more about the history of computer science?",
		"What are the latest developments in natural language processing?",
	];

	// Convert the prompts to JSON
	$json = json_encode([
		'api_key' => env('OPENAI_API_KEY'),
		'prompts' => $prompts,
	]);

	// Send the JSON to the API endpoint
	if ( 'local' === env('APP_ENV', 'production') ) {
		$response = Http::withoutVerifying()->post(route('completions.process'), [
			'json' => $json,
		]);
		echo $response;
	} else {
		$response = Http::post(route('completions.process'), [
			'json' => $json,
		]);
	}

	// Get the output from the API endpoint
	$output = $response->body();

	// Decode the output from JSON
	$results = json_decode($output, true);
	echo $results;
	exit;
})->name('chat');
