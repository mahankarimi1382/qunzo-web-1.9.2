<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permissions = [
            ['category' => 'Statistics Management', 'name' => 'total-users'],
            ['category' => 'Statistics Management', 'name' => 'total-agents'],
            ['category' => 'Statistics Management', 'name' => 'total-merchants'],
            ['category' => 'Statistics Management', 'name' => 'all-deposits'],
            ['category' => 'Statistics Management', 'name' => 'all-currencies'],
            ['category' => 'Statistics Management', 'name' => 'total-staff'],
            ['category' => 'Statistics Management', 'name' => 'total-withdraw'],
            ['category' => 'Statistics Management', 'name' => 'total-referral'],
            ['category' => 'Statistics Management', 'name' => 'total-automatic-gateway'],
            ['category' => 'Statistics Management', 'name' => 'total-ticket'],
            ['category' => 'Statistics Management', 'name' => 'total-transfer'],
            ['category' => 'Statistics Management', 'name' => 'total-cashout'],
            ['category' => 'Statistics Management', 'name' => 'total-payments'],

            ['category' => 'Statistics Management', 'name' => 'site-statistics-chart'],

            ['category' => 'Statistics Management', 'name' => 'top-country-statistics'],
            ['category' => 'Statistics Management', 'name' => 'top-browser-statistics'],
            ['category' => 'Statistics Management', 'name' => 'top-os-statistics'],
            ['category' => 'Statistics Management', 'name' => 'latest-users'],
            ['category' => 'Statistics Management', 'name' => 'latest-merchants'],
            ['category' => 'Statistics Management', 'name' => 'latest-agents'],

            ['category' => 'Customer Management', 'name' => 'customer-list'],
            ['category' => 'Customer Management', 'name' => 'customer-mail-send'],
            ['category' => 'Customer Management', 'name' => 'customer-basic-manage'],
            ['category' => 'Customer Management', 'name' => 'customer-balance-add-or-subtract'],
            ['category' => 'Customer Management', 'name' => 'customer-change-password'],
            ['category' => 'Customer Management', 'name' => 'all-type-status'],

            ['category' => 'Merchant Management', 'name' => 'merchant-list'],
            ['category' => 'Merchant Management', 'name' => 'merchant-requests'],
            ['category' => 'Merchant Management', 'name' => 'merchant-mail-send'],
            ['category' => 'Merchant Management', 'name' => 'merchant-basic-manage'],
            ['category' => 'Merchant Management', 'name' => 'merchant-balance-add-or-subtract'],
            ['category' => 'Merchant Management', 'name' => 'merchant-change-password'],
            ['category' => 'Merchant Management', 'name' => 'merchant-all-type-status'],
            ['category' => 'Merchant Management', 'name' => 'merchant-delete'],
            ['category' => 'Merchant Management', 'name' => 'merchant-kyc-info'],

            ['category' => 'Agent Management', 'name' => 'agent-list'],
            ['category' => 'Agent Management', 'name' => 'agent-requests'],
            ['category' => 'Agent Management', 'name' => 'agent-mail-send'],
            ['category' => 'Agent Management', 'name' => 'agent-basic-manage'],
            ['category' => 'Agent Management', 'name' => 'agent-balance-add-or-subtract'],
            ['category' => 'Agent Management', 'name' => 'agent-change-password'],
            ['category' => 'Agent Management', 'name' => 'agent-all-type-status'],
            ['category' => 'Agent Management', 'name' => 'agent-delete'],
            ['category' => 'Agent Management', 'name' => 'agent-kyc-info'],

            ['category' => 'Verification Center', 'name' => 'verification-list'],
            ['category' => 'Verification Center', 'name' => 'verification-action'],
            ['category' => 'Verification Center', 'name' => 'verification-form-manage'],

            ['category' => 'Role Management', 'name' => 'role-list'],
            ['category' => 'Role Management', 'name' => 'role-create'],
            ['category' => 'Role Management', 'name' => 'role-edit'],
            ['category' => 'Role Management', 'name' => 'role-delete'],

            ['category' => 'Staff Management', 'name' => 'staff-list'],
            ['category' => 'Staff Management', 'name' => 'staff-create'],
            ['category' => 'Staff Management', 'name' => 'staff-edit'],
            ['category' => 'Staff Management', 'name' => 'staff-delete'],

            ['category' => 'Transaction Management', 'name' => 'transaction-list'],
            ['category' => 'Transaction Management', 'name' => 'admin-profits'],

            ['category' => 'Virtual Card Management', 'name' => 'virtual-card-list'],
            ['category' => 'Virtual Card Management', 'name' => 'virtual-card-topup'],
            ['category' => 'Virtual Card Management', 'name' => 'virtual-card-status-change'],

            ['category' => 'Deposit Management', 'name' => 'automatic-gateway-manage'],
            ['category' => 'Deposit Management', 'name' => 'manual-gateway-manage'],
            ['category' => 'Deposit Management', 'name' => 'deposit-list'],
            ['category' => 'Deposit Management', 'name' => 'deposit-action'],

            ['category' => 'Withdraw Management', 'name' => 'withdraw-list'],
            ['category' => 'Withdraw Management', 'name' => 'withdraw-method-manage'],
            ['category' => 'Withdraw Management', 'name' => 'withdraw-action'],
            ['category' => 'Withdraw Management', 'name' => 'withdraw-schedule'],

            ['category' => 'Referral Management', 'name' => 'manage-referral'],
            ['category' => 'Referral Management', 'name' => 'referral-create'],
            ['category' => 'Referral Management', 'name' => 'referral-edit'],
            ['category' => 'Referral Management', 'name' => 'referral-delete'],
            ['category' => 'Appearance Management', 'name' => 'custom-css'],

            ['category' => 'Frontend Management', 'name' => 'site-theme-manage'],
            ['category' => 'Frontend Management', 'name' => 'landing-page-manage'],
            ['category' => 'Frontend Management', 'name' => 'footer-manage'],
            ['category' => 'Frontend Management', 'name' => 'navigation-manage'],

            ['category' => 'Bill Management', 'name' => 'bill-service-import'],
            ['category' => 'Bill Management', 'name' => 'bill-service-list'],
            ['category' => 'Bill Management', 'name' => 'bill-service-edit'],
            ['category' => 'Bill Management', 'name' => 'bill-convert-rate'],
            ['category' => 'Bill Management', 'name' => 'all-bills'],
            ['category' => 'Bill Management', 'name' => 'pending-bills'],
            ['category' => 'Bill Management', 'name' => 'complete-bills'],
            ['category' => 'Bill Management', 'name' => 'return-bills'],

            ['category' => 'Support Ticket Management', 'name' => 'support-ticket-list'],
            ['category' => 'Support Ticket Management', 'name' => 'support-ticket-action'],

            ['category' => 'Mail & Newsletter Management', 'name' => 'mail-send-all'],

            ['category' => 'Subscriber Management', 'name' => 'subscriber-list'],
            ['category' => 'Subscriber Management', 'name' => 'subscriber-mail-send'],

            ['category' => 'Setting Management', 'name' => 'site-setting'],
            ['category' => 'Setting Management', 'name' => 'email-setting'],
            ['category' => 'Setting Management', 'name' => 'plugin-setting'],
            ['category' => 'Setting Management', 'name' => 'currencies-setting'],
            ['category' => 'Setting Management', 'name' => 'currency-manage'],
            ['category' => 'Setting Management', 'name' => 'currency-create'],
            ['category' => 'Setting Management', 'name' => 'currency-edit'],
            ['category' => 'Setting Management', 'name' => 'currency-delete'],
            ['category' => 'Setting Management', 'name' => 'language-setting'],
            ['category' => 'Setting Management', 'name' => 'page-setting'],
            ['category' => 'Setting Management', 'name' => 'notification-tune-setting'],

            ['category' => 'Template Management', 'name' => 'template-list'],
            ['category' => 'Template Management', 'name' => 'template-edit'],
            ['category' => 'Template Management', 'name' => 'template-update'],
            ['category' => 'Template Management', 'name' => 'template-delete'],
            ['category' => 'Template Management', 'name' => 'push-notification-template'],

            ['category' => 'System Management', 'name' => 'manage-cron-job'],
            ['category' => 'System Management', 'name' => 'cron-job-create'],
            ['category' => 'System Management', 'name' => 'cron-job-edit'],
            ['category' => 'System Management', 'name' => 'cron-job-delete'],
            ['category' => 'System Management', 'name' => 'cron-job-logs'],
            ['category' => 'System Management', 'name' => 'cron-job-run'],

            ['category' => 'System Management', 'name' => 'addon-manage'],

            ['category' => 'System Management', 'name' => 'clear-cache'],
            ['category' => 'System Management', 'name' => 'application-details'],
        ];

        foreach ($permissions as $permission) {
            $permission = Permission::create([
                'guard_name' => 'admin',
                'name' => $permission['name'],
                'category' => $permission['category'],
            ]);
        }
    }
}
