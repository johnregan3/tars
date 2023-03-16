<template>
  <div>
    <div class="pointer-events-auto md:hidden" data-headlessui-state="">
      <button
        class="group flex items-center rounded-full bg-white/90 px-4 py-2 text-sm font-medium text-zinc-800 shadow-lg shadow-zinc-800/5 ring-1 ring-zinc-900/5 backdrop-blur dark:bg-zinc-800/90 dark:text-zinc-200 dark:ring-white/10 dark:hover:ring-white/20"
        type="button"
        aria-expanded="false"
        data-headlessui-state=""
        id="headlessui-popover-button-:R2qb6:"
      >
        Menu
        <svg
          viewBox="0 0 8 6"
          aria-hidden="true"
          class="ml-3 h-auto w-2 stroke-zinc-500 group-hover:stroke-zinc-700 dark:group-hover:stroke-zinc-400"
        >
          <path
            d="M1.75 1.75 4 4.25l2.25-2.5"
            fill="none"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          ></path>
        </svg>
      </button>
    </div>
    <nav class="pointer-events-auto hidden md:block">
      <ul
        class="flex rounded-full bg-white/90 px-3 text-sm font-medium text-zinc-800 shadow-lg shadow-zinc-800/5 ring-1 ring-zinc-900/5 backdrop-blur dark:bg-zinc-800/90 dark:text-zinc-200 dark:ring-white/10"
      >
        <li v-if="true">
          <span
            class="relative block px-3 py-2 transition hover:text-teal-500 dark:hover:text-teal-400"
          >
            You are logged in.
          </span>
        </li>
        <li v-else>
          <a
            href="#"
            class="relative block px-3 py-2 transition hover:text-teal-500 dark:hover:text-teal-400"
          >
            Log in
          </a>
        </li>
      </ul>
    </nav>
  </div>
</template>
