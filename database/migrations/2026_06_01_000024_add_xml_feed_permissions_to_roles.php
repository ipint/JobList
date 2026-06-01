<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $roles = DB::table('roles')->select(['id', 'permissions'])->get();

        foreach ($roles as $role) {
            $permissions = json_decode((string) $role->permissions, true);

            if (! is_array($permissions)) {
                $permissions = [];
            }

            $hasAdminPermissions = collect(['view', 'create', 'edit', 'delete'])
                ->every(fn (string $action): bool => in_array("users.{$action}", $permissions, true));

            if (! $hasAdminPermissions) {
                continue;
            }

            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                $permission = "xml_feeds.{$action}";

                if (! in_array($permission, $permissions, true)) {
                    $permissions[] = $permission;
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
        $roles = DB::table('roles')->select(['id', 'permissions'])->get();

        foreach ($roles as $role) {
            $permissions = json_decode((string) $role->permissions, true);

            if (! is_array($permissions)) {
                $permissions = [];
            }

            $permissions = array_values(array_filter(
                $permissions,
                fn (string $permission): bool => ! str_starts_with($permission, 'xml_feeds.'),
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
