<?php

namespace App\Enums;

enum TxnType: string
{
    case Credit = 'Credit';
    case Debit = 'Debit';
    case Deposit = 'Deposit';
    case ManualDeposit = 'Manual Deposit';
    case Withdraw = 'Withdraw';
    case SendMoney = 'Send Money';
    case Referral = 'Referral';
    case WithdrawAuto = 'Withdraw Auto';
    case ReceiveMoney = 'Receive Money';
    case Refund = 'Refund';
    case Exchange = 'Exchange';
    case SignupBonus = 'Signup Bonus';
    case Payment = 'Payment';
    case PaymentLink = 'Payment Link';
    case GiftRedeemed = 'Gift Redeemed';
    case GiftCode = 'Gift Code';
    case GiftCardOrder = 'Gift Card Order';
    case CashIn = 'Cash In';
    case CashOut = 'Cash Out';
    case CashReceived = 'Cash Received';
    case RequestMoney = 'Request Money';
    case Invoice = 'Invoice';
    case CashoutCommission = 'Cashout Commission';
    case CashInCommission = 'Cash In Commission';
    case PayBill = 'Pay Bill';
    case CardCreate = 'Card Create';
    case CardTopup = 'Card Topup';
}
