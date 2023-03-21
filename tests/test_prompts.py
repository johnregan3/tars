import os
from dotenv import load_dotenv
from app import prompts

load_dotenv()
tars_name = os.getenv("TARS_NAME", "TARS")
user_name = os.getenv("USER_NAME", "Cooper")


def test_completion_prompt(client):
    related = "Hello, how are you?"
    conversation = "I am good, how are you?"

    test = """I am a chatbot named {0}. My goal is to assist {1} to reach their goals. I will read the related and recent messages, and then I will provide a reponse.

Here are the related messages:
Hello, how are you?

The following are the most recent messages in the conversation:
I am good, how are you?

I will now provide a long, detailed, verbose response, followed by a question:
{2}:"""
    compare = test.format(tars_name, user_name, tars_name)

    output = prompts.completion_prompt(conversation, related)
    assert output == compare
