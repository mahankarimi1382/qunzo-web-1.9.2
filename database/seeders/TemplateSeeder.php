<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $data = [
            [
                'name' => 'User Mail Send',
                'code' => 'user_mail',
                'for' => 'User',
                'banner' => 'global/images/Uxp3vfYFFi4GuO95lyZn.jpg',
                'title' => 'Admin Mail',
                'subject' => '[[subject]] for [[full_name]]',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => '[[message]]<br /> Thanks for joining us [[site_title]]<br /><br /><br />Find out more about in - [[site_url]]',
                'button_level' => 'Login Your Account',
                'button_link' => null,
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[site_url]]","[[site_title]]","[[subject]]","[[message]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'mail', // Lucide icon for mail
                'sms_body' => 'Thanks for joining us [[site_title]]. Find out more at [[site_url]].',
                'notification_body' => 'Thanks for joining us [[site_title]]. Find out more at [[site_url]].',
            ],
            [
                'name' => 'Subscriber Mail Send',
                'code' => 'subscriber_mail',
                'for' => 'Subscriber',
                'banner' => null,
                'title' => 'Welcome to [[site_title]]',
                'subject' => '[[subject]] for [[full_name]]',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'Thanks for joining our platform! ---  [[site_title]]<br /><br />[[message]]<br /><br />As a member of our platform, you can manage your account, buy or sell cryptocurrency, invest and earn profits.<br /><br />Find out more about in - [[site_url]]',
                'button_level' => 'Login Your Account',
                'button_link' => null,
                'footer_status' => 1,
                'footer_body' => 'Thanks for joining our platform! ---  [[site_title]]<br /><br />[[message]]<br /><br />As a member of our platform, you can manage your account, buy or sell cryptocurrency, invest and earn profits.<br /><br />Find out more about in - [[site_url]]',
                'short_codes' => '["[[full_name]]","[[site_url]]","[[site_title]]","[[subject]]","[[message]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'mail', // Lucide icon for mail
                'sms_body' => 'Welcome to [[site_title]]! Manage your account, trade crypto, and earn profits. Visit [[site_url]].',
                'notification_body' => 'Welcome to [[site_title]]! Manage your account, trade crypto, and earn profits. Visit [[site_url]].',
            ],
            [
                'name' => 'Manual Deposit Request',
                'code' => 'admin_manual_deposit',
                'for' => 'Admin',
                'banner' => 'global/images/deposit_request.jpg',
                'title' => 'New Manual Deposit Request',
                'subject' => 'New Deposit Request of [[amount]] [[currency]]',
                'salutation' => 'Hello Admin,',
                'email_body' => 'A new manual deposit request has been submitted.<br /><br />
                Amount: [[amount]] [[currency]]<br />
                Charge: [[charge]] [[currency]]<br />
                Wallet: [[wallet]]<br />
                Gateway: [[gateway]]<br />
                Requested At: [[request_at]]<br />
                Total Amount: [[total_amount]] [[currency]]<br /><br />
                Please review and approve it.',
                'button_level' => 'View Request',
                'button_link' => '[[request_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[amount]]","[[charge]]","[[wallet]]","[[gateway]]","[[request_at]]","[[total_amount]]","[[request_link]]","[[site_title]]","[[currency]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'dollar-sign', // Lucide icon for deposit
                'sms_body' => 'New manual deposit request of [[amount]] [[currency]]. Please review and approve.',
                'notification_body' => 'New manual deposit request of [[amount]] [[currency]]. Please review and approve.',

            ],
            [
                'name' => 'Manual Deposit Request Approved',
                'code' => 'user_manual_deposit_approved',
                'for' => 'User',
                'banner' => 'global/images/deposit_approved.jpg',
                'title' => 'Deposit Request Approved',
                'subject' => 'Your Deposit Request of [[amount]] [[currency]] has been Approved',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'We are pleased to inform you that your deposit request has been approved.<br /><br />
        Amount: [[amount]] [[currency]]<br />
        Charge: [[charge]] [[currency]]<br />
        Wallet: [[wallet]]<br />
        Gateway: [[gateway]]<br />
        Requested At: [[request_at]]<br />
        Total Amount: [[total_amount]] [[currency]]<br /><br />
        The funds have been credited to your account. Thank you for using our services!',
                'button_level' => 'View Transaction',
                'button_link' => '[[transaction_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[amount]]","[[charge]]","[[wallet]]","[[gateway]]","[[request_at]]","[[total_amount]]","[[transaction_link]]","[[site_title]]","[[currency]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'check-circle', // Lucide icon for approval
                'sms_body' => 'Your deposit request of [[amount]] [[currency]] has been approved. Funds have been credited to your account.',
                'notification_body' => 'Your deposit request of [[amount]] [[currency]] has been approved. Funds have been credited to your account.',
            ],
            [
                'name' => 'Manual Deposit Request Rejected',
                'code' => 'user_manual_deposit_rejected',
                'for' => 'User',
                'banner' => 'global/images/deposit_rejected.jpg',
                'title' => 'Deposit Request Rejected',
                'subject' => 'Your Deposit Request of [[amount]] [[currency]] has been Rejected',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'We regret to inform you that your deposit request has been rejected.<br /><br />
        Amount: [[amount]] [[currency]]<br />
        Charge: [[charge]] [[currency]]<br />
        Wallet: [[wallet]]<br />
        Gateway: [[gateway]]<br />
        Requested At: [[request_at]]<br />
        Total Amount: [[total_amount]] [[currency]]<br /><br />
        Reason for Rejection: [[rejection_reason]]<br /><br />',
                'button_level' => 'Contact Support',
                'button_link' => '[[support_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[amount]]","[[charge]]","[[wallet]]","[[gateway]]","[[request_at]]","[[total_amount]]","[[rejection_reason]]","[[site_title]]","[[currency]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'x-circle', // Lucide icon for rejection
                'sms_body' => 'Your deposit request of [[amount]] [[currency]] has been rejected. Reason: [[rejection_reason]].',
                'notification_body' => 'Your deposit request of [[amount]] [[currency]] has been rejected. Reason: [[rejection_reason]].',
            ],
            [
                'name' => 'Withdraw Request',
                'code' => 'admin_withdraw_request',
                'for' => 'Admin',
                'banner' => 'global/images/withdraw_request.jpg',
                'title' => 'New Withdraw Request',
                'subject' => 'Withdraw Request of [[amount]] [[currency]]',
                'salutation' => 'Hello Admin,',
                'email_body' => 'A new withdrawal request has been submitted.<br /><br />
                Amount: [[amount]] [[currency]]<br />
                Charge: [[charge]] [[currency]]<br />
                Wallet: [[wallet]]<br />
                Gateway: [[gateway]]<br />
                Requested At: [[request_at]]<br />
                Total Amount: [[total_amount]] [[currency]]<br /><br />
                Please review and approve it.',
                'button_level' => 'View Request',
                'button_link' => '[[request_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[amount]]","[[charge]]","[[wallet]]","[[gateway]]","[[request_at]]","[[total_amount]]","[[request_link]]","[[site_title]]","[[currency]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'arrow-up', // Lucide icon for withdraw
                'sms_body' => 'New withdrawal request of [[amount]] [[currency]]. Please review and approve.',
                'notification_body' => 'New withdrawal request of [[amount]] [[currency]]. Please review and approve.',
            ],
            [
                'name' => 'Ticket Reply',
                'code' => 'admin_ticket_reply',
                'for' => 'Admin',
                'banner' => 'global/images/ticket_reply.jpg',
                'title' => 'New Ticket Reply',
                'subject' => 'New Reply for Ticket: [[title]]',
                'salutation' => 'Hello Admin,',
                'email_body' => 'A new reply has been received for the support ticket.<br /><br />
                Ticket Title: [[title]]<br />
                Message: [[message]]<br /><br />
                Click the button below to view and respond.',
                'button_level' => 'View Ticket',
                'button_link' => '[[reply_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[title]]","[[message]]","[[reply_link]]","[[site_title]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'message-circle', // Lucide icon for ticket reply
                'sms_body' => 'New reply received for ticket: [[title]]. Message: [[message]].',
                'notification_body' => 'New reply received for ticket: [[title]]. Message: [[message]].',
            ],
            [
                'name' => 'Invoice Payment Received',
                'code' => 'user_invoice_payment',
                'for' => 'User',
                'banner' => 'global/images/invoice_payment.jpg',
                'title' => 'Invoice Payment Received',
                'subject' => 'Payment received for Invoice #[[invoice_number]]',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'We have received your payment for Invoice #[[invoice_number]].<br /><br />Amount: [[amount]] [[currency]]<br />Charge: [[charge]] [[currency]]<br />Total: [[total_amount]] [[currency]]<br /><br />Thank you for your payment!',
                'button_level' => 'View Invoice',
                'button_link' => '[[invoice_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[invoice_number]]","[[amount]]","[[charge]]","[[total_amount]]","[[invoice_link]]","[[site_title]]","[[currency]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'file-text', // Lucide icon for invoice
                'sms_body' => 'Payment received for Invoice #[[invoice_number]]. Amount: [[amount]] [[currency]]. Thank you!',
                'notification_body' => 'Payment received for Invoice #[[invoice_number]]. Amount: [[amount]] [[currency]]. Thank you!',

            ],
            [
                'name' => 'Request Money',
                'code' => 'user_request_money',
                'for' => 'User',
                'banner' => 'global/images/request_money.jpg',
                'title' => 'Money Request Received',
                'subject' => 'You received a money request from [[sender_name]]',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'You have received a money request from [[sender_name]].<br /><br />Amount: [[amount]] [[currency]]<br />Charge: [[charge]] [[currency]]<br />Total: [[total_amount]] [[currency]]<br />Sender Note: [[sender_note]]<br />Sender Wallet: [[sender_wallet]]<br />Sender Account No: [[sender_account_no]]',
                'button_level' => 'View Request',
                'button_link' => '[[request_money_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[amount]]","[[charge]]","[[total_amount]]","[[sender_name]]","[[sender_note]]","[[sender_wallet]]","[[sender_account_no]]","[[request_money_link]]","[[site_title]]","[[currency]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'dollar-sign', // Lucide icon for money request
                'sms_body' => 'You received a money request from [[sender_name]]. Amount: [[amount]] [[currency]].',
                'notification_body' => 'You received a money request from [[sender_name]]. Amount: [[amount]] [[currency]].',
            ],
            [
                'name' => 'Gift Redeemed',
                'code' => 'user_gift_redeemed',
                'for' => 'User',
                'banner' => 'global/images/gift_redeemed.jpg',
                'title' => 'Gift Successfully Redeemed',
                'subject' => 'Gift Redeemed by [[redeemer_name]]',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'A gift has been redeemed successfully.<br /><br />Redeemer Name: [[redeemer_name]]<br />Redeemer Account No: [[redeemer_account_no]]<br />Amount: [[amount]] [[currency]]<br />Gift Code: [[gift_code]]<br />Redeemed At: [[redeemed_at]]',
                'button_level' => 'View Details',
                'button_link' => '[[gift_redeem_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[redeemer_name]]","[[redeemer_account_no]]","[[amount]]","[[gift_code]]","[[redeemed_at]]","[[gift_redeem_link]]","[[site_title]]","[[currency]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'gift', // Lucide icon for gift
                'sms_body' => 'Gift redeemed by [[redeemer_name]]. Amount: [[amount]] [[currency]]. Gift Code: [[gift_code]].',
                'notification_body' => 'Gift redeemed by [[redeemer_name]]. Amount: [[amount]] [[currency]]. Gift Code: [[gift_code]].',
            ],
            [
                'name' => 'Money Received',
                'code' => 'user_receive_money',
                'for' => 'User',
                'banner' => 'global/images/receive_money.jpg',
                'title' => 'Money Received Successfully',
                'subject' => 'You have received money from [[sender_name]]',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'You have received a money transfer.<br /><br />
        Amount: [[amount]] [[currency]]<br />
        Sender Name: [[sender_name]]<br />
        Sender Account No: [[sender_account_no]]<br /><br />
        The funds have been successfully credited to your account.',
                'button_level' => 'View Transaction',
                'button_link' => '[[transaction_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[amount]]","[[currency]]","[[sender_name]]","[[sender_account_no]]","[[transaction_link]]","[[site_title]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'arrow-down', // Lucide icon for receiving money
                'sms_body' => 'You have received [[amount]] [[currency]] from [[sender_name]]. Check your account.',
                'notification_body' => 'You have received [[amount]] [[currency]] from [[sender_name]].',
            ],

            [
                'name' => 'Referral Joining',
                'code' => 'user_referral_join',
                'for' => 'User',
                'banner' => 'global/images/referral_join.jpg',
                'title' => 'Referral Joining',
                'subject' => 'Your referral [[referred_name]] has successfully joined',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'You have received a referral bonus.<br /><br />Referred Name: [[referred_name]]<br />Referred Account No: [[referred_account_no]]<br />Joined At: [[joined_at]]',
                'button_level' => 'View Referral',
                'button_link' => '[[referral_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[referred_name]]","[[referred_account_no]]","[[joined_at]]","[[referral_link]]","[[site_title]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'users', // Lucide icon for referral
                'sms_body' => 'A new referral, [[referred_name]], has joined. Joined at: [[joined_at]].',
                'notification_body' => 'Your referral [[referred_name]] has successfully joined. Joined at: [[joined_at]].',
            ],
            [
                'name' => 'Ticket Reply',
                'code' => 'user_ticket_reply',
                'for' => 'User',
                'banner' => 'global/images/ticket_reply.jpg',
                'title' => 'New Ticket Reply',
                'subject' => 'Reply received for Ticket: [[title]]',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'A new reply has been received on your support ticket "<b>[[title]]</b>".<br /><br />Message: [[message]].<br /><br />Click the button below to view the reply.',
                'button_level' => 'View Ticket',
                'button_link' => '[[reply_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[title]]","[[message]]","[[reply_link]]","[[site_title]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'message-circle', // Lucide icon for ticket reply
                'sms_body' => 'New reply received for ticket: [[title]]. Message: [[message]].',
                'notification_body' => 'New reply received for ticket: [[title]]. Message: [[message]].',
            ],
            [
                'name' => 'Payment',
                'code' => 'merchant_payment',
                'for' => 'Merchant',
                'banner' => 'global/images/payment.jpg',
                'title' => 'New Payment Received',
                'subject' => 'Payment Received: [[payment_id]]',
                'salutation' => 'Hi [[merchant_name]],',
                'email_body' => 'A new payment of <b>[[amount]]</b> has been received.<br /><br />
                        Wallet: [[wallet]]<br />
                        Gateway: [[gateway]]<br />
                        Charge: [[charge]]<br />
                        Total Amount: [[total_amount]]<br /><br />
                        Payment Date: [[payment_at]]<br /><br />
                        Payment ID: [[payment_id]]<br /><br />
                        Customer: [[user_name]] ([[user_account_no]])<br /><br />
                        Please verify and process accordingly.',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[merchant_name]]","[[amount]]","[[charge]]","[[total_amount]]","[[wallet]]","[[gateway]]","[[payment_at]]","[[payment_id]]","[[user_name]]","[[user_account_no]]","[[site_title]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'credit-card', // Lucide icon for payment
                'sms_body' => 'New payment of [[amount]] received via [[gateway]].',
                'notification_body' => 'New payment of [[amount]] received via [[gateway]].',
            ],
            [
                'name' => 'Ticket Reply',
                'code' => 'merchant_ticket_reply',
                'for' => 'Merchant',
                'banner' => 'global/images/ticket_reply.jpg',
                'title' => 'New Ticket Reply',
                'subject' => 'Reply received for Ticket: [[title]]',
                'salutation' => 'Hi [[merchant_name]],',
                'email_body' => 'A new reply has been received on a support ticket "<b>[[title]]</b>".<br /><br />Message: [[message]].<br /><br />Click the button below to view the reply.',
                'button_level' => 'View Ticket',
                'button_link' => '[[reply_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[merchant_name]]","[[title]]","[[message]]","[[reply_link]]","[[site_title]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'message-circle', // Lucide icon for ticket reply
                'sms_body' => 'New reply received for ticket: [[title]]. Message: [[message]].',
                'notification_body' => 'New reply received for ticket: [[title]]. Message: [[message]].',
            ],
            [
                'name' => 'Withdraw Request Approved',
                'code' => 'withdraw_approved',
                'for' => 'User',
                'banner' => 'global/images/withdraw_approved.jpg',
                'title' => 'Withdraw Request Approved',
                'subject' => 'Your Withdraw Request of [[amount]] [[currency]] has been Approved',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'We are pleased to inform you that your withdrawal request has been approved.<br /><br />
        Amount: [[amount]] [[currency]]<br />
        Charge: [[charge]] [[currency]]<br />
        Wallet: [[wallet]]<br />
        Gateway: [[gateway]]<br />
        Requested At: [[request_at]]<br />
        Total Amount: [[total_amount]] [[currency]]<br /><br />
        The funds have been successfully transferred. Thank you for using our services!',
                'button_level' => 'View Transaction',
                'button_link' => '[[transaction_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[amount]]","[[charge]]","[[wallet]]","[[gateway]]","[[request_at]]","[[total_amount]]","[[transaction_link]]","[[site_title]]","[[currency]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'check-circle', // Lucide icon for approval
                'sms_body' => 'Your withdrawal request of [[amount]] [[currency]] has been approved. Funds have been transferred.',
                'notification_body' => 'Your withdrawal request of [[amount]] [[currency]] has been approved. Funds have been transferred.',
            ],
            [
                'name' => 'Withdraw Request Rejected',
                'code' => 'withdraw_rejected',
                'for' => 'User',
                'banner' => 'global/images/withdraw_rejected.jpg',
                'title' => 'Withdraw Request Rejected',
                'subject' => 'Your Withdraw Request of [[amount]] [[currency]] has been Rejected',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'We regret to inform you that your withdrawal request has been rejected.<br /><br />
        Amount: [[amount]] [[currency]]<br />
        Charge: [[charge]] [[currency]]<br />
        Wallet: [[wallet]]<br />
        Gateway: [[gateway]]<br />
        Requested At: [[request_at]]<br />
        Total Amount: [[total_amount]] [[currency]]<br /><br />
        Reason for Rejection: [[rejection_reason]]<br /><br />',
                'button_level' => 'Contact Support',
                'button_link' => '[[support_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[amount]]","[[charge]]","[[wallet]]","[[gateway]]","[[request_at]]","[[total_amount]]","[[rejection_reason]]","[[site_title]]","[[currency]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'x-circle', // Lucide icon for rejection
                'sms_body' => 'Your withdrawal request of [[amount]] [[currency]] has been rejected. Reason: [[rejection_reason]].',
                'notification_body' => 'Your withdrawal request of [[amount]] [[currency]] has been rejected. Reason: [[rejection_reason]].',
            ],

            [
                'name' => 'Cash In Successful',
                'code' => 'user_cash_in',
                'for' => 'User',
                'banner' => 'global/images/cash_in.jpg',
                'title' => 'Cash In Successful',
                'subject' => 'You have successfully cashed in [[amount]] [[currency]]',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'Your cash-in request has been successfully processed.<br /><br />
    Amount: [[amount]] [[currency]]<br />
    Charge: [[charge]] [[currency]]<br />
    Total Amount: [[total_amount]] [[currency]]<br />
    Wallet: [[wallet]]<br />
    Agent Name: [[agent_name]]<br />
    Agent Account No: [[agent_account_no]]<br /><br />
    Click the button below to view your transaction details.',
                'button_level' => 'View Transaction',
                'button_link' => '[[transaction_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[amount]]","[[charge]]","[[total_amount]]","[[wallet]]","[[agent_name]]","[[agent_account_no]]","[[transaction_link]]","[[site_title]]","[[currency]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'arrow-down-circle', // Lucide icon for cash-in
                'sms_body' => 'Cash-in successful! Amount: [[amount]] [[currency]], Wallet: [[wallet]], Total received: [[total_amount]] [[currency]].',
                'notification_body' => 'You have successfully cashed in [[amount]] [[currency]] to [[wallet]]. Total received: [[total_amount]] [[currency]].',
            ],

            [
                'name' => 'Agent Commission Earned',
                'code' => 'agent_commission',
                'for' => 'Agent',
                'banner' => 'global/images/agent_commission.jpg',
                'title' => 'Commission Earned',
                'subject' => 'You have received a commission of [[amount]]',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'You have earned a new commission.<br /><br />
    <b>Amount:</b> [[amount]]<br />
    <b>Wallet:</b> [[wallet]]<br />
    <b>Transaction ID:</b> [[txn_id]]<br />
    <b>Commission Date:</b> [[commission_at]]<br /><br />
    Click the button below to view details.',
                'button_level' => 'View Commission',
                'button_link' => '[[transaction_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[amount]]","[[wallet]]","[[commission_at]]","[[txn_id]]","[[transaction_link]]","[[site_title]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'dollar-sign', // Lucide icon for commission
                'sms_body' => 'You have received a commission of [[amount]]. Wallet: [[wallet]], Transaction ID: [[txn_id]].',
                'notification_body' => 'You earned a commission of [[amount]]. Wallet: [[wallet]], Transaction ID: [[txn_id]].',
            ],
            [
                'name' => 'Ticket Reply',
                'code' => 'agent_ticket_reply',
                'for' => 'Agent',
                'banner' => 'global/images/ticket_reply.jpg',
                'title' => 'New Ticket Reply',
                'subject' => 'Reply received for Ticket: [[title]]',
                'salutation' => 'Hi [[agent_name]],',
                'email_body' => 'A new reply has been received on a support ticket "<b>[[title]]</b>".<br /><br />
    Message: [[message]].<br /><br />
    Click the button below to view the reply.',
                'button_level' => 'View Ticket',
                'button_link' => '[[reply_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[agent_name]]","[[title]]","[[message]]","[[reply_link]]","[[site_title]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'message-circle', // Lucide icon for ticket reply
                'sms_body' => 'New reply received for ticket: [[title]]. Message: [[message]].',
                'notification_body' => 'New reply received for ticket: [[title]]. Message: [[message]].',
            ],
            [
                'name' => 'KYC Request',
                'code' => 'admin_kyc_request',
                'for' => 'Admin',
                'banner' => 'global/images/kyc_request.jpg',
                'title' => 'New KYC Request from [[full_name]]',
                'subject' => 'KYC request received from [[full_name]]',
                'salutation' => 'Hello Admin,',
                'email_body' => 'A new KYC verification request has been submitted.<br /><br />
    <b>Full Name:</b> [[full_name]]<br />
    <b>Email:</b> [[email]]<br />
    <b>KYC Type:</b> [[kyc_type]]<br /><br />
    Click the button below to review the request.',
                'button_level' => 'Review KYC',
                'button_link' => '[[kyc_review_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[email]]","[[kyc_type]]","[[kyc_review_link]]","[[site_title]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'check-circle', // Lucide icon for KYC request
                'sms_body' => 'New KYC request received from [[full_name]]. Review now.',
                'notification_body' => 'New KYC request received from [[full_name]]. Click to review.',
            ],

            [
                'name' => 'KYC Action',
                'code' => 'kyc_action',
                'for' => 'User',
                'banner' => 'global/images/kyc_action.jpg',
                'title' => 'Your KYC request is [[status]]',
                'subject' => 'Your KYC request status update',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'Your KYC verification request has been [[status]].<br /><br />
    If you have any questions or need further assistance, please contact support.<br /><br />
    Click the button below to view your KYC status.',
                'button_level' => 'View KYC Status',
                'button_link' => '[[kyc_status_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[status]]","[[kyc_status_link]]","[[site_title]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'check-circle', // Lucide icon for KYC status update
                'sms_body' => 'Your KYC request is [[status]].',
                'notification_body' => 'Your KYC request has been [[status]]. Click to view.',
            ],
            [
                'name' => 'Forgot Password',
                'code' => 'forgot_password',
                'for' => 'User',
                'banner' => 'global/images/forgot_password.jpg',
                'title' => 'Reset Your Password',
                'subject' => 'Reset Your Password - [[site_title]]',
                'salutation' => 'Hello,',
                'email_body' => 'We received a request to reset your password.<br /><br />
    To reset your password, please click the button below or use the link provided.<br /><br />
    If you didn’t request this, you can safely ignore this email.<br /><br />
    Link: <a href="[[token]]">[[token]]</a><br /><br />
    Visit our site for more info: <a href="[[site_url]]">[[site_url]]</a>',
                'button_level' => 'Reset Password',
                'button_link' => '[[token]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[token]]","[[site_title]]","[[site_url]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'key-round',
                'sms_body' => 'Reset your password here: [[token]] - [[site_title]]',
                'notification_body' => 'A password reset was requested. Click the link to proceed: [[token]]',
            ],
            [
                'name' => 'Email Verification',
                'code' => 'email_verification',
                'for' => 'User',
                'banner' => null,
                'title' => 'Verify Email Address',
                'subject' => 'Verify Email Address - [[site_title]]',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'Hello!<br /><br />
        Please click the button below to verify your email address.<br /><br />
        If you didn’t request this, you can safely ignore this email.<br /><br />
        <a href="[[token]]">Verify Email Address</a><br /><br />
        Visit our site for more info: <a href="[[site_url]]">[[site_url]]</a>',
                'button_level' => 'Verify Email Address',
                'button_link' => '[[token]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[token]]","[[full_name]]","[[site_title]]","[[site_url]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'check-circle',
                'sms_body' => 'Please verify your email address here: [[token]] - [[site_title]]',
                'notification_body' => 'Please verify your email address. Click the link to proceed: [[token]]',
            ],
            [
                'name' => 'Contact Mail Send',
                'code' => 'contact_mail',
                'for' => 'Admin',
                'banner' => null,
                'title' => 'Welcome to [[site_title]]',
                'subject' => '[[subject]] for [[full_name]]',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'Thanks for joining our platform! --- [[site_title]]<br /><br />
[[message]]<br />
[[full_name]]<br />
[[email]]<br /><br />
As a member of our platform, you can mange your account, buy or sell cryptocurrency, invest and earn profits.<br /><br />
Find out more about in - [[site_url]]',
                'button_level' => 'Login Your Account',
                'button_link' => null,
                'footer_status' => 1,
                'footer_body' => 'Thanks for joining our platform! --- [[site_title]]<br /><br />
[[message]]<br /><br /><br />
As a member of our platform, you can mange your account, buy or sell cryptocurrency, invest and earn profits.<br /><br />
Find out more about in - [[site_url]]',
                'short_codes' => '["[[site_url]]","[[site_title]]","[[full_name]]","[[email]]","[[subject]]","[[message]]"]',
                'notification_status' => 0,
                'email_status' => 1,
                'sms_status' => 0,
                'icon' => 'mail',
                'sms_body' => null,
                'notification_body' => null,
            ],
            [
                'name' => 'App Forgot Password OTP',
                'code' => 'forgot_password_otp',
                'for' => 'User',
                'banner' => 'global/images/forgot_password.jpg',
                'title' => 'Reset Your Password',
                'subject' => 'Reset Your Password - [[site_title]]',
                'salutation' => 'Hello,',
                'email_body' => 'We received a request to reset your password.<br /><br />
        To reset your password, please click the button below or use the link provided.<br /><br />
        If you didn’t request this, you can safely ignore this email.<br /><br />
        Link: <a href="[[token]]">[[token]]</a><br /><br />
        Visit our site for more info: <a href="[[site_url]]">[[site_url]]</a>',
                'button_level' => 'Reset Password',
                'button_link' => null,
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[token]]","[[site_title]]","[[site_url]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'key-round',
                'sms_body' => 'Reset your password here: [[token]] - [[site_title]]',
                'notification_body' => 'A password reset was requested. Click the link to proceed: [[token]]',
            ],
            [
                'name' => 'Email Verification',
                'code' => 'app_email_verification',
                'for' => 'User',
                'banner' => null,
                'title' => 'Verify Email Address',
                'subject' => 'Verify Email Address - [[site_title]]',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'Hello!<br /><br />
        Please use the otp below to verify your email address.<br /><br />
        If you didn’t request this, you can safely ignore this email.<br /><br />
        Use the given otp to verify your email address. <br>Your otp is <b>[[token]]</b><br /><br />
        Visit our site for more info: <a href="[[site_url]]">[[site_url]]</a>',
                'button_level' => null,
                'button_link' => null,
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[token]]","[[full_name]]","[[site_title]]","[[site_url]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'check-circle',
                'sms_body' => 'Please verify your email address here: OTP: [[token]] - [[site_title]]',
                'notification_body' => 'Please verify your email address. Click the link to proceed: [[token]]',
            ],
            [
                'name' => 'Ticket Closed',
                'code' => 'user_ticket_closed',
                'for' => 'User',
                'banner' => 'global/images/ticket_closed.jpg',
                'title' => 'Ticket Closed',
                'subject' => 'Your Ticket: [[title]] Has Been Closed',
                'salutation' => 'Hi [[user_name]],',
                'email_body' => 'Your support ticket "<b>[[title]]</b>" has been closed.<br /><br />
        Click the button below to view the ticket details.',
                'button_level' => 'View Ticket',
                'button_link' => '[[ticket_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[title]]","[[ticket_link]]","[[site_title]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'check-circle',
                'sms_body' => 'Your ticket: [[title]] has been closed.',
                'notification_body' => 'Your ticket: [[title]] has been closed.',
            ],
            [
                'name' => 'New Ticket Created',
                'code' => 'admin_new_ticket',
                'for' => 'Admin',
                'banner' => 'global/images/new_ticket.jpg',
                'title' => 'New Ticket Created',
                'subject' => 'New Ticket: [[title]]',
                'salutation' => 'Hello Admin,',
                'email_body' => 'A new support ticket has been created.<br /><br />
        Ticket Title: [[title]]<br />
        Message: [[message]]<br /><br />
        Click the button below to view and respond.',
                'button_level' => 'View Ticket',
                'button_link' => '[[ticket_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[title]]","[[message]]","[[ticket_link]]","[[site_title]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'ticket',
                'sms_body' => 'New ticket created: [[title]]. Message: [[message]].',
                'notification_body' => 'New ticket created: [[title]]. Message: [[message]].',
            ],
            [
                'name' => 'Request Money Accepted',
                'code' => 'user_request_money_accepted',
                'for' => 'User',
                'banner' => 'global/images/request_money_accepted.jpg',
                'title' => 'Request Money Accepted',
                'subject' => 'Your Money Request Has Been Accepted - [[site_title]]',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'Good news! Your money request of <b>[[amount]] [[currency]]</b> has been accepted.<br /><br />
    The amount has been successfully added to your wallet.<br /><br />
    Transaction ID: <b>[[trx_id]]</b><br />
    Date: <b>[[date]]</b><br /><br />
    You can view full transaction details in your dashboard.<br /><br />
    Visit our site: <a href="[[site_url]]">[[site_url]]</a>',
                'button_level' => 'View Transaction',
                'button_link' => '[[transaction_link]]',
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[amount]]","[[currency]]","[[trx_id]]","[[date]]","[[site_title]]","[[site_url]]","[[sender_name]]","[[sender_note]]","[[sender_wallet]]","[[sender_account_no]]","[[request_money_link]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'check-circle',
                'sms_body' => 'Your request for [[amount]] [[currency]] has been accepted. [[site_title]]',
                'notification_body' => 'Your money request of [[amount]] [[currency]] has been accepted successfully.',
            ],
            [
                'name' => 'Agent Cashout Received',
                'code' => 'agent_cashout_received',
                'for' => 'Agent',
                'banner' => 'global/images/agent_cashout.jpg',
                'title' => 'Cashout Request Received',
                'subject' => 'Cash Received from [[user_name]] - [[site_title]]',
                'salutation' => 'Hi [[full_name]],',
                'email_body' => 'You have received a cash from a user.<br /><br />
    <b>User Name:</b> [[user_name]]<br />
    <b>User Account No:</b> [[user_account_no]]<br />
    <b>Amount:</b> [[amount]] [[currency]]<br />
    <b>Charge:</b> [[charge]] [[currency]]<br />
    <b>Total Amount:</b> [[total_amount]] [[currency]]<br />
    <b>Wallet:</b> [[wallet]]<br />
    <b>Transaction ID:</b> [[txn_id]]<br />
    <b>Date:</b> [[date]]<br /><br />
    Please provide the cash to the user.',
                'button_level' => null,
                'button_link' => null,
                'footer_status' => 1,
                'footer_body' => 'Regards,<br />[[site_title]]',
                'short_codes' => '["[[full_name]]","[[user_name]]","[[user_account_no]]","[[amount]]","[[currency]]","[[charge]]","[[total_amount]]","[[wallet]]","[[txn_id]]","[[date]]","[[site_title]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'dollar-sign',
                'sms_body' => 'Cash received from [[user_name]] for [[amount]] [[currency]]. Total: [[total_amount]] [[currency]].',
                'notification_body' => 'Cash received from [[user_name]] for [[amount]] [[currency]].',
            ],
            [
                'name' => 'Bill Pay',
                'code' => 'bill_pay',
                'for' => 'Admin',
                'banner' => null,
                'title' => '[[user_name]] \'s "[[service_name]]" Pay bill completed.',
                'subject' => '[[user_name]] \'s "[[service_name]]" Pay bill completed.',
                'salutation' => 'Hello Admin,',
                'email_body' => '[[user_name]] \'s "[[service_name]]" Pay bill completed.<br /><br />Amount: [[amount]]<br />Charge: [[charge]]',
                'button_level' => null,
                'button_link' => null,
                'footer_status' => 1,
                'footer_body' => 'Regards,',
                'short_codes' => '["[[user_name]]","[[service_name]]","[[amount]]","[[charge]]"]',
                'notification_status' => 1,
                'email_status' => 1,
                'sms_status' => 1,
                'icon' => 'credit-card',
                'sms_body' => '[[user_name]] \'s "[[service_name]]" Pay bill completed.',
                'notification_body' => '[[user_name]] \'s "[[service_name]]" Pay bill completed.',
            ],
        ];

        Template::truncate();
        foreach ($data as $template) {
            Template::create($template);
        }
    }
}
