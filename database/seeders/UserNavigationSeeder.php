<?php

namespace Database\Seeders;

use App\Models\UserNavigation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Uri;

class UserNavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        UserNavigation::truncate();

        $navigationData = [
            // Users Navigation
            [
                'icon' => 'solar:widget-add-bold-duotone',
                'visible_to' => 'user',
                'url' => Uri::route('user.dashboard')->path(),
                'type' => 'dashboard',
                'name' => 'Dashboard',
                'position' => 1,
            ],
            [
                'icon' => 'solar:wallet-money-bold-duotone',
                'visible_to' => 'user',
                'url' => Uri::route('user.userWallet.index')->path(),
                'type' => 'my_wallets',
                'name' => 'My Wallets',
                'position' => 2,
            ],
            [
                'icon' => 'solar:qr-code-bold-duotone',
                'visible_to' => 'user',
                'url' => Uri::route('user.myQrcode')->path(),
                'type' => 'qr_code',
                'name' => 'QR Code',
                'position' => 3,
            ],
            [
                'icon' => 'solar:add-square-bold-duotone',
                'visible_to' => 'user',
                'url' => Uri::route('user.addMoney')->path(),
                'type' => 'add_money',
                'name' => 'Add Money',
                'position' => 4,
            ],
            [
                'icon' => 'solar:plain-3-bold-duotone',
                'visible_to' => 'user',
                'url' => Uri::route('user.payment.index')->path(),
                'type' => 'make_payment',
                'name' => 'Make Payment',
                'position' => 5,
            ],
            [
                'icon' => 'solar:bill-list-bold-duotone',
                'visible_to' => 'user',
                'url' => Uri::route('user.invoices.index')->path(),
                'type' => 'invoice',
                'name' => 'Invoice',
                'position' => 6,
            ],
            [
                'icon' => 'solar:wad-of-money-bold-duotone',
                'visible_to' => 'user',
                'url' => Uri::route('user.request.money.index')->path(),
                'type' => 'request_money',
                'name' => 'Request Money',
                'position' => 7,
            ],
            [
                'icon' => 'solar:gift-bold-duotone',
                'visible_to' => 'user',
                'url' => Uri::route('user.gift.redeem')->path(),
                'type' => 'gift',
                'name' => 'Gift',
                'position' => 8,
            ],
            [
                'icon' => 'solar:card-transfer-bold-duotone',
                'visible_to' => 'user',
                'url' => Uri::route('user.transferMoney')->path(),
                'type' => 'transfer',
                'name' => 'Transfer',
                'position' => 9,
            ],
            [
                'icon' => 'solar:cash-out-bold-duotone',
                'visible_to' => 'user',
                'url' => Uri::route('user.cashout.index')->path(),
                'type' => 'cash_out',
                'name' => 'Cash Out',
                'position' => 10,
            ],
            [
                'icon' => 'solar:inbox-out-bold-duotone',
                'visible_to' => 'user',
                'url' => Uri::route('user.withdrawMoney.index')->path(),
                'type' => 'withdraw',
                'name' => 'Withdraw',
                'position' => 11,
            ],
            [
                'icon' => 'solar:square-transfer-vertical-bold-duotone',
                'visible_to' => 'user',
                'url' => Uri::route('user.exchange')->path(),
                'type' => 'exchange',
                'name' => 'Exchange',
                'position' => 12,
            ],
            [
                'icon' => 'solar:chat-round-money-bold-duotone',
                'visible_to' => 'user',
                'url' => Uri::route('user.transactions')->path(),
                'type' => 'transactions',
                'name' => 'Transactions',
                'position' => 13,
            ],
            [
                'icon' => 'solar:user-plus-bold-duotone',
                'visible_to' => 'user',
                'url' => Uri::route('user.referral')->path(),
                'type' => 'inviting',
                'name' => 'Inviting',
                'position' => 14,
            ],

            // Agnet Navigation
            [
                'icon' => 'solar:widget-line-duotone',
                'visible_to' => 'agent',
                'url' => Uri::route('agent.dashboard')->path(),
                'type' => 'dashboard',
                'name' => 'Dashboard',
                'position' => 1,
            ],
            [
                'icon' => 'solar:wallet-money-line-duotone',
                'visible_to' => 'agent',
                'url' => Uri::route('agent.wallet.index')->path(),
                'type' => 'my_wallets',
                'name' => 'My Wallets',
                'position' => 2,
            ],
            [
                'icon' => 'solar:hand-money-line-duotone',
                'visible_to' => 'agent',
                'url' => Uri::route('agent.cash.index')->path(),
                'type' => 'cash_in',
                'name' => 'Cash In',
                'position' => 3,
            ],
            [
                'icon' => 'solar:square-transfer-horizontal-line-duotone',
                'visible_to' => 'agent',
                'url' => Uri::route('agent.transaction')->path(),
                'type' => 'transactions',
                'name' => 'Transactions',
                'position' => 4,
            ],
            [
                'icon' => 'solar:history-line-duotone',
                'visible_to' => 'agent',
                'url' => Uri::route('agent.profit.history')->path(),
                'type' => 'profit_history',
                'name' => 'Profit History',
                'position' => 5,
            ],
            [
                'icon' => 'solar:add-square-line-duotone',
                'visible_to' => 'agent',
                'url' => Uri::route('agent.addMoney')->path(),
                'type' => 'add_money',
                'name' => 'Add Money',
                'position' => 6,
            ],
            [
                'icon' => 'solar:cash-out-line-duotone',
                'visible_to' => 'agent',
                'url' => Uri::route('agent.withdraw.index')->path(),
                'type' => 'withdraw',
                'name' => 'Withdraw',
                'position' => 7,
            ],
            [
                'icon' => 'solar:bell-line-duotone',
                'visible_to' => 'agent',
                'url' => Uri::route('agent.notification.all')->path(),
                'type' => 'notifications',
                'name' => 'Notifications',
                'position' => 8,
            ],
            [
                'icon' => 'solar:qr-code-line-duotone',
                'visible_to' => 'agent',
                'url' => Uri::route('agent.myQrcode')->path(),
                'type' => 'qr_code',
                'name' => 'QR Code',
                'position' => 9,
            ],
            [
                'icon' => 'solar:podcast-line-duotone',
                'visible_to' => 'agent',
                'url' => Uri::route('agent.tickets')->path(),
                'type' => 'support',
                'name' => 'Support',
                'position' => 10,
            ],
            [
                'icon' => 'solar:settings-line-duotone',
                'visible_to' => 'agent',
                'url' => Uri::route('agent.setting.index')->path(),
                'type' => 'settings',
                'name' => 'Settings',
                'position' => 11,
            ],

            // Merchant Navigation
            [
                'icon' => 'solar:widget-line-duotone',
                'visible_to' => 'merchant',
                'url' => Uri::route('merchant.dashboard')->path(),
                'type' => 'dashboard',
                'name' => 'Dashboard',
                'position' => 1,
            ],
            [
                'icon' => 'solar:wallet-money-line-duotone',
                'visible_to' => 'merchant',
                'url' => Uri::route('merchant.wallet.index')->path(),
                'type' => 'wallet',
                'name' => 'My Wallets',
                'position' => 2,
            ],
            [
                'icon' => 'solar:qr-code-line-duotone',
                'visible_to' => 'merchant',
                'url' => Uri::route('merchant.myQrcode')->path(),
                'type' => 'qrcode',
                'name' => 'QR Code',
                'position' => 3,
            ],
            [
                'icon' => 'solar:key-minimalistic-square-2-line-duotone',
                'visible_to' => 'merchant',
                'url' => Uri::route('merchant.access.key')->path(),
                'type' => 'api',
                'name' => 'API Access Key',
                'position' => 4,
            ],
            [
                'icon' => 'solar:round-transfer-horizontal-line-duotone',
                'visible_to' => 'merchant',
                'url' => Uri::route('merchant.transactions')->path(),
                'type' => 'transactions',
                'name' => 'Transactions',
                'position' => 5,
            ],
            [
                'icon' => 'solar:hand-money-line-duotone',
                'visible_to' => 'merchant',
                'url' => Uri::route('merchant.withdraw.index')->path(),
                'type' => 'withdraw',
                'name' => 'Withdraw',
                'position' => 6,
            ],
            [
                'icon' => 'solar:bell-line-duotone',
                'visible_to' => 'merchant',
                'url' => Uri::route('merchant.notification.all')->path(),
                'type' => 'notification',
                'name' => 'Notifications',
                'position' => 7,
            ],
            [
                'icon' => 'solar:podcast-line-duotone',
                'visible_to' => 'merchant',
                'url' => Uri::route('merchant.tickets')->path(),
                'type' => 'support',
                'name' => 'Support',
                'position' => 8,
            ],
            [
                'icon' => 'solar:settings-line-duotone',
                'visible_to' => 'merchant',
                'url' => Uri::route('merchant.setting.index')->path(),
                'type' => 'settings',
                'name' => 'Settings',
                'position' => 9,
            ],
        ];

        foreach ($navigationData as $nav) {
            UserNavigation::create([
                'icon' => $nav['icon'],
                'visible_to' => $nav['visible_to'],
                'url' => $nav['url'],
                'type' => $nav['type'],
                'name' => $nav['name'],
                'position' => $nav['position'],
                'translation' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
