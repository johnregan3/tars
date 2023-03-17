import os
import re
import openai
from time import sleep
from dotenv import load_dotenv

load_dotenv()

openai.api_key = os.getenv('OPENAI_API_KEY')

# Regexes for code blocks and inline code elements.
pre_re = re.compile(r'```([^`]+)```')
code_re = re.compile(r'`([^`]+)`')
linebreak_re = re.compile(r'(?<!<pre>)\r\n(?!</pre>)')

# This is the "friendly" voice of TARS.
# Based on code from github/daveshap.
def gpt3_completion(prompt, engine='text-davinci-003', temp=0.0, top_p=1.0, tokens=400, freq_pen=0.0, pres_pen=0.0):
    max_retry = 5
    retry = 0
    prompt = prompt.encode(encoding='ASCII',errors='ignore').decode()
    stop = [
        os.getenv('USER_NAME', 'Cooper') + ':',
        os.getenv('TARS_NAME', 'TARS') + ':'
    ]
    while retry < max_retry:
        try:
            response = openai.Completion.create(
                engine=engine,
                prompt=prompt,
                temperature=temp,
                max_tokens=tokens,
                top_p=top_p,
                frequency_penalty=freq_pen,
                presence_penalty=pres_pen,
                stop=stop)
            text = response['choices'][0]['text'].strip()
            text = re.sub('[\n\n]+', '\n', text)
            text = re.sub('[\r\n]+', '\n', text)
            text = re.sub('[\t ]+', ' ', text)
            # TODO: Move these steps only for display.
            text = pre_re.sub(r'<pre><code>\1</code></pre>', text)
            text = code_re.sub(r'<code>\1</code>', text)
            paragraphs = linebreak_re.split(text)
            text = ''.join([f'<p>{p}</p>' for p in paragraphs])
            return text
        except Exception as oops:
            retry += 1
            if retry >= max_retry:
                print("\033[91mGPT Completion Failed: %s \033[0m" % oops)
                return False
            print("\033[91mError communicating with OpenAI: %s \033[0m" % oops)
            sleep(1)


# Used for generating embeddings/vectors.
# Lifted almost directly from github/daveshap
def gpt3_embedding(content, engine='text-embedding-ada-002'):
    # Fix any Unicode Errors
    content = content.encode(encoding='ASCII',errors='ignore').decode()
    response = openai.Embedding.create(input=content,engine=engine)
    #This is a normal list.
    #TODO Add try/catch.
    vector = response['data'][0]['embedding']
    return vector

# Used for more technical processing.
# Lifted almost directly from github/daveshap
def chatgpt_completion(messages, model="gpt-3.5-turbo"):
    response = openai.ChatCompletion.create(model=model, messages=messages)
    # TODO: Add try/catch.
    text = response['choices'][0]['message']['content']
    return text
