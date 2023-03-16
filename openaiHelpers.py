import os
import re
import openai
from time import sleep
from dotenv import load_dotenv

load_dotenv()

openai.api_key = os.getenv('OPENAI_API_KEY')

pre_re = re.compile(r'```([^`]+)```')
code_re = re.compile(r'`([^`]+)`')
linebreak_re = re.compile(r'(?<!<pre>)\r\n(?!</pre>)')

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
            text = pre_re.sub(r'<pre><code>\1</code></pre>', text)
            text = code_re.sub(r'<code>\1</code>', text)
            paragraphs = linebreak_re.split(text)
            text = ''.join([f'<p>{p}</p>' for p in paragraphs])
            return text
        except Exception as oops:
            retry += 1
            if retry >= max_retry:
                print("GPT Completion Failed: %s" % oops)
                return False
            print('Error communicating with OpenAI:', oops)
            sleep(1)
