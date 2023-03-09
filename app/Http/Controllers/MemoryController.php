<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memory;
use App\Models\Summary;
use App\Http\Controllers\OpenAIAPIController as OpenAI;
use Illuminate\Support\Facades\Log;

class MemoryController extends Controller
{

	/**
	 * Create a summary and compose the reply to the user.
	 *
	 * @param array $current_vector Vector of the current message.
	 *
	 * @return string
	 */
	public static function generate_reply( $current_vector = []) {
		$conversation = self::get_conversation();
		$related      = self::get_related_memories($current_vector, $conversation);
		$notes        = self::create_summary($related);
		$recent       = self::get_last_messages($conversation, 4);
		return self::write_new_memory($notes, $recent);
	}

	/**
	 * Get a bunch of memories from the database, starting with the oldest.
	 *
	 * @param int $limit Limit of memories to return.
	 *
	 * @return array Array of Memories.
	 */
	public static function get_conversation( $limit = 10000) {
		return Memory::orderBy('created_at', 'asc')->take($limit)->get()->toArray();
	}

	/**
	 * Create an array of memories similar to the current message.
	 *
	 * @param string $vector Vector of the current message.
	 * @param array $nodes Memories (in conversation)
	 * @param int $limit Limit of memories to return.
	 *
	 * @return array Array of similar memories.
	 */
	public static function get_related_memories( $vector, $memories, $limit = 10) {
		$scores = array();
		foreach ($memories as $memory) {
			if ($vector == $memory['vector']) {
				// skip this one because it is the same message.
				continue;
			}
			$score = self::calc_similarity(json_decode($memory['vector']), json_decode($vector));
			$memory['score'] = $score;
			$scores[] = $memory;
		}
		usort($scores, function($a, $b) {
			if ($a['score'] == $b['score']) {
				return 0;
			} else if ($a['score'] < $b['score']) {
				return 1;
			} else {
				return -1;
			}
		});
		return array_slice($scores, 0, $limit);
	}

	static function get_last_messages($conversation, $limit = 4) {
		$memories = array_slice( $conversation, -1 * $limit );
		$output = '';
		foreach ($memories as $memory) {
			$output .= $memory['message'] . '\n\n';
		}
		return trim($output);
	}

	/**
	 * Calculate similarity between two vectors.
	 *
	 * @param array $vector1
	 * @param array $vector2
	 * @return float
	 */
	public static function calc_similarity($vector1, $vector2) {
		return self::dot_product($vector1, $vector2) / (self::norm($vector1) * self::norm($vector2));
	}

	/**
	 * Summarize a block of memories into one payload.
	 *
	 * @param array $memories Array of Memories.
	 *
	 * @return string
	 */
	public static function create_summary($memories) {
		$start_time = microtime(true);
		usort($memories, function($a, $b) {
			if ($a['created_at'] == $b['created_at']) {
				return 0;
			} else if ($a['created_at'] < $b['created_at']) {
				return -1;
			} else {
				return 1;
			}
		});

		$input = '';
		$identifiers = [];
		$timestamps = [];
		foreach ($memories as $memory) {
			$input .= $memory['message'] . "\n\n";
			$identifiers[] = $memory['id'];
			$timestamps[]  = $memory['created_at'];
		}

		$input = trim($input);
		$prompt = str_replace('<<INPUT>>', $input, self::summary_template());

		// Create the Summary.
		$summary = Summary::create([
			'text'       => OpenAI::gpt3_completion($prompt),
			'vector'     => OpenAI::gpt3_embedding($input),
			'timestamps' => json_encode($timestamps),
		]);

		array_map( function( $memory_id ) use ($summary) {
			$summary->memories()->attach($memory_id);
		}, $identifiers);

		$summary->save();
		$end_time = microtime(true);

		if ( env('APP_DEBUG', false ) ) {
			Log::info('Save Summary Text: ' . ($end_time - $start_time) . ' seconds ***' . $summary->text);
		}
		return $summary->text;
	}

	/**
	 * Create a New Memory from Tars' POV.
	 * This is the response to the user's message.
	 *
	 * @param string $notes
	 * @param array $array of recent Memory objects.
	 */
	public static function write_new_memory($notes, $recent) {
		$text  = str_replace( '<<NOTES>>', $notes, self::prompt_template() );
		$text  = str_replace( '<<CONVERSATION>>', $recent, $text );
		$memory = Memory::create([
			'speaker_id' => env('CHAT_TARS_ID', 2),
			'message' => OpenAI::gpt3_completion($text),
			'vector' => OpenAI::gpt3_embedding($notes),
		]);
		return $memory->message;
	}


	/**
	 * Calculate dot product of two vectors.
	 *
	 * @param array $vector1
	 * @param array $vector2
	 * @return float
	 */
	public static function dot_product($vector1, $vector2) {
		$sum = 0;
		foreach( $vector1 as $i => $value ) {
			$sum += $value * $vector2[$i];
		}
		return $sum;
	}

	/**
	 * Calculate norm of a vector.
	 *
	 * @param array $vector
	 * @return float
	 */
	public static function norm($vector) {
		$sum = 0;
		foreach( $vector as $value ) {
			$sum += $value * $value;
		}
		return sqrt($sum);
	}

	public static function summary_template() {
		ob_start();
		?>
		Write detailed notes of the following in a hyphenated list format like "- "

		<<INPUT>>

		Notes:
		<?php
		return ob_get_clean();
	}

	public static function prompt_template() {
		ob_start();
		?>

		I am a chatbot named TARS. My goals are to reduce suffering, increase prosperity, and increase understanding. I will read the conversation notes and recent messages, and then I will provide a long, verbose, detailed answer. I will then end my response with a follow-up or leading question.


		The following are notes from earlier conversations with <?php echo env( 'CHAT_USER_NAME', 'USER' ); ?>:
		<<NOTES>>

		The following are the most recent messages in the conversation:
		<<CONVERSATION>>

		I will now provide a long, detailed, verbose response, followed by a question:
		TARS:

		<?php
		return ob_get_clean();
	}

}
