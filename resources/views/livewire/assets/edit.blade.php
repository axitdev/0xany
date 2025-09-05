<?php

use App\Models\Asset;
use App\Enums\AssetTypeEnum;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public ?Asset $asset = null;
    public string $name = '';
    public string $symbol = '';
    public string $type = '';
    public int $decimals = 18;
    public $logo;
    public string $currentLogo = '';
    public string $description = '';
    public string $website = '';
    public string $twitter = '';
    public string $discord = '';
    public string $telegram = '';

    public function mount(?Asset $asset = null): void
    {
        if ($asset) {
            $this->asset = $asset;
            $this->name = $asset->name;
            $this->symbol = $asset->symbol;
            $this->type = $asset->type->value;
            $this->decimals = $asset->decimals;
            $this->currentLogo = $asset->logo ?? '';
            $this->description = $asset->description;
            $this->website = $asset->website ?? '';
            $this->twitter = $asset->twitter ?? '';
            $this->discord = $asset->discord ?? '';
            $this->telegram = $asset->telegram ?? '';
        }
    }

    public function update(): void
    {
        if (!$this->asset) {
            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('assets', 'name')->ignore($this->asset->id)],
            'symbol' => ['required', 'string', 'max:10', Rule::unique('assets', 'symbol')->ignore($this->asset->id)],
            'type' => ['required', 'string', Rule::in(array_column(AssetTypeEnum::cases(), 'value'))],
            'decimals' => ['required', 'integer', 'min:0', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'description' => ['required', 'string'],
            'website' => ['nullable', 'string', 'url'],
            'twitter' => ['nullable', 'string', 'url'],
            'discord' => ['nullable', 'string', 'url'],
            'telegram' => ['nullable', 'string', 'url'],
        ]);

        // Handle file upload if new logo is provided
        if ($this->logo) {
            // Delete old logo if it exists
            if ($this->currentLogo && Storage::disk('public')->exists($this->currentLogo)) {
                Storage::disk('public')->delete($this->currentLogo);
            }
            $validated['logo'] = $this->logo->store('logos', 'public');
        } else {
            // Keep the current logo if no new file is uploaded
            $validated['logo'] = $this->currentLogo ?: null;
        }

        $this->asset->update($validated);

        session()->flash('message', 'Asset updated successfully.');

        $this->redirectRoute('assets.index');
    }

    public function cancel(): void
    {
        $this->redirectRoute('assets.index');
    }
}; ?>

<section class="w-full">
    <div class="mb-6">
        <flux:heading size="xl" level="1">{{ __('Edit Asset') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Update asset information') }}</flux:subheading>
    </div>

    <div class="max-w-4xl">
        <form wire:submit="update" class="space-y-6">
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

                    @if ($currentLogo && !$logo)
                        <!-- Current Logo Display -->
                        <div class="mb-4 p-4 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <div class="flex items-center space-x-4">
                                <img src="{{ asset('storage/' . $currentLogo) }}" alt="Current logo" class="w-16 h-16 object-cover rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Current Logo') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Upload a new file to replace') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-center w-full">
                        <label for="logo-upload-edit" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500">
                            @if ($logo)
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-green-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="text-sm text-green-600 dark:text-green-400">
                                        {{ $logo->getClientOriginalName() }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('New file selected') }}
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
                            <input id="logo-upload-edit" type="file" wire:model="logo" class="hidden" accept="image/*" />
                        </label>
                    </div>
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <flux:text size="sm" class="mt-1 text-gray-600 dark:text-gray-400">
                        {{ __('Upload a new image file to replace the current logo (optional)') }}
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
                    {{ __('Update Asset') }}
                </flux:button>
            </div>
        </form>
    </div>
</section>
