# TARS

A GPT-3 chatbot Lavavel App with short and near-term memory.

![tars-github-2](https://user-images.githubusercontent.com/2053940/224233487-3e2e4c17-670e-4cb8-9561-929d1fa7b76e.jpg)

## Overview

This is an attempt to recreate Dave Shapiro's [LongtermChatExternalSources](https://github.com/daveshap/LongtermChatExternalSources) using a database instead of text files to store data.

### Terminology:
- ***Memories*** are individual messages sent either by the user or bot.
- ***Summaries*** are internal notes stored by the bot for later use. These are not currently used in this iteration. (I believe Shapiro planned for periodic runs of a "dream sequence" so that the bot can go back and "think" about what has transpired.)
- A ***Vector*** is an array of of floating point numbers (positive and negative `float`s). In our context, a vector is a way of identifying what's in a "Memory" (a message).
- An ***Embedding** is nearly synonymous with "vector" in our context. Each message, whether sent by the user or bot is sent to OpenAI's API to get an embedding, and that embedding is saved with the rest of the message into a Memory.
- ***Cosine similarity*** is the [mathematical formula](https://en.wikipedia.org/wiki/Cosine_similarity) for comparing two vectors to see how close ("similar") they are.  In our case, when the bot is looking for similar parts of the conversation, it uses the cosine similarity.

## Observations

Using a DB instead of text files means the bot takes up significantly less disk space, and information is processed faster.

My very first iteration used a MySQL database and used PHP to handle the cosine similarity between entries, but it took quite a long time for (~20 sec) for the bot to provide a response to any prompt.  I changed to using a PostgreSQL database so that the cosine similarity can be caclulated in the DB instead of the code, and this reduced response times down to as low as 3 seconds if Open AI's API responded quickly.


## Pre-Setup Notes
- This requires using a PostgreSQL (as opposed to MySQL) database. [[setup](https://www.codementor.io/@engineerapart/getting-started-with-postgresql-on-mac-osx-are8jcopb)].
- I used Homebrew to install postgreSQL: `brew install postgresql`
- Install the pgvector Extension `brew install pgvector/brew/pgvector`
- I installed Redis on my Mac M1 (16GB RAM) to help with caching.

## Installation
1. clone the repo.
2. run `composer install`
3. run `npm install`
4. in `database/seeders/DatabaseSeeder.php` replace the first created User's name with your first name

5. To replicate my config, update your `.env` file to include the following:
```
OPENAI_API_KEY=sk-...g
OPENAI_ORGANIZATION=
CHAT_USER_NAME=John
CHAT_USER_ID=1
CHAT_TARS_NAME=TARS
CHAT_TARS_ID=2

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

6. `php artisan migrate:fresh --seed` sets up database and cretates users based on our config.
7. `npm run build` runs Vite and builds the files
8. `npm run dev` polls the code for updates
9. visit yourLocalWebServer.test/chat/ to start chatting with TARS

## Credits

- Inspired by [Dave Shapiro's work](https://github.com/daveshap/LongtermChatExternalSources).
- Icons purchased from ["Interstellar Icon Pack" ](https://www.iconfinder.com/iconsets/interstellar) on IconFinder

