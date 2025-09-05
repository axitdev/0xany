<?php

use App\Models\Asset;
use App\Enums\AssetTypeEnum;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $symbol = '';
    public string $type = '';
    public int $decimals = 18;
    public $logo;
    public string $description = '';
    public string $website = '';
    public string $twitter = '';
    public string $discord = '';
    public string $telegram = '';

    public function mount(): void
    {
        $this->type = AssetTypeEnum::TOKEN->value;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:assets,name'],
            'symbol' => ['required', 'string', 'max:10', 'unique:assets,symbol'],
            'type' => ['required', 'string', Rule::in(array_column(AssetTypeEnum::cases(), 'value'))],
            'decimals' => ['required', 'integer', 'min:0', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'description' => ['required', 'string'],
            'website' => ['nullable', 'string', 'url'],
            'twitter' => ['nullable', 'string', 'url'],
            'discord' => ['nullable', 'string', 'url'],
            'telegram' => ['nullable', 'string', 'url'],
        ]);

        // Handle file upload if logo is provided
        if ($this->logo) {
            $validated['logo'] = $this->logo->store('logos', 'public');
        } else {
            $validated['logo'] = null;
        }

        Asset::create($validated);

        session()->flash('message', 'Asset created successfully.');

        $this->redirectRoute('assets.index');
    }

    public function cancel(): void
    {
        $this->redirectRoute('assets.index');
    }
}; ?>

<section class="w-full">
    <div class="mb-6">
        <flux:heading size="xl" level="1">{{ __('Create Asset') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Add a new cryptocurrency or financial asset') }}</flux:subheading>
    </div>

    <div class="max-w-4xl">
        <form wire:submit="save" class="space-y-6">
            <div class="bg-white dark:bg-zinc-700 rounded-lg shadow p-6">
                <flux:heading size="lg" class="mb-4">{{ __('Basic Information') }}</flux:heading>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Asset Name -->
                    <div>
                        <flux:input
                            wire:model="name"
                            :label="__('Asset Name')"
                            type="text"
                            required
                            placeholder="e.g., Bitcoin, Ethereum"
                            :error="$errors->first('name')"
                        />
                        <flux:text size="sm" class="mt-1 text-gray-600 dark:text-gray-400">
                            The full name of the asset
                        </flux:text>
                    </div>

                    <!-- Symbol -->
                    <div>
                        <flux:input
                            wire:model="symbol"
                            :label="__('Symbol')"
                            type="text"
                            required
                            placeholder="e.g., BTC, ETH"
                            maxlength="10"
                            :error="$errors->first('symbol')"
                        />
                        <flux:text size="sm" class="mt-1 text-gray-600 dark:text-gray-400">
                            Trading symbol or ticker (max 10 characters)
                        </flux:text>
                    </div>

                    <!-- Asset Type -->
                    <div>
                        <flux:select wire:model="type" :label="__('Asset Type')" required :error="$errors->first('type')">
                            @foreach(AssetTypeEnum::cases() as $assetType)
                                <flux:select.option value="{{ $assetType->value }}">{{ ucfirst($assetType->value) }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:text size="sm" class="mt-1 text-gray-600 dark:text-gray-400">
                            The category of this asset
                        </flux:text>
                    </div>

                    <!-- Decimals -->
                    <div>
                        <flux:input
                            wire:model="decimals"
                            :label="__('Decimals')"
                            type="number"
                            required
                            min="0"
                            max="255"
                            :error="$errors->first('decimals')"
                        />
                        <flux:text size="sm" class="mt-1 text-gray-600 dark:text-gray-400">
                            Number of decimal places (typically 18 for tokens)
                        </flux:text>
                    </div>
                </div>

                <!-- Logo Upload -->
                <div class="mt-6">
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Logo') }} <span class="text-gray-500">({{ __('Optional') }})</span>
                        </label>
                    </div>
                    <div class="flex items-center justify-center w-full">
                        <label for="logo-upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500">
                            @if ($logo)
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-green-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="text-sm text-green-600 dark:text-green-400">
                                        {{ $logo->getClientOriginalName() }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('File selected successfully') }}
                                    </p>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                        <span class="font-semibold">{{ __('Click to upload') }}</span> {{ __('or drag and drop') }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('PNG, JPG, GIF up to 2MB') }}
                                    </p>
                                </div>
                            @endif
                            <input id="logo-upload" type="file" wire:model="logo" class="hidden" accept="image/*" />
                        </label>
                    </div>
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <flux:text size="sm" class="mt-1 text-gray-600 dark:text-gray-400">
                        {{ __('Upload an image file for the asset logo (optional)') }}
                    </flux:text>
                </div>

                <!-- Description -->
                <div class="mt-6">
                    <flux:textarea
                        wire:model="description"
                        :label="__('Description')"
                        required
                        rows="4"
                        placeholder="Brief description of the asset and its purpose..."
                        :error="$errors->first('description')"
                    />
                    <flux:text size="sm" class="mt-1 text-gray-600 dark:text-gray-400">
                        Detailed description of the asset
                    </flux:text>
                </div>
            </div>

            <!-- Social Links -->
            <div class="bg-white dark:bg-zinc-700 rounded-lg shadow p-6">
                <flux:heading size="lg" class="mb-4">{{ __('Social Links') }}</flux:heading>
                <flux:text class="mb-4 text-gray-600 dark:text-gray-400">
                    Optional links to official websites and social media accounts
                </flux:text>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Website -->
                    <div>
                        <flux:input
                            wire:model="website"
                            :label="__('Website')"
                            type="url"
                            placeholder="https://example.com"
                            :error="$errors->first('website')"
                        />
                    </div>

                    <!-- Twitter -->
                    <div>
                        <flux:input
                            wire:model="twitter"
                            :label="__('Twitter')"
                            type="url"
                            placeholder="https://twitter.com/username"
                            :error="$errors->first('twitter')"
                        />
                    </div>

                    <!-- Discord -->
                    <div>
                        <flux:input
                            wire:model="discord"
                            :label="__('Discord')"
                            type="url"
                            placeholder="https://discord.gg/invite"
                            :error="$errors->first('discord')"
                        />
                    </div>

                    <!-- Telegram -->
                    <div>
                        <flux:input
                            wire:model="telegram"
                            :label="__('Telegram')"
                            type="url"
                            placeholder="https://t.me/channel"
                            :error="$errors->first('telegram')"
                        />
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:justify-end gap-4">
                <flux:button variant="ghost" wire:click="cancel" type="button">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit" class="w-full sm:w-auto">
                    {{ __('Create Asset') }}
                </flux:button>
            </div>
        </form>
    </div>
</section>
