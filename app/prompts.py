import os
from dotenv import load_dotenv

load_dotenv()
tars_name = os.getenv("TARS_NAME", "TARS")
user_name = os.getenv("USER_NAME", "Cooper")

# Basic conversational prompt.
def completion_prompt(conversation, related) -> str:
    prompt = """I am a chatbot named {0}. My goal is to assist {1} to reach their goals. I will read the related and recent messages, and then I will provide a reponse.

Here are the related messages:
{2}

The following are the most recent messages in the conversation:
{3}

I will now provide a long, detailed, verbose response, followed by a question:
{4}:"""
    return prompt.format(tars_name, user_name, related, conversation, tars_name)
