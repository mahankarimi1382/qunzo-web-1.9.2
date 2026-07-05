<div wire:init='loadPay'>
    @if ($isLoaded && !$isSuccess && !$isCancelled)
        <div class="payment-card-box">
            <div class="payment-card-wrapper">
                @if ($isSandbox)
                    <div class="payment-status-card danger">
                        <div class="icon">
                            <svg width="27" height="27" viewBox="0 0 27 27" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M13.1251 0C20.3726 0 26.2502 5.87757 26.2502 13.1251C26.2502 20.3726 20.3726 26.2502 13.1251 26.2502C5.87757 26.2502 0 20.3726 0 13.1251C0 5.87757 5.87757 0 13.1251 0ZM11.7189 14.2545V7.49919C11.7189 6.72475 12.3507 6.09306 13.1251 6.09306C13.8995 6.09306 14.5312 6.73063 14.5312 7.49919V14.2545C14.5312 15.0231 13.8995 15.6606 13.1251 15.6606C12.3506 15.6606 11.7189 15.0289 11.7189 14.2545ZM13.119 17.1688C13.9991 17.1688 14.7128 17.8825 14.7128 18.7625C14.7128 19.6426 13.9991 20.3563 13.119 20.3563C12.239 20.3563 11.5253 19.6426 11.5253 18.7625C11.5253 17.8825 12.239 17.1688 13.119 17.1688Z"
                                    fill="#FF6670"></path>
                            </svg>
                        </div>
                        <div class="contents">
                            <p class="message">
                                {{ __('You are currently in sandbox mode. No real money is involved.') }}
                            </p>
                        </div>
                    </div>
                @endif
                <div class="payment-card">
                    <div class="payment-card-header d-flex justify-content-between align-items-center">
                        <div class="payment-card-logo">
                            <img src="{{ asset(setting('site_logo')) }}" alt="App Logo">
                        </div>
                        @if ($step !== 1)
                            <div class="currency">{{ $transaction->pay_currency }}</div>
                        @endif
                    </div>
                    <main class="payment-card-body">
                        <div class="merchant-info">
                            <div class="merchant-info-details">
                                <div class="merchant-info-logo">
                                    <img src="{{ asset('frontend/images/icons/shopping-basket-01.png') }}"
                                        alt="Payment Logo">
                                </div>
                                <div class="contents">
                                    <h6 class="merchant-info-name">{{ $transaction->user?->full_name ?? __('N/A') }}
                                    </h6>
                                    <span class="merchant-info-invoice">{{ __('Transaction ID') }}:
                                        <span class="fw-bold">{{ $transaction->tnx }}</span></span>
                                </div>
                            </div>
                            @if ($step !== 1)
                                <span
                                    class="merchant-info-amount">{{ formatAmount($transaction->final_amount, $transaction->pay_currency, true) }}</span>
                            @endif
                        </div>
                        @if (
                            $transaction->type == App\Enums\TxnType::PaymentLink &&
                                $transaction->invoice &&
                                $transaction->invoice->address != null)
                            <div class="payment-status-card info mb-3">
                                <div class="icon">
                                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M8.4375 6.875C7.7475 6.875 7.1875 6.315 7.1875 5.625V1.25C7.1875 0.56 7.7475 0 8.4375 0C9.1275 0 9.6875 0.56 9.6875 1.25V5.625C9.6875 6.315 9.1275 6.875 8.4375 6.875Z"
                                            fill="#41B1FC"></path>
                                        <path
                                            d="M14.375 6.875C13.685 6.875 13.125 6.315 13.125 5.625V1.25C13.125 0.56 13.685 0 14.375 0C15.065 0 15.625 0.56 15.625 1.25V5.625C15.625 6.315 15.065 6.875 14.375 6.875Z"
                                            fill="#41B1FC"></path>
                                        <path
                                            d="M20.3125 6.875C19.6225 6.875 19.0625 6.315 19.0625 5.625V1.25C19.0625 0.56 19.6225 0 20.3125 0C21.0025 0 21.5625 0.56 21.5625 1.25V5.625C21.5625 6.315 21.0025 6.875 20.3125 6.875Z"
                                            fill="#41B1FC"></path>
                                        <path
                                            d="M22.8125 2.8125H5.9375C4.04125 2.8125 2.5 4.35375 2.5 6.25V26.5625C2.5 28.4587 4.04125 30 5.9375 30H22.8125C24.7087 30 26.25 28.4587 26.25 26.5625V6.25C26.25 4.35375 24.7087 2.8125 22.8125 2.8125ZM8.75 10H15C15.69 10 16.25 10.56 16.25 11.25C16.25 11.94 15.69 12.5 15 12.5H8.75C8.06 12.5 7.5 11.94 7.5 11.25C7.5 10.56 8.06 10 8.75 10ZM21.25 22.5H8.75C8.06 22.5 7.5 21.94 7.5 21.25C7.5 20.56 8.06 20 8.75 20H21.25C21.94 20 22.5 20.56 22.5 21.25C22.5 21.94 21.94 22.5 21.25 22.5ZM21.25 17.5H8.75C8.06 17.5 7.5 16.94 7.5 16.25C7.5 15.56 8.06 15 8.75 15H21.25C21.94 15 22.5 15.56 22.5 16.25C22.5 16.94 21.94 17.5 21.25 17.5Z"
                                            fill="#41B1FC"></path>
                                    </svg>

                                </div>
                                <div class="contents">
                                    <p class="title">{{ __('Note:') }}</p>
                                    <p class="message">{{ $transaction->invoice->address }}</p>
                                </div>
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="payment-status-card warning mb-3">
                                @foreach ($errors->all() as $error)
                                    <div class="icon">
                                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="18" cy="18" r="18" fill="#FFB7B7"></circle>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M23.7372 12.2631C24.144 12.6699 24.144 13.3294 23.7372 13.7362L13.7372 23.7362C13.3304 24.143 12.6709 24.143 12.2641 23.7362C11.8573 23.3294 11.8573 22.6699 12.2641 22.2631L22.2641 12.2631C22.6709 11.8563 23.3304 11.8563 23.7372 12.2631Z"
                                                fill="white"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M12.2641 12.2631C12.6709 11.8563 13.3304 11.8563 13.7372 12.2631L23.7372 22.2631C24.144 22.6699 24.144 23.3294 23.7372 23.7362C23.3304 24.143 22.6709 24.143 22.2641 23.7362L12.2641 13.7362C11.8573 13.3294 11.8573 12.6699 12.2641 12.2631Z"
                                                fill="white"></path>
                                        </svg>
                                    </div>
                                    <div class="contents">
                                        <p class="title">{{ $error }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="account-info">
                            @if ($step === 1)
                                <div class="input-group-item">
                                    <label for="account-otp" class="account-info-label">
                                        {{ __('Currency') }} <span class="required">*</span></label>
                                    <div class="account-info-input-group">
                                        <select class="form-select" wire:model='wallet_id'>
                                            @foreach ($this->currencies as $currency)
                                                <option value="{{ $currency['id'] }}">
                                                    {{ $currency['name'] . ' (' . $currency['code'] . ')' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="input-group-item  mt-4">
                                    <label for="link-amount" class="account-info-label">
                                        {{ __('Amount') }} <span class="required">*</span></label>
                                    <div class="account-info-input-group">
                                        <input class="account-info-input" type="number" wire:model='link_amount'
                                            id="link-amount" placeholder="{{ __('Enter Amount') }}" autofocus />
                                    </div>
                                </div>
                            @elseif ($step === 2)
                                @if (!$isSandbox)
                                    <div class="select-wrapper">
                                        <input type="radio" wire:model="payment_type" value="wallet" id="wallets"
                                            hidden="">
                                        <label class="select-option" for="wallets">
                                            <div class="left">
                                                <span class="icon">
                                                    <img src="{{ asset('frontend') }}/images/icons/wallets.png"
                                                        alt="">
                                                </span>
                                                <span class="text">{{ __('Pay Via Qunzo') }}</span>
                                            </div>
                                            <span class="check">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="20" height="20" rx="10"
                                                        fill="#7445FF">
                                                    </rect>
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M14.5209 6.977C14.6262 7.08247 14.6854 7.22544 14.6854 7.37451C14.6854 7.52357 14.6262 7.66654 14.5209 7.772L9.27091 13.022C9.16544 13.1273 9.02247 13.1865 8.87341 13.1865C8.72434 13.1865 8.58138 13.1273 8.47591 13.022L5.47591 10.022C5.42064 9.97051 5.37631 9.90841 5.34557 9.83941C5.31483 9.77041 5.2983 9.69592 5.29696 9.6204C5.29563 9.54487 5.30952 9.46985 5.33781 9.39981C5.3661 9.32977 5.40821 9.26614 5.46163 9.21273C5.51504 9.15931 5.57867 9.1172 5.64871 9.08891C5.71875 9.06062 5.79377 9.04673 5.8693 9.04806C5.94483 9.04939 6.01931 9.06593 6.08831 9.09667C6.15731 9.12741 6.21941 9.17174 6.27091 9.22701L8.87341 11.8295L13.7259 6.977C13.8314 6.87167 13.9743 6.8125 14.1234 6.8125C14.2725 6.8125 14.4154 6.87167 14.5209 6.977Z"
                                                        fill="white"></path>
                                                </svg>
                                            </span>
                                        </label>

                                        <input type="radio" wire:model="payment_type" value="gateway"
                                            id="gateways" hidden="">
                                        <label class="select-option" for="gateways">
                                            <div class="left">
                                                <span class="icon">
                                                    <img src="{{ asset('frontend') }}/images/icons/gateway.png"
                                                        alt="">
                                                </span>
                                                <span class="text">{{ __('Gateways') }}</span>
                                            </div>
                                            <span class="check">
                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="20" height="20" rx="10"
                                                        fill="#7445FF">
                                                    </rect>
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M14.5209 6.977C14.6262 7.08247 14.6854 7.22544 14.6854 7.37451C14.6854 7.52357 14.6262 7.66654 14.5209 7.772L9.27091 13.022C9.16544 13.1273 9.02247 13.1865 8.87341 13.1865C8.72434 13.1865 8.58138 13.1273 8.47591 13.022L5.47591 10.022C5.42064 9.97051 5.37631 9.90841 5.34557 9.83941C5.31483 9.77041 5.2983 9.69592 5.29696 9.6204C5.29563 9.54487 5.30952 9.46985 5.33781 9.39981C5.3661 9.32977 5.40821 9.26614 5.46163 9.21273C5.51504 9.15931 5.57867 9.1172 5.64871 9.08891C5.71875 9.06062 5.79377 9.04673 5.8693 9.04806C5.94483 9.04939 6.01931 9.06593 6.08831 9.09667C6.15731 9.12741 6.21941 9.17174 6.27091 9.22701L8.87341 11.8295L13.7259 6.977C13.8314 6.87167 13.9743 6.8125 14.1234 6.8125C14.2725 6.8125 14.4154 6.87167 14.5209 6.977Z"
                                                        fill="white"></path>
                                                </svg>
                                            </span>
                                        </label>
                                    </div>
                                @endif

                                <div class="input-group-item" x-show="$wire.payment_type === 'wallet'">
                                    <label for="account-number"
                                        class="account-info-label">{{ __('UID') }}</label>
                                    <div class="account-info-input-group">
                                        <input class="account-info-input" type="text" wire:model='account_number'
                                            id="account-number" placeholder="{{ __('Enter UID') }}" autofocus />
                                    </div>
                                </div>
                                @if (!$isSandbox)
                                    <div class="payment-gatway-info" x-show="$wire.payment_type === 'gateway'">
                                        <div class="gateway-section">
                                            <label class="section-label">{{ __('Select Gateway') }}
                                                <span>*</span></label>
                                            @if (count($this->gateways) > 0)
                                                <div class="gateway-grid">
                                                    @foreach ($this->gateways as $index => $gateway)
                                                        <input type="radio" name="gateway"
                                                            id="gateway_{{ $gateway['id'] }}"
                                                            value="{{ $gateway['id'] }}" wire:model="gateway_id"
                                                            {{ $index === 0 ? 'checked' : '' }} hidden="">
                                                        <label class="gateway-card"
                                                            for="gateway_{{ $gateway['id'] }}">
                                                            <span class="check">
                                                                <svg width="20" height="20"
                                                                    viewBox="0 0 20 20" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <rect width="20" height="20"
                                                                        rx="10" fill="#7445FF"></rect>
                                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                                        d="M14.5209 6.977C14.6262 7.08247 14.6854 7.22544 14.6854 7.37451C14.6854 7.52357 14.6262 7.66654 14.5209 7.772L9.27091 13.022C9.16544 13.1273 9.02247 13.1865 8.87341 13.1865C8.72434 13.1865 8.58138 13.1273 8.47591 13.022L5.47591 10.022C5.42064 9.97051 5.37631 9.90841 5.34557 9.83941C5.31483 9.77041 5.2983 9.69592 5.29696 9.6204C5.29563 9.54487 5.30952 9.46985 5.33781 9.39981C5.3661 9.32977 5.40821 9.26614 5.46163 9.21273C5.51504 9.15931 5.57867 9.1172 5.64871 9.08891C5.71875 9.06062 5.79377 9.04673 5.8693 9.04806C5.94483 9.04939 6.01931 9.06593 6.08831 9.09667C6.15731 9.12741 6.21941 9.17174 6.27091 9.22701L8.87341 11.8295L13.7259 6.977C13.8314 6.87167 13.9743 6.8125 14.1234 6.8125C14.2725 6.8125 14.4154 6.87167 14.5209 6.977Z"
                                                                        fill="white"></path>
                                                                </svg>
                                                            </span>
                                                            <img src="{{ $gateway['logo'] }}"
                                                                alt="{{ $gateway['name'] }}">
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="alert alert-warning">
                                                    {{ __('No gateways available for this currency.') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @elseif($step === 3)
                                <label for="account-pass" class="account-info-label">{{ __('Password') }}</label>
                                <div class="account-info-input-group">
                                    <input class="account-info-input" type="password" wire:model='account_password'
                                        id="account-pass" placeholder="{{ __('Enter Password') }}" />
                                </div>
                            @endif
                        </div>
                    </main>
                    <div class="payment-card-footer d-flex justify-content-between flex-wrap gap-2">
                        <button type="button" class="td-btn outline-danger-btn" wire:click='cancelPayment'>
                            <span><svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_1468_8912)">
                                        <path d="M11.2495 11.25L6.75 6.75M6.75048 11.25L11.25 6.75" stroke="red"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M16.5 9C16.5 4.85786 13.1421 1.5 9 1.5C4.85786 1.5 1.5 4.85786 1.5 9C1.5 13.1421 4.85786 16.5 9 16.5C13.1421 16.5 16.5 13.1421 16.5 9Z"
                                            stroke="red" stroke-width="1.5" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_1468_8912">
                                            <rect width="18" height="18" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            {{ __('Cancel') }}
                        </button>
                        @if ($step === 1 || $step === 2)
                            <button wire:click='nextStep' wire:loading.attr='disabled' wire:loading.remove
                                wire:target='nextStep' type="button" class="td-btn bg-grad-1 gateway-btn">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24">
                                        <g fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="1.5" color="currentColor">
                                            <circle cx="12" cy="12" r="10" />
                                            <path
                                                d="M12.915 15s2.585-2.21 2.585-3s-2.585-3-2.585-3M8.5 15s2.585-2.21 2.585-3S8.5 9 8.5 9" />
                                        </g>
                                    </svg>
                                </span>
                                <span>
                                    {{ __('Next') }}
                                </span>
                            </button>
                            <button type="button" wire:loading wire:target='nextStep'
                                class="td-btn bg-grad-1 gateway-btn">
                                <span>
                                    <i class="spinner-icon fa-spin">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <path
                                                d="M304 48a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zm0 416a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zM48 304a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm464-48a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zM142.9 437A48 48 0 1 0 75 369.1 48 48 0 1 0 142.9 437zm0-294.2A48 48 0 1 0 75 75a48 48 0 1 0 67.9 67.9zM369.1 437A48 48 0 1 0 437 369.1 48 48 0 1 0 369.1 437z">
                                            </path>
                                        </svg>
                                    </i>
                                </span>
                                <span>
                                    {{ __('Processing') }}
                                </span>
                            </button>
                        @elseif($step === 3)
                            <button wire:click='payNow' wire:loading.attr='disabled' wire:loading.remove
                                wire:target='payNow' type="button" class="td-btn bg-grad-1 gateway-btn">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" wire:loading.remove wire:target='payNow'>
                                        <g fill="none" stroke="currentColor" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="1.5" color="currentColor">
                                            <path
                                                d="M17 3.338A9.95 9.95 0 0 0 12 2C6.477 2 2 6.477 2 12s4.477 10 10 10s10-4.477 10-10q-.002-1.03-.2-2" />
                                            <path d="M8 12.5s1.5 0 3.5 3.5c0 0 5.559-9.167 10.5-11" />
                                        </g>
                                    </svg>
                                    <i class="spinner-icon fa-spin" wire:loading wire:target='payNow'>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <path
                                                d="M304 48a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zm0 416a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zM48 304a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm464-48a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zM142.9 437A48 48 0 1 0 75 369.1 48 48 0 1 0 142.9 437zm0-294.2A48 48 0 1 0 75 75a48 48 0 1 0 67.9 67.9zM369.1 437A48 48 0 1 0 437 369.1 48 48 0 1 0 369.1 437z">
                                            </path>
                                        </svg>
                                    </i>
                                </span>
                                <span>
                                    {{ __('Confirm') }}
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @elseif ($isSuccess)
        <div class="payment-card-box">
            <div class="payment-card">
                <div class="payment-card-header">
                    <div class="payment-card-logo text-center">
                        <img src="{{ asset(setting('site_logo')) }}" alt="App Logo">
                    </div>
                </div>
                <div class="payment-card-body text-center">
                    <div class="payment-status-wrapper">
                        <div class="icon-wrapper">
                            <svg width="83" height="82" viewBox="0 0 83 82" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="41.625" cy="41" r="41" fill="#EBF8E8"></circle>
                                <circle cx="41.625" cy="41" r="30" fill="#C5EBBA"></circle>
                                <path opacity="0.99"
                                    d="M38.1019 48.5052C37.8665 48.5055 37.6335 48.4582 37.4161 48.366C37.1987 48.2738 37.0013 48.1386 36.8351 47.9681L31.7961 42.8119C31.4636 42.4674 31.2778 42.0023 31.2793 41.5181C31.2808 41.0339 31.4694 40.57 31.804 40.2276C32.1386 39.8852 32.5919 39.6922 33.0651 39.6907C33.5383 39.6891 33.9928 39.8792 34.3295 40.2194L38.1018 44.0795L47.9211 34.0319C48.2578 33.6917 48.7123 33.5016 49.1855 33.5032C49.6587 33.5047 50.112 33.6977 50.4466 34.0401C50.7812 34.3825 50.9698 34.8464 50.9713 35.3306C50.9728 35.8148 50.787 36.2799 50.4545 36.6244L39.3686 47.9681C39.2024 48.1386 39.005 48.2739 38.7876 48.366C38.5702 48.4582 38.3372 48.5055 38.1019 48.5052Z"
                                    fill="white"></path>
                                <path
                                    d="M41.1257 22.6667C37.5821 22.6667 34.1181 23.742 31.1717 25.7565C28.2253 27.771 25.9289 30.6342 24.5728 33.9842C23.2167 37.3342 22.8619 41.0204 23.5533 44.5767C24.2446 48.1331 25.951 51.3997 28.4567 53.9637C30.9624 56.5277 34.1548 58.2737 37.6303 58.9811C41.1058 59.6885 44.7082 59.3255 47.9821 57.9379C51.2559 56.5503 54.0541 54.2004 56.0228 51.1855C57.9915 48.1706 59.0423 44.6261 59.0423 41.0001C59.0369 36.1395 57.1475 31.4796 53.7887 28.0426C50.4298 24.6056 45.8758 22.6723 41.1257 22.6667V22.6667ZM50.4549 36.6244L39.3689 47.9682C39.2026 48.1385 39.0051 48.2735 38.7878 48.3657C38.5704 48.4579 38.3374 48.5053 38.1022 48.5053C37.8669 48.5053 37.6339 48.4579 37.4166 48.3657C37.1992 48.2735 37.0017 48.1385 36.8354 47.9682L31.7965 42.8119C31.464 42.4674 31.2782 42.0023 31.2797 41.5181C31.2811 41.034 31.4698 40.57 31.8043 40.2277C32.1389 39.8853 32.5923 39.6923 33.0655 39.6907C33.5386 39.6892 33.9932 39.8793 34.3299 40.2195L38.1022 44.0795L47.9215 34.032C48.2581 33.6918 48.7127 33.5017 49.1859 33.5032C49.659 33.5048 50.1124 33.6978 50.447 34.0402C50.7816 34.3825 50.9702 34.8465 50.9716 35.3306C50.9731 35.8148 50.7873 36.2799 50.4549 36.6244Z"
                                    fill="#53C330"></path>
                            </svg>
                        </div>

                        <h3 class="status-title">{{ __('SUCCESS') }}</h3>

                        <h4 class="message-title">{{ __('Payment has been completed.') }}</h4>
                        @if ($isRedirection)
                            <p class="info-text" x-data="redirectCountdown">
                                {!! __('Redirecting in :seconds seconds...', ['seconds' => '<span x-text="countdown"></span>']) !!}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @elseif ($isCancelled)
        <div class="payment-card-box">
            <div class="payment-card">
                <div class="payment-card-header">
                    <div class="payment-card-logo text-center">
                        <img src="{{ asset(setting('site_logo')) }}" alt="App Logo">
                    </div>
                </div>

                <div class="payment-card-body text-center">
                    <div class="payment-status-wrapper">
                        <div class="icon-wrapper">
                            <svg width="83" height="82" viewBox="0 0 83 82" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="41.5" cy="41" r="41" fill="#FFEDED" />
                                <circle cx="41.5" cy="41" r="30" fill="#FFB7B7" />
                                <path opacity="0.99"
                                    d="M37.9769 48.5052C37.7415 48.5055 37.5085 48.4582 37.2911 48.366C37.0737 48.2738 36.8763 48.1386 36.7101 47.9681L31.6711 42.8119C31.3386 42.4674 31.1528 42.0023 31.1543 41.5181C31.1558 41.0339 31.3444 40.57 31.679 40.2276C32.0136 39.8852 32.4669 39.6922 32.9401 39.6907C33.4133 39.6891 33.8678 39.8792 34.2045 40.2194L37.9768 44.0795L47.7961 34.0319C48.1328 33.6917 48.5873 33.5016 49.0605 33.5032C49.5337 33.5047 49.987 33.6977 50.3216 34.0401C50.6562 34.3825 50.8448 34.8464 50.8463 35.3306C50.8478 35.8148 50.662 36.2799 50.3295 36.6244L39.2436 47.9681C39.0774 48.1386 38.88 48.2739 38.6626 48.366C38.4452 48.4582 38.2122 48.5055 37.9769 48.5052Z"
                                    fill="white" />
                                <circle cx="41" cy="41" r="18" fill="#F21B1B" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M46.7372 35.2631C47.144 35.6699 47.144 36.3294 46.7372 36.7362L36.7372 46.7362C36.3304 47.143 35.6709 47.143 35.2641 46.7362C34.8573 46.3294 34.8573 45.6699 35.2641 45.2631L45.2641 35.2631C45.6709 34.8563 46.3304 34.8563 46.7372 35.2631Z"
                                    fill="white" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M35.2641 35.2631C35.6709 34.8563 36.3304 34.8563 36.7372 35.2631L46.7372 45.2631C47.144 45.6699 47.144 46.3294 46.7372 46.7362C46.3304 47.143 45.6709 47.143 45.2641 46.7362L35.2641 36.7362C34.8573 36.3294 34.8573 35.6699 35.2641 35.2631Z"
                                    fill="white" />
                            </svg>
                        </div>

                        <h3 class="status-title">{{ __('CANCELLED') }}</h3>

                        <h4 class="message-title"> {{ __('Your payment was not completed.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="payment-card-box">
            <div class="payment-card-wrapper">
                <div class="payment-card">
                    <header class="payment-card-header placeholder-glow">
                        <div class="placeholder col-6 rounded-1 height-40"></div>
                        <div class="currency placeholder col-4"></div>
                    </header>
                    <main class="payment-card-body placeholder-glow">
                        <div class="merchant-info">
                            <div class="merchant-info-details">
                                <div class="merchant-info-logo placeholder col-2 rounded-1 height-40"></div>
                                <div class="contents">
                                    <span class="merchant-info-invoice placeholder col-12 rounded-1">
                                        Invoice
                                    </span>
                                </div>
                            </div>
                            <span class="merchant-info-amount placeholder col-1 rounded-1"></span>
                        </div>
                        <div class="account-info">
                            <label for="account-otp" class="account-info-label placeholder col-2 rounded-1"></label>
                            <div class="account-info-input-group placeholder col-12 rounded-1 height-40"></div>
                        </div>
                    </main>
                    <footer
                        class="payment-card-footer d-flex justify-content-between flex-wrap gap-2  placeholder-glow">
                        <button type="button" class="td-btn cancel-btn gateway-btn placeholder col-4"></button>
                        <a href="" class="td-btn bg-grad-1 placeholder col-4"></a>
                    </footer>
                </div>
            </div>
        </div>
    @endif

    <script>
        "use strict";
        document.addEventListener('alpine:init', function() {
            Alpine.data('redirectCountdown', () => ({
                countdown: 5, // Initial countdown time
                init() {
                    const interval = setInterval(() => {
                        this.countdown--;

                        if (this.countdown <= 0) {
                            clearInterval(interval);
                            // Perform the redirection
                            window.location.href = '{{ $redirectUrl }}';
                        }
                    }, 1000);
                },
            }));
        });
    </script>
</div>
