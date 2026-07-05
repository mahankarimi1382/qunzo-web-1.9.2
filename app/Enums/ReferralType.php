<?php

namespace App\Enums;

enum ReferralType: string
{
    case Deposit = 'deposit';
    case Transfer = 'transfer';
    case CashOut = 'cash_out';
    case RequestMoney = 'request_money';
    case Payment = 'payment';
    case InvoicePay = 'invoice_pay';
    case CreateGift = 'create_gift';
    case Withdraw = 'withdraw';
    case Exchange = 'exchange';
}
