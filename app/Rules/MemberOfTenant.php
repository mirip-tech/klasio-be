<?php

namespace App\Rules;

use App\Enums\Role;
use App\Models\Membership;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MemberOfTenant implements ValidationRule
{
    public function __construct(protected ?Role $role = null) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var Tenant|null $tenant */
        $tenant = app('tenant');

        if (! $tenant) {
            $fail('Missing tenant context.');
            return;
        }

        $query = Membership::query()
            ->where('user_id', $value)
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->when($this->role, fn($q) => $q->where('role', $this->role->value));

        if (! $query->exists()) {
            $fail("The selected {$attribute} is not a valid member for this tenant.");
        }
    }
}
