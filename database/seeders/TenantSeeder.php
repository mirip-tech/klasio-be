<?php

namespace Database\Seeders;

use App\Models\Membership;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::factory(5)->create();
        $users = User::factory(100)->create();

        $membersPerTenant = ceil($users->count() / $tenants->count());
        $userGroups = $users->chunk($membersPerTenant);

        foreach ($tenants as $index => $tenant) {
            $group = $userGroups[$index] ?? collect();
            if ($group->isEmpty()) continue;

            $roles = collect([
                'owner',
                'admin',
                'teacher',
            ])->pad($group->count(), 'student');

            $payload = $group->values()->map(fn($user, $i) => [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'role' => $roles[$i],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            Membership::insert($payload);

            $this->command->info("Tenant {$tenant->id} ({$tenant->name}) -> {$group->count()} members");
        }
    }
}
