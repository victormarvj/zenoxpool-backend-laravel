<x-mail::message>
# Transfer Notification

A new crypto tranfer has been submitted by a user. Please review the details below:

---

**Tranferer Name:** {{ $user->fullname }}

**Email:** {{ $user->email }}

**Phone Number:** {{ $user->phone }}

**Amount - Crypto:** {{ number_format($transaction->type_amount, 2) }} {{ strtoupper($transaction->type_name) }}

**Value - USD:** {{ number_format($transaction->amount, 2) }} USD

**Crypto Name:** {{ explode('/', $transaction->name)[0] }}

**Crypto Address:** {{ $transaction->address }}

**Date of Transfer:** {{ $transaction->created_at }}

**Transaction Reference:** {{ $transaction->transaction_id }}

---

If you believe this transfer is suspicious or needs further verification, please take immediate action.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
