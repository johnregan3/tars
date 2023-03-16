FROM python:3.8-slim-buster

# Set the working directory
WORKDIR /app

COPY requirements.txt .

RUN pip3 install -r requirements.txt

COPY . .

CMD ["python3", "app.py"]
