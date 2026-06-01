<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roles = DB::table('roles')
            ->select(['id', 'permissions'])
            ->get();

        foreach ($roles as $role) {
            $permissions = json_decode((string) $role->permissions, true);

            if (! is_array($permissions)) {
                $permissions = [];
            }

            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                if (! in_array("job_attributes.{$action}", $permissions, true)) {
                    continue;
                }

                foreach (['departments', 'employment_types', 'work_modes', 'experience_levels'] as $section) {
                    $newPermission = "{$section}.{$action}";

                    if (! in_array($newPermission, $permissions, true)) {
                        $permissions[] = $newPermission;
                    }
                }
            }

            DB::table('roles')
                ->where('id', $role->id)
                ->update([
                    'permissions' => json_encode(array_values(array_unique($permissions))),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        $roles = DB::table('roles')
            ->select(['id', 'permissions'])
            ->get();

        foreach ($roles as $role) {
            $permissions = json_decode((string) $role->permissions, true);

            if (! is_array($permissions)) {
                $permissions = [];
            }

            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                $legacyPermission = "job_attributes.{$action}";

                if (! in_array($legacyPermission, $permissions, true)) {
                    $permissions[] = $legacyPermission;
                }
            }

            $permissions = array_values(array_filter(
                $permissions,
                fn (string $permission): bool => ! in_array($permission, [
                    'departments.view',
                    'departments.create',
                    'departments.edit',
                    'departments.delete',
                    'employment_types.view',
                    'employment_types.create',
                    'employment_types.edit',
                    'employment_types.delete',
                    'work_modes.view',
                    'work_modes.create',
                    'work_modes.edit',
                    'work_modes.delete',
                    'experience_levels.view',
                    'experience_levels.create',
                    'experience_levels.edit',
                    'experience_levels.delete',
                ], true),
            ));

            DB::table('roles')
                ->where('id', $role->id)
                ->update([
                    'permissions' => json_encode($permissions),
                    'updated_at' => now(),
                ]);
        }
    }
};
