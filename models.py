

import os
import requests
from sqlalchemy import event
from helpers import time_ago
from dotenv import load_dotenv
from flask_sqlalchemy import SQLAlchemy

db = SQLAlchemy()

class User(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(255))
    messages = db.relationship('Message', backref='user', lazy=True)

    def to_dict(self):
        return {
            'id': self.id,
            'name': self.name,
            'messages': self.messages
        }

def create_default_users(*args, **kwargs):
    db.session.add(User(name=os.getenv('USER_NAME', 'Cooper')))
    db.session.add(User(name=os.getenv('TARS_NAME', 'TARS')))
    db.session.commit()

event.listen(User.__table__, 'after_create', create_default_users)

class Message(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    content = db.Column(db.String(255))
    speaker_id = db.Column(db.Integer, db.ForeignKey('user.id'))
    timestamp = db.Column(db.DateTime, default=db.func.current_timestamp())

    def to_dict(self):
        return {
            'id': self.id,
            'content': self.content,
            'speaker_id': self.user.id,
			'speaker_name': self.user.name,
            'timestamp': time_ago(self.timestamp)
        }