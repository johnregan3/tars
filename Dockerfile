FROM python:3.8-slim-buster

# Set the working directory
WORKDIR /tars

COPY requirements.txt .

RUN pip3 install -r requirements.txt

COPY . .

CMD ["python3", "tars.py"]
