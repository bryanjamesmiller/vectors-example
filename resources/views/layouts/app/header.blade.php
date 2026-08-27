<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

            <x-app-logo href="{{ route('home') }}" wire:navigate />

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="book-open-text" :href="route('articles.index')" :current="request()->routeIs('articles.*')" wire:navigate>
                    {{ __('Articles') }}
                </flux:navbar.item>
                <flux:navbar.item icon="beaker" :href="route('vector-lab')" :current="request()->routeIs('vector-lab')" wire:navigate>
                    {{ __('Vector Lab') }}
                </flux:navbar.item>
                <flux:navbar.item icon="sparkles" :href="route('rag')" :current="request()->routeIs('rag')" wire:navigate>
                    {{ __('Lumion AI') }}
                </flux:navbar.item>
                <flux:navbar.item icon="credit-card" :href="route('payments')" :current="request()->routeIs('payments*')" wire:navigate>
                    {{ __('Tuition Bill') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                <flux:tooltip :content="__('Repository')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="folder-git-2"
                        href="https://github.com/bryanjamesmiller/vectors-example"
                        target="_blank"
                        :label="__('Repository')"
                    />
                </flux:tooltip>
                <flux:tooltip :content="__('Documentation')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5"
                        icon="book-open-text"
                        href="https://github.com/bryanjamesmiller/vectors-example/blob/main/README.md"
                        target="_blank"
                        :label="__('Documentation')"
                    />
                </flux:tooltip>
            </flux:navbar>

            <x-desktop-user-menu />
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar collapsible="mobile" sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('home') }}" wire:navigate />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')">
                    <flux:sidebar.item icon="book-open-text" :href="route('articles.index')" :current="request()->routeIs('articles.*')" wire:navigate>
                        {{ __('Articles')  }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="beaker" :href="route('vector-lab')" :current="request()->routeIs('vector-lab')" wire:navigate>
                        {{ __('Vector Lab')  }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="sparkles" :href="route('rag')" :current="request()->routeIs('rag')" wire:navigate>
                        {{ __('Lumion AI')  }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="credit-card" :href="route('payments')" :current="request()->routeIs('payments*')" wire:navigate>
                        {{ __('Tuition Bill')  }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/bryanjamesmiller/vectors-example" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="book-open-text" href="https://github.com/bryanjamesmiller/vectors-example/blob/main/README.md" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>
        </flux:sidebar>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
