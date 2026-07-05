<div class="site-card mb-0">
    <div class="site-card-header">
        <h3 class="title-small">{{ __('Account Informations') }}</h3>
    </div>
    <div class="site-card-body">
        <div class="row">
            <form action="{{ route('admin.user.status-update', $user->id) }}" method="post">
                @csrf
                <div class="col-xl-12">
                    <div class="profile-card-single">
                        <h5 class="heading">{{ __('Account Status') }}</h5>
                        <div class="switch-field">
                            <input type="radio" id="accSta1" name="status" value="1"
                                @if ($user->status) checked @endif />
                            <label for="accSta1">{{ __('Active') }}</label>
                            <input type="radio" id="accSta2" name="status" value="0"
                                @if (!$user->status) checked @endif />
                            <label for="accSta2">{{ __('Disabled') }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12">
                    <div class="profile-card-single">
                        <h5 class="heading">{{ __('Email Verification') }}</h5>
                        <div class="switch-field">
                            <input type="radio" id="emaSta1" name="email_verified" value="1"
                                @if ($user->email_verified_at != null) checked @endif />
                            <label for="emaSta1">{{ __('Verified') }}</label>
                            <input type="radio" id="emaSta2" name="email_verified" value="0"
                                @if ($user->email_verified_at == null) checked @endif />
                            <label for="emaSta2">{{ __('Unverified') }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12">
                    <div class="profile-card-single">
                        <h5 class="heading">{{ __('2FA Verification') }}</h5>
                        <div class="switch-field">
                            <input type="radio" id="2fa1" name="two_fa" value="1"
                                @if ($user->two_fa) checked @endif />
                            <label for="2fa1">{{ __('Active') }}</label>
                            <input type="radio" id="2fa2" name="two_fa" value="0"
                                @if (!$user->two_fa) checked @endif />
                            <label for="2fa2">{{ __('Disabled') }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12">
                    <div class="profile-card-single">
                        <h5 class="heading">{{ __('KYC Verification') }}</h5>
                        <div class="switch-field">
                            <input type="radio" id="kyc1" name="kyc" value="1"
                                @if ($user->kyc == 1) checked @endif />
                            <label for="kyc1">{{ __('Verified') }}</label>
                            <input type="radio" id="kyc2" name="kyc" value="0"
                                @if ($user->kyc != 1) checked @endif />
                            <label for="kyc2">{{ __('Unverified') }}</label>
                        </div>
                    </div>
                </div>

                {{-- withdraw --}}
                <div class="col-xl-12">
                    <div class="profile-card-single">
                        <h5 class="heading">{{ __('Withdraw Status') }}</h5>
                        <div class="switch-field">
                            <input type="radio" id="wid1" name="withdraw_status" value="1"
                                @if ($user->withdraw_status) checked @endif />
                            <label for="wid1">{{ __('Active') }}</label>
                            <input type="radio" id="wid2" name="withdraw_status" value="0"
                                @if (!$user->withdraw_status) checked @endif />
                            <label for="wid2">{{ __('Disabled') }}</label>
                        </div>
                    </div>
                </div>
                {{-- user_deposit --}}
                <div class="col-xl-12">
                    <div class="profile-card-single">
                        <h5 class="heading">{{ __('Deposit Status') }}</h5>
                        <div class="switch-field">
                            <input type="radio" id="dep1" name="deposit_status" value="1"
                                @if ($user->deposit_status) checked @endif />
                            <label for="dep1">{{ __('Active') }}</label>
                            <input type="radio" id="dep2" name="deposit_status" value="0"
                                @if (!$user->deposit_status) checked @endif />
                            <label for="dep2">{{ __('Disabled') }}</label>
                        </div>
                    </div>
                </div>
                {{-- payment_status as cash in --}}
                <div class="col-xl-12">
                    <div class="profile-card-single">
                        <h5 class="heading">{{ __('Cash In Status') }}</h5>
                        <div class="switch-field">
                            <input type="radio" id="payment_status_enabled" name="payment_status" value="1"
                                @if ($user->payment_status) checked @endif />
                            <label for="payment_status_enabled">{{ __('Active') }}</label>
                            <input type="radio" id="payment_status_disabled" name="payment_status" value="0"
                                @if (!$user->payment_status) checked @endif />
                            <label for="payment_status_disabled">{{ __('Disabled') }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="site-btn-sm primary-btn w-100 centered">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
