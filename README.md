# TARS

[![Python Version: 3.10](https://badgen.net/badge/python/3.10/blue/)](https://www.python.org/downloads/release/python-3100/) [![Docker Version: 20.10](https://badgen.net/badge/docker/20.10.14/blue/)](https://www.docker.com/)  [![Code style: black](https://img.shields.io/badge/code%20style-black-000000.svg)](https://github.com/psf/black)

GPT-3 chatbot with long term memory, runs locally or in Docker.

![tars-github-2](https://user-images.githubusercontent.com/2053940/224233487-3e2e4c17-670e-4cb8-9561-929d1fa7b76e.jpg)

## Overview

This is an attempt to recreate [Dave Shapiro](https://www.patreon.com/daveshap)'s [LongtermChatExternalSources](https://github.com/daveshap/LongtermChatExternalSources), replacing storing data in text files with a database.

To his credit, Shapiro's project was an experiment and was never intended to scale. When I tried out his code, I wanted to play with the chatbot longer to see what it was capable of, but was shocked at how quickly the data amassed on my hard drive.  This is what inspired me to try to put all of it into a database.

This app is run locally in your web browser. Once you have it up and running, start chatting with TARS. It takes a bit of interaction for it to gather enough data to give good responses, but I was able to have some interesting conversations with TARS, covering topics ranging from my personal goals, fried chicken recipes, ceiling fans in cars, and what I enjoy most about the people I love.

---
## Super-Quickstart
---

1. `git clone git@github.com:johnregan3/tars.git`
2. Set up configuration in a `.env` file
3. `bash tars-setup.sh` _(only the first time you start TARS)_
3. `bash tars.sh`
4. Visit `http://localhost:4200`
5. Chat with your new best friend

---
## Installation
---
*Note: Unfortunately, I cannot devote much time to providing support for setup issues because I don't want to get fired from my day job*

### Requirements
- Python 3.0+
  - To confirm, in your command shell run `python -V` or even `python3 -V`.
- [Docker](https://docs.docker.com/get-docker/)
- Your [OpenAI API Key](https://help.openai.com/en/articles/4936850-where-do-i-find-my-secret-api-key)

### Setup
1. Download these files. You have two options:
    - Clone this repo: `git clone git@github.com:johnregan3/tars.git`
	- Download the [zip file](https://github.com/johnregan3/tars/archive/refs/heads/trunk.zip) like a caveman.

2. Inside of your tars/ directory (wherever you put it), set up your config file.  Copy or rename `.env.example` to `.env` and update these settings:
```
OPENAI_API_KEY=sk-...g
OPENAI_ORGANIZATION=
USER_NAME=Cooper
TARS_NAME=TARS
DB_NAME=tars
```
You can leave the OpenAI Organization blank if you don't know it.

*Note that "Cooper" can be replaced with your name, and you can call "TARS" whatever the heck you want: HAL, Ava, Shakira, it doesn't matter.*

3. Open up your terminal and run `bash tars-setup.sh` to fire it up and start Docker.  This only has to be done the first time you run TARS.

4. Run `bash tars.sh` to start.

5. Your site will be available at `http://localhost:4200`

6. Press `Ctrl+C` to stop and exit.

🚨 **Important Note:** Your chat database lives inside of your Docker container, so if you destroy — not just stop — the container, your chat history will be wiped out.

---
## Advanced Stuff
---
To do your own develpment or customize the app, here are some further instructions:

1. Run `python -m venv ./venv` to create a virtual environment.

2. Run `source venv/bin/activate` to start the Python environment.
   1. To shut it down, simply run the command `deactivate`

3. Run `pip install -r requirements.txt`

4. For toying with the front end Vue files, sart by changing directories: `cd web`

5. Run `npm install` to get everything ready to go.

6. Run `npm run build` to compile any edits you make in this directory.
    - You can run `npm run dev` to launch a frontend preview if you want to play with the design. It will be viewable at `http://localhost:5173/`. Note that **this is just a preview URL for Vue development**, so the database won't be connected. Python will later give you a different URL for your actual dev site with the database hooked up and whatnot.

7. Run `cd ../` to go back up to the main directory.

8. Run the app. You have two options:
    - Run `python tars.py` to fire up the dev site. It will be at `http://127.0.0.1:5500`
	- Or run `bash tars.sh` to start Docker, then visit `http://localhost:4200` to enjoy the fruits of your labor.

---
## Changelog
---
```
v0.2 (March 2023) Major update. Now runs a Docker container with Python, Flask, SQLite, and Vue.

v0.1 (Early March 2023) Initial Release. Uses PHP/JS with Laravel, Vue and PostgreSQL. Requires local dev server like Laravel Valet.
```
---
## Credits
---

- Architecture inspired by [Dave Shapiro's](https://www.patreon.com/daveshap) work
- Dave Shapiro's YT video Series:
  - [Tutorial: DIY ChatGPT with Long Term Memories](https://www.youtube.com/watch?v=c3aiCrk0F0U)
  - [DIY ChatGPT: Enhancing RAVEN's long-term memories and starting to work on self reflection](https://www.youtube.com/watch?v=QGLF3UbDf7g)
  - [RAVEN Dream Sequence - Memory Consolidation and Insight Extraction for AGI or cognitive architecture](https://www.youtube.com/watch?v=QGLF3UbDf7g)
- Icons purchased from ["Interstellar Icon Pack" ](https://www.iconfinder.com/iconsets/interstellar) on IconFinder
- Spotlight Theme purchased from [TailwindUI](https://tailwindui.com/templates/spotlight)

