import os
import numpy as np
from utils import time_ago
from sqlalchemy import event
from dotenv import load_dotenv
from flask_sqlalchemy import SQLAlchemy
from openaiAPI import gpt3_embedding
from scipy.spatial.distance import cosine

load_dotenv()

db = SQLAlchemy()


class User(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(255))
    messages = db.relationship("Message", backref="user", lazy=True)

    def to_dict(self):
        return {"id": self.id, "name": self.name, "messages": self.messages}


def create_default_users(*args, **kwargs):
    db.session.add(User(name=os.getenv("USER_NAME", "Cooper")))
    db.session.add(User(name=os.getenv("TARS_NAME", "TARS")))
    db.session.commit()


event.listen(User.__table__, "after_create", create_default_users)


class Message(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    content = db.Column(db.String(255))
    speaker_id = db.Column(db.Integer, db.ForeignKey("user.id"))
    embedding = db.Column(db.PickleType, nullable=True, default=list)
    timestamp = db.Column(db.DateTime, default=db.func.current_timestamp())

    def to_dict(self):
        return {
            "id": self.id,
            "content": self.content,
            "speaker_id": self.user.id,
            "speaker_name": self.user.name,
            "timestamp": time_ago(self.timestamp),
            "has_embedding": True if len(self.embedding) == 0 else False,
        }

    def get_embedding(self):
        return self.embedding

    def set_embedding(self, embedding):
        self.embedding = embedding
        db.session.commit()

    def fetch_embedding(self):
        embedding = gpt3_embedding(self.content)
        if embedding:
            self.set_embedding(embedding)
            db.session.commit()


# Save a message to the database.
def save_message(speaker_id, content):
    message = Message(speaker_id=speaker_id, content=content)
    db.session.add(message)
    db.session.commit()
    message.fetch_embedding()
    return message


def get_similar_messages(message):
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
