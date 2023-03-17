import os
tars_name = os.getenv("TARS_NAME", "TARS")
user_name = os.getenv("USER_NAME", "Cooper")

# Basic conversational prompt.
def completion_prompt(conversation):
    prompt = """I am a chatbot named {0}. My goal is to assist {1} to reach their goals. I will read the recent messages, and then I will provide a reponse.

The following are the most recent messages in the conversation:
{2}

I will now provide a long, detailed, verbose response, followed by a question:
{3}:"""
    return prompt.format(tars_name, user_name, conversation, tars_name)
