FROM python:3.10-slim-buster
FROM node:18-buster-slim
COPY . .
RUN pip install -r requirements.txt
CMD ["python3", "app.py"]
