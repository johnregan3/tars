import pytest
from app.db import User, Message
from app.messages import (
    save_message,
    get_messages_list,
    get_user_messages_list,
    get_tars_reply,
    get_recent_messages,
    stringify_messages,
)


def test_save_message(db):
    speaker_id = 1
    content = "Test message 1"
    message = save_message(speaker_id, content)
    assert message.speaker_id == speaker_id
    assert message.content == content
    db.session.delete(message)
    db.session.commit()


def test_get_messages_list(db):
    speaker_id = 1
    content = "Test message 2"
    message = save_message(speaker_id, content)
    messages = get_messages_list()
    assert len(messages) == 1
    assert messages[0]["content"] == content
    assert messages[0]["speaker_id"] == speaker_id

    db.session.delete(message)
    db.session.commit()


def test_get_user_messages_list(db):
    speaker_id = 1
    content = "Test message 3"
    message = save_message(speaker_id, content)
    messages = get_user_messages_list(speaker_id)
    assert len(messages) == 1
    assert messages[0]["content"] == content
    assert messages[0]["speaker_id"] == speaker_id

    db.session.delete(message)
    db.session.commit()


@pytest.mark.parametrize(
    "data",
    [
        {"user_id": 1, "content": "Hello, TARS!"},
    ],
)
def test_get_tars_reply(db, data):
    tars_reply = get_tars_reply(data)
    assert tars_reply is not None
    assert isinstance(tars_reply, dict)
    db.session.rollback()


def test_get_recent_messages(db):
    user = User.query.filter_by(id=1).first()
    message_contents = ["Message 1", "Message 2", "Message 3", "Message 4"]

    new_messages = []
    for content in message_contents:
        new_messages = save_message(user.id, content)

    recent_messages = get_recent_messages()
    for content in message_contents:
        assert content in recent_messages

    for message in new_messages:
        db.session.delete(message)
        db.session.commit()
    db.session.rollback()


def test_stringify_messages(db):
    user = User.query.filter_by(id=1).first()
    test_contents = ["Message 1", "Message 2", "Message 3", "Message 4"]

    test_messages = []
    for content in test_contents:
        test_messages.append(save_message(user.id, content))

    message_string = stringify_messages(test_messages)
    for message in test_messages:
        message = message.to_dict()
        assert f"{message['speaker_name']}: {message['content']}\n" in message_string

    for message in test_messages:
        db.session.delete(message)
        db.session.commit()
    db.session.rollback()
