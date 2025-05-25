<x-mail::message>
# Deposit Notification

A new bank deposit has been submitted by a user. Please review the details below:

---

**Depositor Name:** {{ $user->fullname }}

**Email:** {{ $user->email }}

**Phone Number:** {{ $user->phone }}

**Amount Deposited - Crypto:** ₦{{ number_format($transaction->type_amount, 2) }}

**Amount Deposited - USD:** ₦{{ number_format($transaction->amount, 2) }}


**Bank Name:** {{ $transaction->name }}

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
