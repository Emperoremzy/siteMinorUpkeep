<x-mail::message>
# Monitor {{ ucfirst($currentStatus) }}

The monitored site changed from **{{ $previousStatus }}** to **{{ $currentStatus }}**.

<x-mail::panel>
**URL:** {{ $monitor->url }}

**Current status:** {{ $currentStatus }}

**Last checked:** {{ $monitor->last_checked_at?->toDayDateTimeString() ?? 'Not checked yet' }}

**Uptime:** {{ $monitor->uptime_percentage !== null ? number_format($monitor->uptime_percentage, 2).'%' : 'Not available' }}
</x-mail::panel>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
