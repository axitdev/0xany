<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <!-- Welcome Header -->
        <div class="mb-6">
            <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
            <flux:subheading size="lg" class="mb-2">{{ __('Welcome back, :name!', ['name' => auth()->user()->name]) }}</flux:subheading>
            <flux:text class="text-gray-600 dark:text-gray-400">
                {{ __('Manage your cryptocurrency and financial assets from here.') }}
            </flux:text>
        </div>

        <!-- Quick Stats Cards -->
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <!-- Asset Management Card -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg" class="mb-2">{{ __('Asset Management') }}</flux:heading>
                        <flux:text class="text-gray-600 dark:text-gray-400 mb-4">
                            {{ __('Manage your cryptocurrency and financial assets') }}
                        </flux:text>
                        <flux:button variant="primary" href="{{ route('assets.index') }}" wire:navigate icon="cube">
                            {{ __('Manage Assets') }}
                        </flux:button>
                    </div>
                    <flux:icon name="cube" class="h-12 w-12 text-blue-500" />
                </div>
            </div>

            <!-- Quick Create Card -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg" class="mb-2">{{ __('Quick Create') }}</flux:heading>
                        <flux:text class="text-gray-600 dark:text-gray-400 mb-4">
                            {{ __('Add a new asset to your portfolio') }}
                        </flux:text>
                        <flux:button variant="outline" href="{{ route('assets.create') }}" wire:navigate icon="plus">
                            {{ __('Create Asset') }}
                        </flux:button>
                    </div>
                    <flux:icon name="plus-circle" class="h-12 w-12 text-green-500" />
                </div>
            </div>

            <!-- Settings Card -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg" class="mb-2">{{ __('Settings') }}</flux:heading>
                        <flux:text class="text-gray-600 dark:text-gray-400 mb-4">
                            {{ __('Manage your profile and account settings') }}
                        </flux:text>
                        <flux:button variant="ghost" href="{{ route('settings.profile') }}" wire:navigate icon="cog">
                            {{ __('Settings') }}
                        </flux:button>
                    </div>
                    <flux:icon name="cog" class="h-12 w-12 text-gray-500" />
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="relative flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-zinc-700 p-6">
            <flux:heading size="lg" class="mb-4">{{ __('Recent Activity') }}</flux:heading>
            <div class="flex items-center justify-center h-64">
                <div class="text-center">
                    <flux:icon name="chart-bar" class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                    <flux:text class="text-gray-600 dark:text-gray-400">
                        {{ __('Activity feed will appear here once you start managing assets.') }}
                    </flux:text>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
