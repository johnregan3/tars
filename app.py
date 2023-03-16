import os
import requests
from dotenv import load_dotenv
from models import db, Message, User
from prompts import completion_prompt
from openaiHelpers import gpt3_completion
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS, cross_origin
from flask import Flask, current_app, render_template, send_from_directory, request, jsonify

load_dotenv()
db_filename = os.getenv('DB_NAME', 'tars') + '.db'

app = Flask(__name__, static_folder='vue/dist/assets', template_folder='vue/dist/')

# Set up CORS.
CORS(app, resources={r"/api": {"origins": "*"}})
app.config['CORS_HEADERS'] = 'Content-Type'

# Set up database.
app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///{}'.format(db_filename)
db.init_app(app)

with app.app_context():
    if not os.path.exists(db_filename):
        db.create_all()
        db.session.commit()

# Routes.
@app.route('/chat')
@app.route('/')
def index():
    return render_template('index.html')

@app.route('/api/messages', methods=['POST', 'GET'])
@cross_origin(origin='*',headers=['content-type'])
def add_message():
    if request.method == 'POST':
        return_messages = []
        data = request.get_json()

        #Save the Cooper message.
        message = Message(speaker_id=data['user_id'], content=data['content'])
        db.session.add(message)
        db.session.commit()

        # Get the TARS reply and save it.
        tars_reply = get_tars_reply()
        if tars_reply:
            tars_reply = Message(speaker_id=2, content=tars_reply)
            db.session.add(tars_reply)
            db.session.commit()
            return_messages.append( Message.query.filter_by(id=tars_reply.id).first().to_dict() )

        return jsonify(return_messages)
    if request.method == 'GET':
        messages = Message.query.all()
        message_list = [message.to_dict() for message in messages]
        return jsonify(message_list)

def get_recent_messages():
    messages = Message.query.order_by(Message.id.desc()).limit(4).all()
    # create one string containing the user name and the message content for each message
    output_string = ""
    if (len(messages) == 0):
        return False

    ordered_messages = list(reversed(messages))
    for message in ordered_messages:
        this_message = message.to_dict()
        print(this_message)
        output_string += this_message['speaker_name'] + ": " + this_message['content'] + "\n"

    return output_string

def get_tars_reply():
    prompt = completion_prompt().replace("<<CONVERSATION>>", get_recent_messages())
    print(prompt)
    try:
        return gpt3_completion(prompt)
    except:
        return False

if __name__ == '__main__':
    app.run(debug=True)
