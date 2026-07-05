@extends('backend.layouts.app')
@section('title')
    {{ __('P2P Order Chat') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('Order Chat') }} #{{ $order->id }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div class="site-card overflow-hidden">
                        <div class="site-card-header">
                            <h3 class="title">{{ __('Messages') }}</h3>
                            <div class="card-header-links">
                                <a href="{{ route('admin.p2p.orders.show', $order->id) }}"
                                    class="card-header-link">{{ __('Order Details') }}</a>
                            </div>
                        </div>
                        <div class="site-card-body">
                            @forelse($messages as $message)
                                <div
                                    class="support-ticket-single-message {{ $message->sender_type->value === 'admin' ? 'admin' : 'user' }}">
                                    <div class="logo">
                                        <span class="avatar-text">
                                            {{ strtoupper(substr($message->sender()?->name ?? $message->sender()?->username ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="message-body">
                                        <div class="article">
                                            {{ $message->message ?: '-' }}
                                        </div>
                                    </div>
                                    <div class="message-footer">
                                        <div class="name">
                                            {{ $message->sender_type->value === 'admin' ? ($message->sender()?->name ?? 'Admin') : ($message->sender()?->username ?? 'User') }}
                                        </div>
                                        <div class="email">{{ $message->created_at->format('Y-m-d H:i:s') }}</div>
                                    </div>
                                    @if ($message->attachment_path)
                                        <div class="message-attachments">
                                            <div class="title">{{ __('Attachments') }}</div>
                                            <div class="single-attachment">
                                                <div class="attach">
                                                    <a href="{{ asset($message->attachment_path) }}" target="_blank">
                                                        <i class="anticon anticon-picture"></i>{{ $message->attachment_name ?: getBasename($message->attachment_path) }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="mb-0">{{ __('No messages found.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="site-card">
                        <div class="site-card-body">
                            <form action="{{ route('admin.p2p.orders.messages.store', $order->id) }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-3">
                                        <div class="wrap-custom-file">
                                            <input type="file" name="attachment" id="order-chat-attachment"
                                                accept=".jpg,.jpeg,.png,.gif,.svg,.webp,.pdf,.doc,.docx" />
                                            <label for="order-chat-attachment">
                                                <img class="upload-icon"
                                                    src="{{ asset('global/materials/upload.svg') }}" alt="" />
                                                <span>{{ __('Attach File') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="site-input-groups">
                                            <label>{{ __('Message') }}</label>
                                            <textarea class="form-textarea" name="message" rows="3">{{ old('message') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="site-btn blue-btn">{{ __('Send Message') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
