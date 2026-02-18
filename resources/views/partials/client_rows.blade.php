@forelse($clients as $client)
<tr>
    <td style="width: 50px;">
        <img src="{{ config('constants.assets_url') }}/images/img.svg" alt="" style="width: 40px; height: 40px; border-radius: 8px;">
    </td>

    <td class="client-details">
        <h5 style="margin: 0; font-weight: 600; color: #fff;">{{ $client->name }}</h5>
        <small style="color: #6c757d; font-size: 12px;">{{ $client->contact_person ?? 'Dr. Unknown' }}</small>
    </td>

    <td class="client-amount" style="font-weight: 700; color: #10b981;">
        ${{ number_format($client->subscription_amount, 2) }}
    </td>

    <td class="client-state">
        <span class="badge badge-light" style="font-weight: 500; background: #f3f4f6; color: #4b5563; padding: 5px 10px; border-radius: 20px;">
            {{ $client->dentalOffice->state->name ?? 'N/A' }}
        </span>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="text-center" style="padding: 20px; color: #9ca3af;">
        No clients found for this period.
    </td>
</tr>
@endforelse