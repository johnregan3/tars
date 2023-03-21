import os
from .utils import time_ago
from sqlalchemy import event
from dotenv import load_dotenv
from flask_sqlalchemy import SQLAlchemy
from .openaiAPI import gpt3_embedding


load_dotenv()

db = SQLAlchemy()


class User(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(255))
    messages = db.relationship(
        "Message", backref=db.backref("user", lazy=True, cascade="delete")
    )

    def to_dict(self) -> dict:
        return {"id": self.id, "name": self.name, "messages": self.messages}


def create_default_users(*args, **kwargs) -> None:
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

    def to_dict(self) -> dict:
        return {
            "id": self.id,
            "content": self.content,
            "speaker_id": self.user.id,
            "speaker_name": self.user.name,
            "timestamp": time_ago(self.timestamp),
            "has_embedding": True if len(self.embedding) == 0 else False,
        }

    def get_embedding(self) -> list:
        return self.embedding

    def set_embedding(self, embedding) -> None:
        self.embedding = embedding
        db.session.commit()

    def fetch_embedding(self) -> None:
        embedding = gpt3_embedding(self.content)
        if embedding:
            self.set_embedding(embedding)
            db.session.commit()


def first_or_create_user(user_id) -> User:
    if ( user_id > 2 ):
        raise ValueError("User ID is greater than 2")

    user = User.query.filter_by(id=user_id).first()
    if not user:
        # assign the user name based on the provide user id
        user_name = os.getenv("USER_NAME", "Cooper") if user_id == 1 else os.getenv("TARS_NAME", "TARS")
        user = User(id=user_id, name=user_name)
        db.session.add(user)
        db.session.commit()
    return user
