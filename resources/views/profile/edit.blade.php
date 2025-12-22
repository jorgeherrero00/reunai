@extends('layouts.app')
@section('content')
<style>
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse at top left, rgba(0,0,0,0.6), transparent),
                        linear-gradient(135deg, #110f17 0%, #0b0b0f 100%);
            z-index: -1;
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-black shadow sm:rounded-lg">
                <div class="">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-black shadow sm:rounded-lg">
                <div class="">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-black shadow sm:rounded-lg">
                <div class="">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

@endsection