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
                $legacyPermissions = [
                    "job_attributes.{$action}",
                    "application_attributes.{$action}",
                ];

                $hasLegacyPermission = false;

                foreach ($legacyPermissions as $legacyPermission) {
                    if (in_array($legacyPermission, $permissions, true)) {
                        $hasLegacyPermission = true;
                        break;
                    }
                }

                if (! $hasLegacyPermission) {
                    continue;
                }

                foreach (['application_statuses', 'application_flags'] as $section) {
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
                    'application_statuses.view',
                    'application_statuses.create',
                    'application_statuses.edit',
                    'application_statuses.delete',
                    'application_flags.view',
                    'application_flags.create',
                    'application_flags.edit',
                    'application_flags.delete',
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
