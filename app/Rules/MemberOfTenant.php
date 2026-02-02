<?php

namespace App\Rules;

use App\Enums\Role;
use App\Models\Membership;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

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

        $values = is_array($value) ? $value : [$value];

        $query = Membership::query()
            ->whereIn('user_id', $values)
            ->where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->when(
                $this->role,
                fn ($q) => $q->where('role', $this->role->value)
            );

        $validIds = $query->pluck('user_id')->all();

        $invalid = array_diff($values, $validIds);

        if ($invalid) {
            $fail('Invalid membership for: '.implode(',', $invalid));
        }
    }
}
