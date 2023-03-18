import os
from dotenv import load_dotenv
from .models import Message, save_message, get_similar_messages
from .prompts import completion_prompt
from .openaiAPI import gpt3_completion

load_dotenv()


def get_messages_list():
    messages = Message.query.all()
    return [message.to_dict() for message in messages]


def get_user_messages_list(user_id):
    messages = Message.query.filter_by(speaker_id=user_id).all()
    return [message.to_dict() for message in messages]


def get_tars_reply(data):

    # Save the Cooper message.
    user_message = save_message(data["user_id"], data["content"])

    # Get the TARS reply and save it.
    tars_reply = get_tars_response(user_message)

    if tars_reply:
        tars_reply = save_message(2, tars_reply)
        tars_reply = Message.query.filter_by(id=tars_reply.id).first().to_dict()

    else:
        tars_reply = {
            "id": "x",
            "content": "I'm sorry, I had an error connecting.",
            "speaker_name": os.getenv("TARS_NAME", "TARS"),
            "speaker_id": 2,
            "timestamp": "Just now",
        }
    return tars_reply


# Get the TARS reply.
def get_tars_response(user_message):
    conversation = get_recent_messages()
    related = get_related_messages(user_message)
    prompt = completion_prompt(conversation, related)
    print("Prompt:")
    print(prompt)
    try:
        return gpt3_completion(prompt)
    except:
        return False


# Create one string containing the user name
# and the message content for each message
def get_recent_messages():
    messages = Message.query.order_by(Message.id.desc()).limit(4).all()
    if len(messages) == 0:
        return False

    ordered_messages = list(reversed(messages))
    output_string = stringify_messages(ordered_messages)
    return output_string


# Create a string containing the user name
# and the message content for each Message.
def stringify_messages(messages):
    output_string = ""
    for message in messages:
        output_string += stringify_message(message)
    return output_string


# Specific to the Message model.
def stringify_message(message):
    message = message.to_dict()
    return message["speaker_name"] + ": " + message["content"] + "\n"


def get_related_messages(message):
    messages = get_similar_messages(message)
    if len(messages) == 0:
        return ""

    output_string = stringify_messages(messages)
    return output_string
