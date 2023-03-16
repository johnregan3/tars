# TARS

GPT-3 chatbot with long term memory, as a Laravel App

![tars-github-2](https://user-images.githubusercontent.com/2053940/224233487-3e2e4c17-670e-4cb8-9561-929d1fa7b76e.jpg)

## Overview

This is an attempt to recreate [Dave Shapiro](https://www.patreon.com/daveshap)'s [LongtermChatExternalSources](https://github.com/daveshap/LongtermChatExternalSources), replacing storing data in text files with a database.

To his credit, Shaprio's project was an experiment and was never intended to scale. When I tried out his code, I wanted to play with the chatbot longer to see what it was capable of, but was shocked at how quickly the data amassed on my hard drive.  This is what inspired me to try to put all of it into a database.

This app is designed to be run on your local webserver (like [Laravel valet](https://laravel.com/docs/10.x/valet)). Once you have it up and running, start chatting with TARS. It takes a bit of interaction for it to gather enough data to give good responses, but I was able to have some interesting conversations with TARS, covering topics ranging from my personal goals, fried chicken recipes, ceiling fans in cars, and what I enjoy most about the people I love.

## Installation

*Note: this is what I did to install it on my Mac. I have no idea how to do it on your machine. I spent almost a full day working with ChatGPT to get the PostreSQL up and configured. Best of luck!*

### Requirements
- Docker Desktop (or some way to run docker locally)
- Vue CLI `npm install -g @vue/cli` (Make sure you configure for Vue v3)


### Setup
1. Clone this repo.

4. Set up your own config file.  Copy `.env.example` to `.env` and update these settings:
```
OPENAI_API_KEY=sk-...g
OPENAI_ORGANIZATION=
USER_NAME=Cooper
TARS_NAME=TARS
DB_NAME=tars
```
*Note that "Cooper" can be replaced with your name, and you can call "TARS" whatever the heck you want: HAL, Ava, Shakira, it doesn't matter.*

5. Run `python -m venv ./venv` to create a virtual environment.

6. Run `source venv/build/activate` to start the Python environment.
   1. To shut it down, simply run the command `deactivate`

7. Set up the frontend files. Start by changing directories: `cd vue`

8. Run `npm install`

9. Run `npm run build`
    - You can also run `npm run dev` to launch a frontend preview if you want to make design changes. It will be viewable at `http://localhost:5173/`. This is just a preview for Vue development.
	- Note that Python will later give you a different URL for your actual site.

10. Run `cd ../` to go back up to the main directory.

11.


## Credits

- Architecture inspired by [Dave Shapiro's](https://www.patreon.com/daveshap) work
- Dave Shapiro's YT video Series:
  - [Tutorial: DIY ChatGPT with Long Term Memories](https://www.youtube.com/watch?v=c3aiCrk0F0U)
  - [DIY ChatGPT: Enhancing RAVEN's long-term memories and starting to work on self reflection](https://www.youtube.com/watch?v=QGLF3UbDf7g)
  - [RAVEN Dream Sequence - Memory Consolidation and Insight Extraction for AGI or cognitive architecture](https://www.youtube.com/watch?v=QGLF3UbDf7g)
- Icons purchased from ["Interstellar Icon Pack" ](https://www.iconfinder.com/iconsets/interstellar) on IconFinder
- Spotlight Theme purchased from [TailwindUI](https://tailwindui.com/templates/spotlight)

