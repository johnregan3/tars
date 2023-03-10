<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memory;
use App\Models\User;
use App\Http\Controllers\MemoryController;
use App\Http\Controllers\OpenAIAPIController as OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ChatController extends Controller
{

	public function index()
	{
		$data = [];
		$data['memories'] = Memory::all()->map(
			function ($memory) {
				$memory['speaker'] = $memory->speaker;
				$memory['date'] = $this->humanReadableDate($memory->created_at, true);
				$memory['message'] = Str::of($memory->content)->markdown();
				return $memory;
			}
		)->toArray();
		return Inertia::render('Chat', ['data' => $data]);
	}

	public function store(Request $request)
	{

		// Preform some validation.
		$request->validate([
			'message' => 'required',
		]);

		if (env('APP_DEBUG', false)) {
			$start_time = microtime(true);
			Log::info('====== START PROMPT/RESPONSE ======');
		}

		// Create the Memory.
		$memory = Memory::create([
			'speaker_id' => env('CHAT_USER_ID', 1),
			'content'    => $request->input('message'),
			'embedding'  => OpenAI::gpt3Embedding($request->input('message')),
		]);

		MemoryController::generateReply($memory);

		if (env('APP_DEBUG', false)) {
			$end_time = microtime(true);
			Log::info('====== chat.store() : ' . round($end_time - $start_time, 2) . ' seconds ======');
		}

		return to_route('chat.index');
	}

	public function humanReadableDate($date, $full = false)
	{
		$now = new \DateTime();
		$ago = new \DateTime($date);
		$diff = $now->diff($ago);
		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;
		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}
		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}
}
