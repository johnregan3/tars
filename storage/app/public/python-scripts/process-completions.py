


# Use LANGCHAIN, DUMMY.





import json
import openai
import concurrent.futures

def process_prompt(prompt):
    response = openai.Completion.create(
        model="text-davinci-003",
        max_tokens=1000,
        frequency_penalty=0.5,
        presence_penalty=0.75,
        prompt=prompt,
    )
    return response.choices[0].text.strip()

def process_prompts(prompts):
    results = []
    with concurrent.futures.ThreadPoolExecutor() as executor:
        futures = [executor.submit(process_prompt, prompt) for prompt in prompts]
        for future in concurrent.futures.as_completed(futures):
            result = future.result()
            results.append(result)
    return results

if __name__ == '__main__':
    # Read the JSON input from stdin
    input_json = input()

    # Parse the JSON input into a Python object
    input_data = json.loads(input_json)

    # Set the OpenAI API key
    openai.api_key = input_data['api_key']

    # Extract the list of prompts from the input data
    prompts = input_data['prompts']

    # Process the prompts
    results = process_prompts(prompts)

    # Print the results as JSON
    output_data = {'results': results}
    print(json.dumps(output_data))
