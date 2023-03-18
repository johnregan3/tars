import os
from dotenv import load_dotenv
from models import db, Message, save_message, get_similar_messages
from prompts import completion_prompt
from openaiAPI import gpt3_completion
from flask_cors import CORS, cross_origin
from flask import (
    Flask,
    render_template,
    request,
    jsonify,
)

# Initial setup.
load_dotenv()
app = Flask(__name__, static_folder="web/dist/assets", template_folder="web/dist/")

# Set up CORS.
CORS(app, resources={r"/api": {"origins": "*"}})
app.config["CORS_HEADERS"] = "Content-Type"

# Configure the database.
db_filename = os.getenv("DB_NAME", "tars") + ".db"
app.config["SQLALCHEMY_DATABASE_URI"] = "sqlite:///{}".format(db_filename)
db.init_app(app)

# Create the database if it doesn't exist.
with app.app_context():
    if not os.path.exists(db_filename):
        db.create_all()
        db.session.commit()

# Routes.
@app.route("/chat")
@app.route("/")
def index():
    messages = Message.query.all()
    message_list = [message.to_dict() for message in messages]
    props = {"messages": message_list}
    return render_template("index.html", props=props)


@app.route("/api/messages", methods=["POST", "GET"])
@cross_origin(origin="*", headers=["content-type"])
def add_message():
    if request.method == "POST":
        # TODO: Don't use a list.
        data = request.get_json()

        # Save the Cooper message.
        user_message = save_message(data["user_id"], data["content"])

        # Get the TARS reply and save it.
        tars_reply = get_tars_reply(user_message)

        if tars_reply:
            tars_reply = save_message(2, tars_reply)

            # Debugging.
            get_related_messages(Message.query.filter_by(id=tars_reply.id).first())

            tars_reply = Message.query.filter_by(id=tars_reply.id).first().to_dict()

        else:
            tars_reply = {
                "id": "x",
                "content": "I'm sorry, I had an error connecting.",
                "speaker_name": os.getenv("TARS_NAME", "TARS"),
                "speaker_id": 2,
                "timestamp": "Just now",
            }

        return jsonify([tars_reply])
    if request.method == "GET":
        messages = Message.query.all()
        message_list = [message.to_dict() for message in messages]
        return jsonify(message_list)


# TODO create Vue router for this.
@app.route("/api/debug/<int:user_id>", methods=["POST"])
def get_user_messages(user_id):
    try:
        messages = (
            Message.query.filter_by(speaker_id=user_id)
            .order_by(Message.timestamp.desc())
            .all()
        )
        message_list = [message.to_dict() for message in messages]
        return jsonify(message_list)
    except Exception as e:
        print("Error getting User messages: %s" % str(e))
        return jsonify([])


# Get the TARS reply.
def get_tars_reply(user_message):
    conversation = get_recent_messages()
    related = get_related_messages(user_message)
    prompt = completion_prompt(conversation, related)
    print("Prompt:")
    print(prompt)
    try:
        return gpt3_completion(prompt)
    except:
        return False


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


# Create one string containing the user name
# and the message content for each message
def get_recent_messages():
    messages = Message.query.order_by(Message.id.desc()).limit(4).all()
    if len(messages) == 0:
        return False

    ordered_messages = list(reversed(messages))
    output_string = stringify_messages(ordered_messages)
    return output_string


if __name__ == "__main__":

    def is_docker():
        return os.path.exists("/.dockerenv")

    prompt_note_url = "http://localhost:5500"
    if is_docker():
        prompt_note_url = "http://localhost:4200"

    debug_message = """
    \033[94m***********************************************************
    \033[94m* 👉 TARS URL is \033[4m{0}\033
    \033[94m* 👉 Ignore any URLs mentioned below. They\'re for the API.
    \033[94m***********************************************************\033[0m
    """
    print(debug_message.format(prompt_note_url))
    maybe_debug = is_docker()
    app.run(host="0.0.0.0", port=5500, debug=maybe_debug)
