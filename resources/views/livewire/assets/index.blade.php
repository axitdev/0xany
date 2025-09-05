<?php

use App\Models\Asset;
use App\Enums\AssetTypeEnum;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use function Livewire\Volt\{state, computed};

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $typeFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function getAssetsProperty()
    {
        return Asset::query()
            ->when($this->search, fn($query) =>
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('symbol', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
            )
            ->when($this->typeFilter, fn($query) =>
                $query->where('type', $this->typeFilter)
            )
            ->orderBy('name')
            ->paginate(10);
    }

    public function deleteAsset(string $assetId): void
    {
        $asset = Asset::findOrFail($assetId);
        $asset->delete();

        session()->flash('message', 'Asset deleted successfully.');
    }
}; ?>

<section class="w-full">
    <div class="mb-6">
        <flux:heading size="xl" level="1">{{ __('Asset Management') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage your cryptocurrency and financial assets') }}</flux:subheading>
    </div>

    <div class="space-y-6">
        <!-- Header with Create Button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                <!-- Search Input -->
                <div class="flex-1">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search assets..."
                        icon="magnifying-glass"
                    />
                </div>

                <!-- Type Filter -->
                <div class="w-full sm:w-48">
                    <flux:select wire:model.live="typeFilter" placeholder="All Types">
                        <flux:select.option value="">All Types</flux:select.option>
                        @foreach(AssetTypeEnum::cases() as $type)
                            <flux:select.option value="{{ $type->value }}">{{ ucfirst($type->value) }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <flux:button variant="primary" href="/assets/create" wire:navigate icon="plus">
                {{ __('Create Asset') }}
            </flux:button>
        </div>

        <!-- Assets Table -->
        <div class="bg-white dark:bg-zinc-700 shadow overflow-hidden">
            @if($this->assets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-600">
                        <thead class="bg-gray-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Asset
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Symbol
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Decimals
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-600">
                            @foreach($this->assets as $asset)
                                <tr wire:key="asset-{{ $asset->id }}" class="hover:bg-gray-50 dark:hover:bg-zinc-600">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($asset->logo)
                                                @php
                                                    // Handle both uploaded files and URL-based logos
                                                    $logoSrc = str_starts_with($asset->logo, 'http')
                                                        ? $asset->logo
                                                        : asset('storage/' . $asset->logo);
                                                @endphp
                                                <img class="h-8 w-8 rounded-full mr-3 object-cover" src="{{ $logoSrc }}" alt="{{ $asset->name }}">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-zinc-600 mr-3 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                                        {{ substr($asset->name, 0, 2) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $asset->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <flux:badge variant="outline">{{ $asset->symbol }}</flux:badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <flux:badge
                                            variant="{{ $asset->type === AssetTypeEnum::TOKEN ? 'primary' : ($asset->type === AssetTypeEnum::STABLECOIN ? 'success' : 'warning') }}"
                                        >
                                            {{ ucfirst($asset->type->value) }}
                                        </flux:badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $asset->decimals }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate" title="{{ $asset->description }}">
                                            {{ $asset->description }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <flux:button
                                                size="sm"
                                                variant="ghost"
                                                href="/assets/{{ $asset->id }}/edit"
                                                wire:navigate
                                                icon="pencil"
                                            >
                                                Edit
                                            </flux:button>
                                            <flux:button
                                                size="sm"
                                                variant="danger"
                                                wire:click="deleteAsset('{{ $asset->id }}')"
                                                wire:confirm="Are you sure you want to delete this asset?"
                                                icon="trash"
                                            >
                                                Delete
                                            </flux:button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-600">
                    {{ $this->assets->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <flux:icon name="cube" class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                    <flux:heading size="lg" class="mb-2">No assets found</flux:heading>
                    <flux:text class="mb-6">
                        @if($search || $typeFilter)
                            No assets match your current filters.
                        @else
                            Get started by creating your first asset.
                        @endif
                    </flux:text>
                    @if(!$search && !$typeFilter)
                        <flux:button variant="primary" href="/assets/create" wire:navigate icon="plus">
                            Create Asset
                        </flux:button>
                    @endif
                </div>
            @endif
        </div>

        <!-- Flash Messages -->
        @if(session('message'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4">
                <div class="flex">
                    <flux:icon name="check-circle" class="h-5 w-5 text-green-400 mr-3" />
                    <flux:text class="text-green-800 dark:text-green-200">
                        {{ session('message') }}
                    </flux:text>
                </div>
            </div>
        @endif
    </div>
</section>
