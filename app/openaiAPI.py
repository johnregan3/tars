import os
import re
import openai
from time import sleep
from dotenv import load_dotenv
from .utils import print_error, print_warning

load_dotenv()

openai.api_key = os.getenv("OPENAI_API_KEY")

# Regexes for code blocks and inline code elements.
pre_re = re.compile(r"```([^`]+)```")
code_re = re.compile(r"`([^`]+)`")
linebreak_re = re.compile(r"(?<!<pre>)\r\n(?!</pre>)")

# This is the "friendly" voice of TARS.
def gpt3_completion(
    prompt,
    engine="text-davinci-003",
    temp=0.0,
    top_p=1.0,
    tokens=400,
    freq_pen=0.0,
    pres_pen=0.0,
) -> str:
    max_retries = 5
    retry = 0
    prompt = prompt.encode(encoding="ASCII", errors="ignore").decode()
    stop = [
        os.getenv("USER_NAME", "Cooper") + ":",
        os.getenv("TARS_NAME", "TARS") + ":",
    ]
    while retry < max_retries:
        try:
            response = openai.Completion.create(
                engine=engine,
                prompt=prompt,
                temperature=temp,
                max_tokens=tokens,
                top_p=top_p,
                frequency_penalty=freq_pen,
                presence_penalty=pres_pen,
                stop=stop,
            )
            text = response["choices"][0]["text"].strip()
            text = re.sub("[\n\n]+", "\n", text)
            text = re.sub("[\r\n]+", "\n", text)
            text = re.sub("[\t ]+", " ", text)
            # TODO: Move these steps only for display.
            text = pre_re.sub(r"<pre><code>\1</code></pre>", text)
            text = code_re.sub(r"<code>\1</code>", text)
            paragraphs = linebreak_re.split(text)
            # TODO: This isn't working correctly.
            text = "".join([f"<p>{p}</p>" for p in paragraphs])
            return text
        except Exception as oops:
            retry += 1
            if retry >= max_retries:
                print_error("GPT Completion Failed: %s" % oops)
                return ""
            print_warning("Error communicating with GPT3: %s Retrying..." % oops)
            sleep(0.5)


# Used for generating embeddings/vectors.
def gpt3_embedding(content):
    # Fix any Unicode Errors
    content = content.encode(encoding="ASCII", errors="ignore").decode()

    max_retries = 5
    retry = 0
    while retry < max_retries:
        try:
            response = openai.Embedding.create(
                input=content, model="text-embedding-ada-002"
            )
            # This is a normal list.
            vector = response["data"][0]["embedding"]
            return vector
        except Exception as oops:
            retry += 1
            if retry >= max_retries:
                print_error("GPT Embedding Failed: %s" % oops)
                return False
            print_warning("Error communicating with OpenAI: %s Retrying..." % oops)
            sleep(0.5)


# Used for more technical processing.
def chatgpt_completion(messages):
    max_retries = 5
    retry = 0
    while retry < max_retries:
        try:
            response = openai.ChatCompletion.create(
                model="gpt-3.5-turbo", messages=messages
            )
            text = response["choices"][0]["message"]["content"]
            return text
        except Exception as oops:
            retry += 1
            if retry >= max_retries:
                print_error("GPT Embedding Failed: %s" % oops)
                return False
            print_warning("Error communicating with Chat: %s Retrying..." % oops)
            sleep(0.5)
