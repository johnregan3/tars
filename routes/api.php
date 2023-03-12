<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
	return $request->user();
});

// @todo add auth.
// Define the API endpoint
Route::post('/process-prompts', function (Request $request) {
	// Get the JSON data from the request body
	$content = $request->getContent();
	$json = json_encode( $content );

	// Execute the Python script with the JSON data
	$output = shell_exec("python3 ../storage/app/python-scripts/test.py");
	//$file_path = '..storage/app/python-scripts/process-completions.py';
	//$output = shell_exec('if [ -f '.$file_path.' ]; then echo "File exists"; else echo "File does not exist"; fi');
	//$output = shell_exec("python ../storage/app/python-scripts/process-completions.py");
	$output = shell_exec("python3 ../storage/app/python-scripts/process-completions.py");
	ray( $output )->blue();

	// Return the output from the Python script
	return $output;
})->name('completions.process');
