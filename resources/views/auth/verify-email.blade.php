@extends('layouts.MainLayout')

@section('title', 'Verify Email - SmartShamba')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">
                    Verify Your Email Address
                </h1>
            </div>

            <div class="p-6">
                <div class="text-center">
                    <div class="mb-6">
                        <i class="fas fa-envelope-open-text fa-3x text-emerald-500 mb-4"></i>
                        <h2 class="text-xl font-semibold text-gray-800">
                            Please check your email
                        </h2>
                    </div>

                    <p class="text-gray-600 mb-6">
                        We've sent a verification link to <strong>{{ auth()->user()->email }}</strong>.
                        Please click the link in the email to verify your address.
                    </p>

                    @if (session('resent'))
                        <div class="mb-4 p-3 bg-blue-50 text-blue-800 text-sm rounded">
                            {{ session('resent') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit"
                                class="btn btn-outline text-sm">
                            Resend Verification Email
                        </button>
                    </form>

                    <p class="mt-4 text-sm text-gray-500">
                        Didn't receive the email? <a href="#" class="text-emerald-600 hover:text-emerald-800"
                                                   onclick="event.preventDefault(); this.closest('form').requestSubmit();">
                            Click to resend
                        </a>.
                    </p>

                    <p class="mt-2 text-sm text-gray-500">
                        If you didn't receive the email, check your spam folder or make sure you entered the correct email address.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection