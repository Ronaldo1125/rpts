<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define all permissions
        $permissions = [
            // Agency management
            'agency-view', 'agency-create', 'agency-update', 'agency-delete',
            // Chapter management
            'chapter-view', 'chapter-create', 'chapter-update', 'chapter-delete',
            // Endorse Year management
            'endorse_year-view', 'endorse_year-create', 'endorse_year-update', 'endorse_year-delete',
            // Indicator management
            'indicator-view', 'indicator-create', 'indicator-update', 'indicator-delete',
            // Permission management
            'permission-view', 'permission-create', 'permission-update', 'permission-delete',
            // Project management
            'project-view', 'project-create', 'project-edit', 'project-update', 'project-delete',
            // Role management
            'role-view', 'role-create', 'role-update', 'role-delete',
            // Sector management
            'sector-view', 'sector-create', 'sector-update', 'sector-delete',
            // Sub Sector management
            'sub_sector-view', 'sub_sector-create', 'sub_sector-update', 'sub_sector-delete',
            // User management
            'user-view', 'user-create', 'user-update', 'user-delete',
            // CIPG Submission management
            'cipg_submission-create', 'cipg_submission-view', 'cipg_submission-edit', 'cipg_submission-update', 'cipg_submission-delete',
            // Specialized views / dashboards
            'rdc_review_validation-view',
            'admin_management-view',
            'report-view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // 2. Define roles and assign permissions
        
        // Administrator / Admin gets everything
        $adminRole1 = Role::findOrCreate('administrator', 'web');
        $adminRole2 = Role::findOrCreate('admin', 'web');
        $adminRole1->syncPermissions(Permission::all());
        $adminRole2->syncPermissions(Permission::all());

        // Chiefs / Division Heads
        $chiefRoles = ['division_head', 'division_chief', 'chief', 'pmed_chief'];
        $chiefPermissions = [
            'project-view', 'project-create', 'project-edit', 'project-update',
            'cipg_submission-view', 'cipg_submission-create', 'cipg_submission-edit', 'cipg_submission-update',
            'report-view', 'rdc_review_validation-view',
            'agency-view', 'sector-view', 'sub_sector-view', 'chapter-view', 'indicator-view',
            'user-view'
        ];
        foreach ($chiefRoles as $rName) {
            $role = Role::findOrCreate($rName, 'web');
            $role->syncPermissions($chiefPermissions);
        }

        // Staff
        $staffRoles = ['staff', 'pdipbd_staff', 'pmed_staff'];
        $staffPermissions = [
            'project-view', 'project-create', 'project-edit', 'project-update',
            'cipg_submission-view', 'cipg_submission-create', 'cipg_submission-edit', 'cipg_submission-update',
            'report-view', 'rdc_review_validation-view',
            'agency-view', 'sector-view', 'sub_sector-view', 'chapter-view', 'indicator-view'
        ];
        foreach ($staffRoles as $rName) {
            $role = Role::findOrCreate($rName, 'web');
            $role->syncPermissions($staffPermissions);
        }

        // Implementing Agency / Agency
        $agencyRoles = ['implementing_agency', 'agency'];
        $agencyPermissions = [
            'project-view', 'project-create', 'project-edit', 'project-update',
            'cipg_submission-view', 'cipg_submission-create', 'cipg_submission-edit', 'cipg_submission-update',
            'agency-view', 'sector-view', 'sub_sector-view', 'chapter-view', 'indicator-view'
        ];
        foreach ($agencyRoles as $rName) {
            $role = Role::findOrCreate($rName, 'web');
            $role->syncPermissions($agencyPermissions);
        }
    }
}
