# TARS

GPT-3 chatbot Lavavel APp with long-term memory and external sources.

## Installation
1. clone the repo.
2. run `composer install`
3. run `npm install`
4. in `database/seeders/DatabaseSeeder.php` replace the first created User's name with your first name

5. Update your `.env` file to include the following:
```
OPENAI_API_KEY=sk-...g
OPENAI_ORGANIZATION=
CHAT_USER_ID=1
CHAT_TARS_ID=2
```

6. `php artisan migrate:fresh --seed` sets up database and cretates users
7. `npm run build` runs Vite and builds the files
8. `npm run dev` polls the code for updates

## Credits

- Inspired by [Dave Shapiro's work](https://github.com/daveshap/LongtermChatExternalSources).
- Icons purchased from ["Interstellar Icon Pack" ](https://www.iconfinder.com/iconsets/interstellar) on IconFinder
- Tesseract background image by Ákos Halász [[LinkedIn](https://www.linkedin.com/in/akoshalasz/), [artstation](https://www.artstation.com/artwork/EJODA)]
