# TARS

GPT-3 chatbot with long term memory, as a Laravel App

| :exclamation:  100% new version coming in v0.2. Will run on Python & SQLite. No laravel, completely new local environment.   |
|-----------------------------------------|

![tars-github-2](https://user-images.githubusercontent.com/2053940/224233487-3e2e4c17-670e-4cb8-9561-929d1fa7b76e.jpg)

## Overview

This is an attempt to recreate [Dave Shapiro](https://www.patreon.com/daveshap)'s [LongtermChatExternalSources](https://github.com/daveshap/LongtermChatExternalSources), replacing storing data in text files with a database.

To his credit, Shaprio's project was an experiment and was never intended to scale. When I tried out his code, I wanted to play with the chatbot longer to see what it was capable of, but was shocked at how quickly the data amassed on my hard drive.  This is what inspired me to try to put all of it into a database.

This app is designed to be run on your local webserver (like [Laravel valet](https://laravel.com/docs/10.x/valet)). Once you have it up and running, start chatting with TARS. It takes a bit of interaction for it to gather enough data to give good responses, but I was able to have some interesting conversations with TARS, covering topics ranging from my personal goals, fried chicken recipes, ceiling fans in cars, and what I enjoy most about the people I love.

### Terminology:
- ***TARS*** is the name for this chatbot.  If you don't know what that is a reference to, here's a [quick video](https://www.youtube.com/watch?v=p3PfKf0ndik).
- ***Memories*** are individual messages sent either by the user or TARS.
- ***Summaries*** are internal notes stored by TARS for later use. These are not currently used in this iteration. (I believe Shapiro planned for periodic runs of a "dream sequence" so that the bot can go back and "think" about what has transpired.)
- A ***Vector*** is an array of of floating point numbers.  In our context, a vector is a way of identifying what's contained in a "Memory".
- An ***Embedding*** is nearly synonymous with "vector" in our context. Each message (whether created by the user or TARS) is sent to OpenAI to get an embedding, and that embedding is saved with the text of the message into a Memory.
- ***Cosine similarity*** is the [mathematical formula](https://en.wikipedia.org/wiki/Cosine_similarity) for comparing two vectors to see how close (or, "similar") they are. In our case, when TARS is looking for similar previous conversations, it uses cosine similarity to locate what content is related.

## How It Works

When the user submits a message to TARS, it is stored in a *Memory* associated with the User. Then, a prompt is prepared for TARS to send to OpenAI's API. This prompt consist of: A brief statement of who TARS is and it's goals, a summary of related Memories, and a chat log of the four most recent messages. When this prompt is returned a completion, it is stored as a Memory associated with TARS, and that reply text is presented to the user.

## Observations

Using a database instead of text files means TARS takes up significantly less disk space, and information is processed faster. Also, DB caching is a huge advantage.

My very first iteration used a MySQL database and plain PHP to calculate cosine similarities, but it took quite a long time for TARS to provide a response to any prompt (~20 sec). I changed to using a PostgreSQL database so that the cosine similarity can be caclulated in the DB query instead of the code, and the cumulative changes reduced response times to as low as 5 seconds if Open AI's API responded quickly.

## Future Development

### Use of a Chat Model over a Completion Model
In the next version I'm going to start using the `gpt-3.5-turbo` *chat* model instead of the current `text-davinci-003` *completion* model.

Turbo is not only 1/10 the cost per API call, but it takes a [series of messages](https://platform.openai.com/docs/guides/chat/chat-vs-completions) all at once to get a more accurate view of what is requested, and additionally, it returns a response with more that just a completion (a simple reply): it provides data like "Anticipation" (guessing what the user needs next) and "Salience" (the important points of the conversation). This data can help TARS provide better results to the user.

### Dream Sequences
Shapiro alluded to letting the chatbot process recent interactions during downtime (like on a Cron). The purpose of this would be to gather a greater understanding of the context it "lives" in, and perhaps not have to sift so hard through data to provide a relevant and accurate response to a prompt. I want to explore this idea more deeply.

### Knowledge Base
I'd like to develop a way for TARS to create and use a knowledge base. This would be a useful reference guide for remembering names of family, important dates, etc. The challenge in this is how TARS will determine when to add information to that table, as well as when to access it.

### External Sources
I'd also like to provide TARS with a way to search the internet for answers and more recent information than is currently available to OpenAI's models. For instance, the ability for TARS to consider the upcoming weather forecast when recommending plans would be really helpful (and frankly, pretty darn cool).

## Installation

*Note: this is what I did to install it on my Mac. I have no idea how to do it on your machine. I spent almost a full day working with ChatGPT to get the PostreSQL up and configured. Best of luck!*

### Pre-Setup Notes
- This requires using a PostgreSQL (as opposed to MySQL) database. [[setup](https://www.codementor.io/@engineerapart/getting-started-with-postgresql-on-mac-osx-are8jcopb)].
- I used Homebrew to install PostgreSQL: `brew install postgresql`
- Install the pgvector PostgreSQL extension: `brew install pgvector/brew/pgvector`
- I installed Redis on my Mac M1 (16GB RAM) to help with caching.

### Setup
1. Clone this repo.

2. Run `composer install`

3. Run `npm install`

4. To replicate my config, update your `.env` file to include the following:
```
OPENAI_API_KEY=sk-...g
OPENAI_ORGANIZATION=
CHAT_USER_NAME=Cooper
CHAT_USER_ID=1
CHAT_TARS_NAME=TARS
CHAT_TARS_ID=2

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

CACHE_DRIVER=redis
```
*Note that "Cooper" can be replaced with your name, and you can call "TARS" whatever the heck you want: HAL, Ava, Shakira, it doesn't matter.*

6. `php artisan migrate:fresh --seed` sets up database and cretates users based on the `.env` file.

7. `npm run build` runs Vite and builds the files

8. `npm run dev` polls the code for updates

## Credits

- Architecture inspired by [Dave Shapiro's](https://www.patreon.com/daveshap) work
- Dave Shapiro's YT video Series:
  - [Tutorial: DIY ChatGPT with Long Term Memories](https://www.youtube.com/watch?v=c3aiCrk0F0U)
  - [DIY ChatGPT: Enhancing RAVEN's long-term memories and starting to work on self reflection](https://www.youtube.com/watch?v=QGLF3UbDf7g)
  - [RAVEN Dream Sequence - Memory Consolidation and Insight Extraction for AGI or cognitive architecture](https://www.youtube.com/watch?v=QGLF3UbDf7g)
- Icons purchased from ["Interstellar Icon Pack" ](https://www.iconfinder.com/iconsets/interstellar) on IconFinder
- Spotlight Theme purchased from [TailwindUI](https://tailwindui.com/templates/spotlight)

