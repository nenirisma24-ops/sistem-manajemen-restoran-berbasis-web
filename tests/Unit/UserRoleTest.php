<?php

use App\Models\User;

describe('user roles', function () {
    it('defaults new users to the customer role', function () {
        $user = User::factory()->create();

        expect($user->role)->toBe('customer');
    });

    it('can create a user with a specific role', function () {
        $user = User::factory()->create([
            'role' => 'kasir',
        ]);

        expect($user->role)->toBe('kasir');
    });
});
