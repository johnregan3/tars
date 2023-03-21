import os
from dotenv import load_dotenv
from app.db import db
from flask_cors import CORS, cross_origin
from flask import (
    Flask,
    render_template,
    request,
    jsonify,
)

# Initial setup.
def create_app(config=None):
    load_dotenv()
    app = Flask(__name__, static_folder="web/dist/assets", template_folder="web/dist/")

    # Set up CORS.
    CORS(app, resources={r"/api": {"origins": "*"}})
    app.config["CORS_HEADERS"] = "Content-Type"

    # Configure the database.
    db_filename = os.getenv("DB_NAME", "tars") + ".db"
    app.config["SQLALCHEMY_DATABASE_URI"] = "sqlite:///{}".format(db_filename)

    # If a config object is provided, update the app config
    if config:
        app.config.update(config)

    db.init_app(app)

    from app.messages import get_messages_list, get_user_messages_list, get_tars_reply

    # Create the database if it doesn't exist.
    with app.app_context():
        if not os.path.exists(db_filename):
            db.create_all()
            db.session.commit()

    # Routes.
    @app.route("/chat")
    @app.route("/")
    def index():
        messages = get_messages_list()
        props = {"messages": messages}
        return render_template("index.html", props=props)

    @app.route("/api/messages", methods=["POST", "GET"])
    @cross_origin(origin="*", headers=["content-type"])
    def add_message():
        if request.method == "POST":
            # TODO: Don't use a list.
            data = request.get_json()
            reply = get_tars_reply(data)
            print(reply)
            return jsonify([reply])
        if request.method == "GET":
            messages = get_messages_list()
            return jsonify(messages)

    # TODO create Vue router for this.
    @app.route("/api/debug/<int:user_id>", methods=["POST"])
    def get_user_messages(user_id):
        try:
            messages = get_user_messages_list(user_id)
            return jsonify(messages)
        except Exception as e:
            print("\033[91mError getting User messages: %s" % str(e))
            return jsonify([])

    return app


if __name__ == "__main__":

    def is_docker():
        return os.path.exists("/.dockerenv")

    prompt_note_url = "http://localhost:5500"
    if is_docker():
        prompt_note_url = "http://localhost:4200"

    debug_message = """
    \033[94m***********************************************************
    \033[94m* 👉 TARS URL is \033[4m{0}\033[0m
    \033[94m* 👉 Ignore any URLs mentioned below. They\'re for the API.
    \033[94m***********************************************************\033[0m
    """
    print(debug_message.format(prompt_note_url))
    maybe_debug = is_docker() == False
    app = create_app()
    app.run(host="0.0.0.0", port=5500, debug=maybe_debug)
