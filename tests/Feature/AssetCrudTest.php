<?php

use App\Enums\AssetTypeEnum;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

// Authentication Tests
test('guests cannot access asset pages', function () {
    $this->get('/assets')->assertRedirect('/login');
    $this->get('/assets/create')->assertRedirect('/login');
});

test('authenticated users can access asset index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/assets')
        ->assertOk();
});

// Asset Index Tests
test('asset index displays assets correctly', function () {
    $user = User::factory()->create();
    $assets = Asset::factory()->count(3)->create();

    Volt::test('assets.index')
        ->actingAs($user)
        ->assertSee($assets[0]->name)
        ->assertSee($assets[0]->symbol)
        ->assertSee(ucfirst($assets[0]->type->value));
});

test('asset index search functionality works', function () {
    $user = User::factory()->create();
    $bitcoin = Asset::factory()->create(['name' => 'Bitcoin', 'symbol' => 'BTC']);
    $ethereum = Asset::factory()->create(['name' => 'Ethereum', 'symbol' => 'ETH']);

    Volt::test('assets.index')
        ->actingAs($user)
        ->set('search', 'Bitcoin')
        ->assertSee('Bitcoin')
        ->assertDontSee('Ethereum');
});

test('asset index type filter works', function () {
    $user = User::factory()->create();
    $token = Asset::factory()->create(['type' => AssetTypeEnum::TOKEN]);
    $fiat = Asset::factory()->create(['type' => AssetTypeEnum::FIAT]);

    Volt::test('assets.index')
        ->actingAs($user)
        ->set('typeFilter', AssetTypeEnum::TOKEN->value)
        ->assertSee($token->name)
        ->assertDontSee($fiat->name);
});

test('can delete asset from index', function () {
    $user = User::factory()->create();
    $asset = Asset::factory()->create();

    expect(Asset::count())->toBe(1);

    Volt::test('assets.index')
        ->actingAs($user)
        ->call('deleteAsset', $asset->id)
        ->assertHasNoErrors();

    expect(Asset::count())->toBe(0);
});

// Asset Creation Tests
test('authenticated users can access create asset page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/assets/create')
        ->assertOk();
});

test('can create asset with valid data', function () {
    $user = User::factory()->create();

    $assetData = [
        'name' => 'Bitcoin',
        'symbol' => 'BTC',
        'type' => AssetTypeEnum::TOKEN->value,
        'decimals' => 8,
        'description' => 'Digital gold cryptocurrency',
        'website' => 'https://bitcoin.org',
        'twitter' => 'https://twitter.com/bitcoin',
    ];

    expect(Asset::count())->toBe(0);

    Volt::test('assets.create')
        ->actingAs($user)
        ->set('name', $assetData['name'])
        ->set('symbol', $assetData['symbol'])
        ->set('type', $assetData['type'])
        ->set('decimals', $assetData['decimals'])
        ->set('description', $assetData['description'])
        ->set('website', $assetData['website'])
        ->set('twitter', $assetData['twitter'])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect('/assets');

    expect(Asset::count())->toBe(1);

    $asset = Asset::first();
    expect($asset->name)->toBe($assetData['name']);
    expect($asset->symbol)->toBe($assetData['symbol']);
    expect($asset->type)->toBe(AssetTypeEnum::TOKEN);
    expect($asset->logo)->toBeNull(); // Logo should be null when not provided
});

test('asset creation validates required fields', function () {
    $user = User::factory()->create();

    Volt::test('assets.create')
        ->actingAs($user)
        ->set('name', '')
        ->set('symbol', '')
        ->set('description', '')
        ->call('save')
        ->assertHasErrors(['name', 'symbol', 'description']);
});

test('asset creation validates unique name and symbol', function () {
    $user = User::factory()->create();
    $existingAsset = Asset::factory()->create([
        'name' => 'Bitcoin',
        'symbol' => 'BTC',
    ]);

    Volt::test('assets.create')
        ->actingAs($user)
        ->set('name', 'Bitcoin')
        ->set('symbol', 'BTC')
        ->set('type', AssetTypeEnum::TOKEN->value)
        ->set('decimals', 8)
        ->set('description', 'Another Bitcoin')
        ->call('save')
        ->assertHasErrors(['name', 'symbol']);
});

