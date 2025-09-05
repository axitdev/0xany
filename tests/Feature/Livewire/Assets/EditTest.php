<?php

use Livewire\Volt\Volt;

it('can render', function () {
    $asset = \App\Models\Asset::factory()->create();

    $component = Volt::test('assets.edit', ['asset' => $asset]);

    $component->assertSee('Edit Asset');
});
