<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"Administrador","guard_name":"web","permissions":["view_category","view_any_category","create_category","update_category","restore_category","restore_any_category","replicate_category","reorder_category","delete_category","delete_any_category","force_delete_category","force_delete_any_category","view_order","view_any_order","create_order","update_order","restore_order","restore_any_order","replicate_order","reorder_order","delete_order","delete_any_order","force_delete_order","force_delete_any_order","view_order::item","view_any_order::item","create_order::item","update_order::item","restore_order::item","restore_any_order::item","replicate_order::item","reorder_order::item","delete_order::item","delete_any_order::item","force_delete_order::item","force_delete_any_order::item","view_product","view_any_product","create_product","update_product","restore_product","restore_any_product","replicate_product","reorder_product","delete_product","delete_any_product","force_delete_product","force_delete_any_product","view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_stock","view_any_stock","create_stock","update_stock","restore_stock","restore_any_stock","replicate_stock","reorder_stock","delete_stock","delete_any_stock","force_delete_stock","force_delete_any_stock","view_tag","view_any_tag","create_tag","update_tag","restore_tag","restore_any_tag","replicate_tag","reorder_tag","delete_tag","delete_any_tag","force_delete_tag","force_delete_any_tag","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user"]},{"name":"Comprador","guard_name":"web","permissions":["view_category","view_any_category","view_order","view_any_order","view_product","view_any_product","view_stock","view_any_stock","view_tag","view_any_tag"]},{"name":"Gestor","guard_name":"web","permissions":["view_category","view_any_category","create_category","update_category","restore_category","restore_any_category","replicate_category","reorder_category","delete_category","delete_any_category","force_delete_category","force_delete_any_category","view_order","view_any_order","create_order","update_order","restore_order","restore_any_order","replicate_order","reorder_order","delete_order","delete_any_order","force_delete_order","force_delete_any_order","view_order::item","view_any_order::item","create_order::item","update_order::item","restore_order::item","restore_any_order::item","replicate_order::item","reorder_order::item","delete_order::item","delete_any_order::item","force_delete_order::item","force_delete_any_order::item","view_product","view_any_product","create_product","update_product","restore_product","restore_any_product","replicate_product","reorder_product","delete_product","delete_any_product","force_delete_product","force_delete_any_product","view_stock","view_any_stock","create_stock","update_stock","restore_stock","restore_any_stock","replicate_stock","reorder_stock","delete_stock","delete_any_stock","force_delete_stock","force_delete_any_stock","view_tag","view_any_tag","create_tag","update_tag","restore_tag","restore_any_tag","replicate_tag","reorder_tag","delete_tag","delete_any_tag","force_delete_tag","force_delete_any_tag"]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
