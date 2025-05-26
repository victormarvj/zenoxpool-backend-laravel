<x-mail::message>
# Transaction Details

Dear {{ $user->fullname }},

Your {{ $transaction->name }} transaction has been successfully processed.

**Transaction Details:**
- Transaction ID: `{{ $transaction->transaction_id }}`
- Type: Crypto
- Amount: {{ number_format($transaction->type_amount, 2) }} {{ strtoupper($transaction->type_name) }}
- Value: {{ number_format($transaction->amount, 2) }} USD
- Status: <span style="color: {{ $transaction->status === 1 ? '#22c55e' : '#f59e0b' }}">{{ ucfirst($transaction->status) }}</span>
- Date: {{ $transaction->created_at->format('M j, Y g:i A') }}

{{-- @if($transaction->address)
**Delivery Address:**
{{ $transaction->address }}
@endif --}}

{{-- <x-mail::button :url="route('transaction.show', $transaction->id)">
View Transaction Details
</x-mail::button> --}}

{{-- **Next Steps:**
- You will receive a separate notification when the transaction is finalized
- Expected completion within 3-5 business days
- Contact support if status doesn't update within timeframe --}}

@if($transaction->status === 2)
<x-mail::panel>
## ❗ Action Required
Your transaction could not be completed. Please contact support immediately or initiate a new transaction.
</x-mail::panel>
@elseif($transaction->status === 0)
<x-mail::panel>
## ⏳ Processing
Your transaction is being verified. You'll receive another notification when completed.
</x-mail::panel>
@else
## ✅ Confirmed
Your transaction has been verified. Thank you for choosing us.
</x-mail::panel>
@endif

For any questions about this transaction, please contact our support team:
📧 support@zenoxpool.com

Thank you for choosing {{ config('app.name') }}!

Sincerely,
{{ config('app.name') }} Team

<x-mail::subcopy>
This transaction was initiated by {{ $user->username }} ({{ $user->email }}).
IP Address: {{ request()->ip() }} • {{ $transaction->created_at->format('Y-m-d H:i:s T') }}
For security reasons, never share your transaction details with anyone.
© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
</x-mail::subcopy>
</x-mail::message>
