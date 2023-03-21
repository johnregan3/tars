import sys
import os
import tempfile
import pytest
from dotenv import load_dotenv

# Add the parent directory to the path so that the tars module can be imported
sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), "..")))

import tars
from app.db import db as _db
from app.db import User

load_dotenv()


@pytest.fixture(scope="session")
def app(request):
    db_fd, db_path = tempfile.mkstemp()
    config = {
        "SQLALCHEMY_DATABASE_URI": f"sqlite:///{db_path}",
        "SQLALCHEMY_TRACK_MODIFICATIONS": False,
        "TESTING": True,
    }

    app = tars.create_app(config)

    with app.app_context():
        _db.create_all()
        _db.session.commit()

    yield app

    os.close(db_fd)
    os.unlink(db_path)


# Test client for making requests to the Flask app.
@pytest.fixture(scope="session")
def client(app):
    return app.test_client()


# access to DB instance.
@pytest.fixture(scope="function")
def db(app, request):
    with app.app_context():
        _db.session.add(User(name=os.getenv("USER_NAME", "Cooper")))
        _db.session.add(User(name=os.getenv("TARS_NAME", "TARS")))
        _db.session.commit()
        _db.session.begin_nested()

        def teardown():
            _db.session.rollback()

        request.addfinalizer(teardown)

        return _db
