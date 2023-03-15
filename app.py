from flask import Flask, render_template, send_from_directory
from flask_sqlalchemy import SQLAlchemy
import os

app = Flask(__name__, static_folder='frontend/dist', template_folder='frontend/dist')
app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///tars.db'
db = SQLAlchemy(app)

class Task(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    title = db.Column(db.String(80), nullable=False)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/js/<path:path>')
def send_js(path):
    return send_from_directory('frontend/dist/js', path)

@app.route('/css/<path:path>')
def send_css(path):
    return send_from_directory('frontend/dist/css', path)

@app.route('/fonts/<path:path>')
def send_fonts(path):
    return send_from_directory('frontend/dist/fonts', path)

@app.route('/img/<path:path>')
def send_img(path):
    return send_from_directory('frontend/dist/img', path)

if __name__ == '__main__':
    app.run(debug=True)
