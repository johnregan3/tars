import os
import numpy as np
from .db import db, Message, first_or_create_user
from .prompts import completion_prompt
from .openaiAPI import gpt3_completion
from .utils import print_warning
from dotenv import load_dotenv
from scipy.spatial.distance import cosine

load_dotenv()

# Save a message to the database.
def save_message(speaker_id, content) -> Message:
    # check if a User with the given speaker_id exists.
    user = first_or_create_user(speaker_id)

    message = Message(speaker_id=user.id, content=content)
    db.session.add(message)
    db.session.commit()
    message.fetch_embedding()
    return message


def get_messages_list() -> list:
    messages = Message.query.all()
    return [message.to_dict() for message in messages]


def get_user_messages_list(user_id) -> list:
    messages = Message.query.filter_by(speaker_id=user_id).all()
    return [message.to_dict() for message in messages]


def get_tars_reply(data) -> dict:
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
def get_tars_response(user_message) -> str:
    conversation = get_recent_messages()
    related = get_related_messages(user_message)
    prompt = completion_prompt(conversation, related)
    try:
        return gpt3_completion(prompt)
    except Exception as e:
        print_warning(e)
        return ""


# Create one string containing the user name
# and the message content for each message
def get_recent_messages() -> str:
    messages = Message.query.order_by(Message.id.desc()).limit(4).all()
    if len(messages) == 0:
        return False

    ordered_messages = list(reversed(messages))
    output_string = stringify_messages(ordered_messages)
    return output_string


def get_similar_messages(message) -> list:
    # Fetch all messages from the database except the input message
    all_messages = (
        Message.query.filter(Message.id != message.id)
        .order_by(Message.timestamp.desc())
        .all()
    )

    # Convert the input message's embedding to a NumPy array
    input_embedding = np.array(message.get_embedding())

    # Compute cosine similarities between the input message and all other messages
    similarities = []
    threshold = 0.8
    for m in all_messages:
        message_embedding = np.array(m.embedding)
        if message_embedding.shape == (1536,):
            similarity = 1 - cosine(input_embedding, message_embedding)

            if similarity >= threshold:
                similarities.append((m, similarity))

    # Sort the messages by similarity in descending order
    sorted_similarities = sorted(similarities, key=lambda x: x[1], reverse=True)

    # if sorted_similarities is empty, return an empty list
    if len(sorted_similarities) == 0:
        return []

    # Get the top 5 most similar messages
    top_5_similar_messages = sorted_similarities[:5]

    # remove the similarity score from the tuple.
    top_5_message_objects = [
        message_tuple[0] for message_tuple in top_5_similar_messages
    ]

    print(top_5_message_objects)
    return top_5_message_objects


# Create a string containing the user name
# and the message content for each Message.
def stringify_messages(messages) -> str:
    output_string = ""
    for message in messages:
        output_string += stringify_message(message)
    return output_string


# Specific to the Message model.
def stringify_message(message) -> str:
    message = message.to_dict()
    return message["speaker_name"] + ": " + message["content"] + "\n"


def get_related_messages(message):
    messages = get_similar_messages(message)
    if len(messages) == 0:
        return ""

    output_string = stringify_messages(messages)
    return output_string