test('asset creation validates URL fields', function () {
    $user = User::factory()->create();

    Volt::test('assets.create')
        ->actingAs($user)
        ->set('name', 'Test Asset')
        ->set('symbol', 'TEST')
        ->set('type', AssetTypeEnum::TOKEN->value)
        ->set('decimals', 18)
        ->set('description', 'Test description')
        ->set('website', 'not-a-url')
        ->call('save')
        ->assertHasErrors(['website']);
});

// Asset Edit Tests
test('authenticated users can access edit asset page', function () {
    $user = User::factory()->create();
    $asset = Asset::factory()->create();

    $this->actingAs($user)
        ->get("/assets/{$asset->id}/edit")
        ->assertOk();
});

test('edit form is pre-populated with asset data', function () {
    $user = User::factory()->create();
    $asset = Asset::factory()->create([
        'name' => 'Bitcoin',
        'symbol' => 'BTC',
        'type' => AssetTypeEnum::TOKEN,
        'decimals' => 8,
    ]);

    Volt::test('assets.edit', ['asset' => $asset])
        ->actingAs($user)
        ->assertSet('name', 'Bitcoin')
        ->assertSet('symbol', 'BTC')
        ->assertSet('type', AssetTypeEnum::TOKEN->value)
        ->assertSet('decimals', 8);
});

test('can update asset with valid data', function () {
    $user = User::factory()->create();
    $asset = Asset::factory()->create(['name' => 'Old Name']);

    Volt::test('assets.edit', ['asset' => $asset])
        ->actingAs($user)
        ->set('name', 'New Name')
        ->call('update')
        ->assertHasNoErrors()
        ->assertRedirect('/assets');

    expect($asset->fresh()->name)->toBe('New Name');
});

test('asset update validates unique name and symbol excluding current asset', function () {
    $user = User::factory()->create();
    $asset1 = Asset::factory()->create(['name' => 'Bitcoin', 'symbol' => 'BTC']);
    $asset2 = Asset::factory()->create(['name' => 'Ethereum', 'symbol' => 'ETH']);

    // Should be able to keep the same name and symbol
    Volt::test('assets.edit', ['asset' => $asset1])
        ->actingAs($user)
        ->set('name', 'Bitcoin')
        ->set('symbol', 'BTC')
        ->call('update')
        ->assertHasNoErrors();

    // Should not be able to use another asset's name or symbol
    Volt::test('assets.edit', ['asset' => $asset1])
        ->actingAs($user)
        ->set('name', 'Ethereum')
        ->set('symbol', 'ETH')
        ->call('update')
        ->assertHasErrors(['name', 'symbol']);
});

// Integration Tests
test('full asset crud workflow', function () {
    $user = User::factory()->create();

    // Test dashboard access
    $this->actingAs($user)
        ->get('/dashboard')
        ->assertSee('Asset Management');

    // Create asset
    $this->get('/assets/create')
        ->assertOk();

    Volt::test('assets.create')
        ->actingAs($user)
        ->set('name', 'Bitcoin')
        ->set('symbol', 'BTC')
        ->set('type', AssetTypeEnum::TOKEN->value)
        ->set('decimals', 8)
        ->set('description', 'Digital gold')
        ->call('save')
        ->assertRedirect('/assets');

    $asset = Asset::first();
    expect($asset)->not()->toBeNull();

    // View asset list
    $this->get('/assets')
        ->assertSee('Bitcoin')
        ->assertSee('BTC');

    // Edit asset
    $this->get("/assets/{$asset->id}/edit")
        ->assertOk();

    Volt::test('assets.edit', ['asset' => $asset])
        ->actingAs($user)
        ->set('name', 'Bitcoin Updated')
        ->call('update')
        ->assertRedirect('/assets');

    expect($asset->fresh()->name)->toBe('Bitcoin Updated');

    // Delete asset
    Volt::test('assets.index')
        ->actingAs($user)
        ->call('deleteAsset', $asset->id);

    expect(Asset::count())->toBe(0);
});
