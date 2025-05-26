<x-mail::message>
# Deposit Notification

A new bank deposit has been submitted by a user. Please review the details below:

---

**Depositor Name:** {{ $user->fullname }}

**Email:** {{ $user->email }}

**Phone Number:** {{ $user->phone }}

**Amount - Crypto:** {{ number_format($transaction->type_amount, 2) }} {{ strtoupper($transaction->type_name) }}

**Value - USD:** {{ number_format($transaction->amount, 2) }} USD

**Bank Name:** {{ explode('/', $transaction->name)[0] }}

**Account Number:** {{ $transaction->address }}

**Date of Deposit:** {{ $transaction->created_at }}

**Transaction Reference:** {{ $transaction->transaction_id }}

---

{{-- <x-mail::button :url="$reviewUrl">
Review Deposit
</x-mail::button> --}}

If you believe this deposit is suspicious or needs further verification, please take immediate action.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
