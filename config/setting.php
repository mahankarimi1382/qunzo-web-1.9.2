<?php

return [
    'global' => [
        'title' => 'Global Settings',

        'elements' => [
            [
                'type' => 'file', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'admin_sidebar_logo', // unique name for field
                'label' => 'Admin Sidebar Logo', // you know what label it is
                'rules' => 'mimes:jpeg,jpg,png,svg|max:1000', // validation rule of laravel
                'value' => 'global/images/logo.png', // default value if you want
            ],
            [
                'type' => 'file', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'site_logo', // unique name for field
                'label' => 'Site Logo', // you know what label it is
                'rules' => 'mimes:jpeg,jpg,png,svg|max:1000', // validation rule of laravel
                'value' => 'global/images/logo.png', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'site_logo_height', // unique name for field
                'label' => 'Site Logo Height', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '35px', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'site_logo_width', // unique name for field
                'label' => 'Site Logo Width', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '35px', // default value if you want
            ],
            [
                'type' => 'file', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'site_favicon', // unique name for field
                'label' => 'Site Favicon', // you know what label it is
                'rules' => 'mimes:jpeg,jpg,png,svg|max:1000', // validation rule of laravel
                'value' => 'global/images/fav.png', // default value if you want
            ],
            [
                'type' => 'file', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'login_bg', // unique name for field
                'label' => 'Admin Login Cover', // you know what label it is
                'rules' => 'mimes:jpeg,jpg,png,svg|max:2000', // validation rule of laravel
                'value' => 'global/images/auth-bg.jpg', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'site_admin_prefix', // unique name for field
                'label' => 'Site Admin Prefix', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'admin', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'site_title', // unique name for field
                'label' => 'Site Title', // you know what label it is
                'rules' => 'required|min:2|max:50', // validation rule of laravel
                'value' => 'Money Chain', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'site_currency', // unique name for field
                'label' => 'Site Currency', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'USD', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'site_currency_decimals', // unique name for field
                'label' => 'Site Currency Decimals', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '2', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'currency_symbol', // unique name for field
                'label' => 'Currency Symbol', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '$', // default value if you want
            ],
            [
                'type' => 'dropdown', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'site_timezone', // unique name for field
                'label' => 'Site Timezone', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'UTC', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'integer', // data type, string, int, boolean
                'name' => 'referral_code_limit', // unique name for field
                'label' => 'Referral Code Lenth', // you know what label it is
                'info' => 'Maximum length is 30',
                'rules' => 'required|max:30', // validation rule of laravel
                'value' => '6', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'integer', // data type, string, int, boolean
                'name' => 'account_no_limit', // unique name for field
                'label' => 'Account ID Digit Limit', // you know what label it is
                'info' => 'Maximum length is 20',
                'rules' => 'required|max:20', // validation rule of laravel
                'value' => '10', // default value if you want
            ],
            [
                'type' => 'email', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'site_email', // unique name for field
                'label' => 'Site Email', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'admin@tdevs.co', // default value if you want
            ],
            [
                'type' => 'email', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'support_email', // unique name for field
                'label' => 'Support Email', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'support@tdevs.co', // default value if you want
            ],
        ],
    ],

    'permission' => [
        'title' => 'Permission Settings',
        'elements' => [
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'merchant_system', // unique name for field
                'label' => 'Merchant System', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'agent_system', // unique name for field
                'label' => 'Agent System', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'email_verification', // unique name for field
                'label' => 'Email Verification', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'virtual_card', // unique name for field
                'label' => 'Virtual Card', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'card_creation', // unique name for field
                'label' => 'Card Creation', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'passcode_verification', // unique name for field
                'label' => 'Passcode Verification', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'fa_verification', // unique name for field
                'label' => '2FA Verification', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],

            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'account_creation', // unique name for field
                'label' => 'User Registration', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'user_deposit', // unique name for field
                'label' => 'User Deposit', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'user_transfer', // unique name for field
                'label' => 'User Transfer', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'user_pay_bill', // unique name for field
                'label' => 'User Pay Bill', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'user_cashout', // unique name for field
                'label' => 'User Cashout', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'user_gift', // unique name for field
                'label' => 'User Gift Code', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'user_payment', // unique name for field
                'label' => 'User Payment', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'user_invoice', // unique name for field
                'label' => 'User Payment Links', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'user_request_money', // unique name for field
                'label' => 'User Request Money', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'user_exchange', // unique name for field
                'label' => 'User Exchange', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'user_ticket', // unique name for field
                'label' => 'User Ticket', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'user_withdraw', // unique name for field
                'label' => 'User Withdraw', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'sign_up_referral', // unique name for field
                'label' => 'User Referral', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'referral_rules_visibility', // unique name for field
                'label' => 'Referral Rules Visibility', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '1', // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'referral_signup_bonus', // unique name for field
                'label' => 'Signup Bonus', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'site_animation', // unique name for field
                'label' => 'Site Animation', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'back_to_top', // unique name for field
                'label' => 'Site Back to Top', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'language_switcher', // unique name for field
                'label' => 'Language Switcher', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 0, // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'debug_mode', // unique name for field
                'label' => 'Development Mode', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 0, // default value if you want
            ],
        ],
    ],

    'fee' => [
        'title' => 'Site Bonus, Fee Settings',
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'referral_bonus', // unique name for field
                'label' => 'Referral Bonus', // you know what label it is
                'rules' => 'required|regex:/^\d+(\.\d{1,2})?$/', // validation rule of laravel
                'value' => 20, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'signup_bonus', // unique name for field
                'label' => 'Sign Up Bonus', // you know what label it is
                'rules' => 'required|regex:/^\d+(\.\d{1,2})?$/', // validation rule of laravel
                'value' => 20, // default value if you want
            ],
        ],
    ],
    'passcode' => [
        'title' => 'Passcode Management',
        'elements' => [
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'deposit_passcode_status', // unique name for field
                'label' => 'Deposit', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '0', // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'transfer_money_passcode_status', // unique name for field
                'label' => 'Transfer Money', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '0', // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'make_payment_passcode_status', // unique name for field
                'label' => 'Make Payment', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '0', // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'gift_passcode_status', // unique name for field
                'label' => 'Gift', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '0', // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'cashout_passcode_status', // unique name for field
                'label' => 'Cashout', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '0', // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'request_money_accept_passcode_status', // unique name for field
                'label' => 'Request Money Accept', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '0', // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'invoice_passcode_status', // unique name for field
                'label' => 'Invoice', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '0', // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'exchange_passcode_status', // unique name for field
                'label' => 'Exchange', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '0', // default value if you want
            ],
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'withdraw_passcode_status', // unique name for field
                'label' => 'Withdraw', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '0', // default value if you want
            ],
        ],
    ],
    'cashout' => [
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashout_charge', // unique name for field
                'label' => 'Cashout Charge', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 5, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashout_charge_type', // unique name for field
                'label' => 'Cashout Charge Type', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'percentage', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashout_agent_commission', // unique name for field
                'label' => 'Cashout Agent Commission', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 5, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashout_agent_commission_type', // unique name for field
                'label' => 'Cashout Agent Commission Type', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'percentage', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashout_minimum', // unique name for field
                'label' => 'Cashout Minimum', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 100, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashout_maximum', // unique name for field
                'label' => 'Cashout Maximum', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 10000, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashout_daily_limit', // unique name for field
                'label' => 'Daily Cashout Limit', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 100000, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashout_monthly_limit', // unique name for field
                'label' => 'Monthly Cashout Limit', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 1000000, // default value if you want
            ],
        ],
    ],
    'cashin' => [
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashin_charge', // unique name for field
                'label' => 'Cash In Charge', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 5, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashin_charge_type', // unique name for field
                'label' => 'Cash In Charge Type', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'percentage', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashin_agent_commission', // unique name for field
                'label' => 'Cash In Agent Commission', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 5, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashin_agent_commission_type', // unique name for field
                'label' => 'Cash In Agent Commission Type', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'percentage', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashin_minimum', // unique name for field
                'label' => 'Cash In Minimum', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 100, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashin_maximum', // unique name for field
                'label' => 'Cash In Maximum', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 10000, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashin_daily_limit', // unique name for field
                'label' => 'Daily Cash In Limit', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 100000, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'cashin_monthly_limit', // unique name for field
                'label' => 'Monthly Cash In Limit', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 1000000, // default value if you want
            ],
        ],
    ],
    'transfer' => [
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'transfer_charge', // unique name for field
                'label' => 'Transfer Charge', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 5, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'transfer_charge_type', // unique name for field
                'label' => 'Transfer Charge Type', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'percentage', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'transfer_minimum', // unique name for field
                'label' => 'Transfer Minimum', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 100, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'transfer_maximum', // unique name for field
                'label' => 'Transfer Maximum', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 10000, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'transfer_daily_limit', // unique name for field
                'label' => 'Daily Transfer Limit', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 10000, // default value if you want
            ],
        ],
    ],
    'gift' => [
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'gift_charge', // unique name for field
                'label' => 'Gift Charge', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 5, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'gift_charge_type', // unique name for field
                'label' => 'Gift Charge Type', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'percentage', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'gift_minimum', // unique name for field
                'label' => 'Gift Minimum', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 100, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'gift_maximum', // unique name for field
                'label' => 'Gift Maximum', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 10000, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'gift_daily_limit', // unique name for field
                'label' => 'Daily Gift Limit', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 100000, // default value if you want
            ],
        ],
    ],
    'invoice' => [
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'invoice_charge', // unique name for field
                'label' => 'Invoice Charge', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 5, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'invoice_charge_type', // unique name for field
                'label' => 'Invoice Charge Type', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'percentage', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'invoice_daily_limit', // unique name for field
                'label' => 'Daily Invoice Limit', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 100000, // default value if you want
            ],
        ],
    ],
    'request_money' => [
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'request_money_minimum', // unique name for field
                'label' => 'Min Payment Amount', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 100, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'request_money_maximum', // unique name for field
                'label' => 'Max Payment Amount', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 10000, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'request_money_charge', // unique name for field
                'label' => 'Request Money Charge', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 5, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'request_money_charge_type', // unique name for field
                'label' => 'Request Money Charge Type', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'percentage', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'request_money_daily_limit', // unique name for field
                'label' => 'Daily Request Money Limit', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 100000, // default value if you want
            ],
        ],
    ],
    'exchange' => [
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'exchange_minimum', // unique name for field
                'label' => 'Exchange Minimum', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 100, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'exchange_maximum', // unique name for field
                'label' => 'Exchange Maximum', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 10000, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'exchange_charge', // unique name for field
                'label' => 'Exchange Charge', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 5, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'exchange_charge_type', // unique name for field
                'label' => 'Exchange Charge Type', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'percentage', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'exchange_daily_limit', // unique name for field
                'label' => 'Daily Exchange Limit', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 100000, // default value if you want
            ],
        ],
    ],
    'api_payment' => [
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'api_payment_charge', // unique name for field
                'label' => 'API Payment Charge', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 5, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'api_payment_charge_type', // unique name for field
                'label' => 'API Payment Charge Type', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'percentage', // default value if you want
            ],
        ],
    ],
    'make_payment' => [
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'payment_minimum', // unique name for field
                'label' => 'Min Payment Amount', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 100, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'payment_maximum', // unique name for field
                'label' => 'Max Payment Amount', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 10000, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'user_make_payment_charge', // unique name for field
                'label' => 'User Make Payment Charge', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 5, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'user_make_payment_charge_type', // unique name for field
                'label' => 'User Make Payment Charge Type', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'percentage', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'merchant_make_payment_charge', // unique name for field
                'label' => 'Merchant Make Payment Charge', // you know what label it is
                'rules' => 'required|numeric', // validation rule of laravel
                'value' => 5, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'merchant_make_payment_charge_type', // unique name for field
                'label' => 'Merchant Make Payment Charge Type', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'percentage', // default value if you want
            ],
        ],
    ],
    'referral_level' => [
        'title' => 'Referral Level Settings',
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'deposit', // unique name for field
                'label' => 'Deposit', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '1', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'withdraw', // unique name for field
                'label' => 'Withdraw', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '1', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'cashout', // unique name for field
                'label' => 'Cashout', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '1', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'transfer', // unique name for field
                'label' => 'Transfer', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '1', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'exchange', // unique name for field
                'label' => 'Exchange', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '1', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'payment', // unique name for field
                'label' => 'Payment', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '1', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'request_money', // unique name for field
                'label' => 'Request Money', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '1', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'create_gift', // unique name for field
                'label' => 'Create Gift', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '1', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'invoice_pay', // unique name for field
                'label' => 'Invoice Pay', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '1', // default value if you want
            ],
        ],
    ],
    'virtual_card' => [
        'title' => 'Virtual Card Fees & Limit',
        'elements' => [
            [
                'type' => 'file',
                'data' => 'string',
                'name' => 'card_bg_image',
                'label' => 'Card Background Image',
                'rules' => 'required',
                'value' => '',
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'card_creation_charge',
                'label' => 'Card Creation Charge',
                'rules' => 'required',
                'value' => '2',
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'card_topup_charge',
                'label' => 'Card Topup Charge',
                'rules' => 'required',
                'value' => '2',
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'card_topup_charge_type', // unique name for field
                'label' => 'Card Topup Charge Type', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'percentage', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'min_card_topup', // unique name for field
                'label' => 'Min Card Topup', // you know what label it is
                'rules' => 'required|regex:/^\d+(\.\d{1,2})?$/', // validation rule of laravel
                'value' => 10, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'max_card_topup', // unique name for field
                'label' => 'Max Card Topup', // you know what label it is
                'rules' => 'required|regex:/^\d+(\.\d{1,2})?$/', // validation rule of laravel
                'value' => 100, // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'double', // data type, string, int, boolean
                'name' => 'card_creation_limit', // unique name for field
                'label' => 'Card Creation Limit Per User', // you know what label it is
                'rules' => 'required|regex:/^\d+(\.\d{1,2})?$/', // validation rule of laravel
                'value' => 4, // default value if you want
            ],
        ],
    ],


    'gift_card' => [
        'title' => 'Gift Card Fees',
        'elements' => [
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'giftcard_order_charge',
                'label' => 'Gift Card Order Charge',
                'rules' => 'required',
                'value' => '2',
            ]
        ],
    ],

    'inactive_user' => [
        'title' => 'Inactive User Setting',
        'elements' => [
            [
                'type' => 'switch',
                'data' => 'string',
                'name' => 'inactive_account_disabled',
                'label' => 'Inactive Account Disable',
                'rules' => 'required',
                'value' => '1',
            ],
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'inactive_days',
                'label' => 'Inactive Days',
                'rules' => 'required',
                'value' => '30',
            ],
        ],
    ],

    'mail' => [
        'title' => 'Mail Settings',
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'email_from_name', // unique name for field
                'label' => 'Email From Name', // you know what label it is
                'rules' => 'required|min:5|max:50', // validation rule of laravel
                'value' => 'Tdevs', // default value if you want
            ],
            [
                'type' => 'email', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'email_from_address', // unique name for field
                'label' => 'Email From Address', // you know what label it is
                'rules' => 'required|min:5|max:50', // validation rule of laravel
                'value' => 'wd2rasel@gmail.com', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'mailing_driver', // unique name for field
                'label' => 'Mailing Driver', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'SMTP', // default value if you want
            ],

            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'mail_username', // unique name for field
                'label' => 'Mail Username', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '465', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'mail_password', // unique name for field
                'label' => 'Mail Password', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '0000', // default value if you want
            ],

            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'mail_host', // unique name for field
                'label' => 'Mail Host', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'mail.tdevs.co', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'integer', // data type, string, int, boolean
                'name' => 'mail_port', // unique name for field
                'label' => 'Mail Port', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '465', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'mail_secure', // unique name for field
                'label' => 'Mail Secure', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'ssl', // default value if you want
            ],
        ],
    ],

    'site_maintenance' => [
        'title' => 'Site Maintenance',
        'elements' => [
            [
                'type' => 'checkbox', // input fields type
                'data' => 'boolean', // data type, string, int, boolean
                'name' => 'maintenance_mode', // unique name for field
                'label' => 'Maintenance Mode', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 1, // default value if you want
            ],
        ],
    ],

    'system' => [
        'title' => 'System Settings',
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'session_lifetime', // unique name for field
                'label' => 'Session Lifetime', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => '120', // default value if you want
            ],
        ],
    ],

    'p2p' => [
        'title' => 'P2P Trading Settings',
        'elements' => [
            [
                'type' => 'text',
                'data' => 'string',
                'name' => 'ads_poster_fee',
                'label' => 'Ads Poster Fee',
                'rules' => 'required|numeric|min:0',
                'value' => '0',
            ],
            [
                'type' => 'dropdown',
                'data' => 'string',
                'name' => 'ads_poster_fee_type',
                'label' => 'Ads Poster Fee Type',
                'rules' => 'required|in:fixed',
                'value' => 'fixed',
            ],
            [
                'type' => 'checkbox',
                'data' => 'boolean',
                'name' => 'ads_approval_required',
                'label' => 'Ads Approval Required',
                'rules' => 'required',
                'value' => 0,
            ],
            [
                'type' => 'text',
                'data' => 'double',
                'name' => 'order_fee_percent',
                'label' => 'Order Fee Percent',
                'rules' => 'required|numeric|min:0',
                'value' => 0,
            ],
            [
                'type' => 'text',
                'data' => 'integer',
                'name' => 'seller_release_grace_minutes',
                'label' => 'Seller Release Grace Minutes',
                'rules' => 'required|integer|min:1',
                'value' => 30,
            ],
            [
                'type' => 'text',
                'data' => 'double',
                'name' => 'min_transactions_success_rate',
                'label' => 'Minimum Transactions Success Rate',
                'rules' => 'required|numeric|min:0|max:100',
                'value' => 0,
            ],
            [
                'type' => 'text',
                'data' => 'double',
                'name' => 'min_completion_rate',
                'label' => 'Minimum Completion Rate',
                'rules' => 'required|numeric|min:0|max:100',
                'value' => 0,
            ],
            [
                'type' => 'text',
                'data' => 'integer',
                'name' => 'min_orders_required',
                'label' => 'Minimum Orders Required',
                'rules' => 'required|integer|min:0',
                'value' => 0,
            ],
            [
                'type' => 'text',
                'data' => 'integer',
                'name' => 'verified_trader_min_completed_orders',
                'label' => 'Verified Trader Min Completed Orders',
                'rules' => 'required|integer|min:0',
                'value' => 0,
            ],
            [
                'type' => 'text',
                'data' => 'double',
                'name' => 'verified_trader_min_completion_rate',
                'label' => 'Verified Trader Min Completion Rate',
                'rules' => 'required|numeric|min:0|max:100',
                'value' => 0,
            ],
        ],
    ],

    'meta' => [
        'elements' => [
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'meta_description', // unique name for field
                'label' => 'Meta Description', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'Qunzo is a fully online transaction system.', // default value if you want
            ],
            [
                'type' => 'text', // input fields type
                'data' => 'string', // data type, string, int, boolean
                'name' => 'meta_keywords', // unique name for field
                'label' => 'Meta Keywords', // you know what label it is
                'rules' => 'required', // validation rule of laravel
                'value' => 'qunzo, money exchange', // default value if you want
            ],
        ],
    ],
];
