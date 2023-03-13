<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memory;
use App\Helpers\Utils;
use App\Http\Controllers\MemoryController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ChatController extends Controller
{

	/**
	 * Run the Index view.
	 *
	 * @return void
	 */
	public function index()
	{
		$data = [];
		$data['memories'] = Memory::all()->map(
			function ($memory) {
				$memory['speaker'] = $memory->speaker;
				$memory['date'] = Utils::humanReadableDate($memory->created_at, true);
				$memory['message'] = Str::of($memory->content)->markdown();
				return $memory;
			}
		)->toArray();
		return Inertia::render('Chat', ['data' => $data]);
	}

	/**
	 * Store a new Memory.
	 *
	 * @param Request $request The incoming request.
	 *
	 * @return void
	 */
	public function store(Request $request)
	{

		$request->validate([
			'message' => 'required|string|max:500',
		]);

		$input_string = htmlspecialchars($request->input('message'));

		if (env('APP_DEBUG', false)) {
			$start_time = microtime(true);
		}

		// Create the Memory.
		$memory = Memory::create([
			'speaker_id' => env('CHAT_USER_ID', 1),
			'content'    => $input_string,
		]);

		ray( $memory )->green();

		$newMemory = new MemoryController($memory);
		$newMemory->createAIMemory();

		if (env('APP_DEBUG', false)) {
			$end_time = microtime(true);
			Log::info('====== chat.store() : ' . round($end_time - $start_time, 2) . ' seconds ======');
		}

		return to_route('chat.index');
	}
}
