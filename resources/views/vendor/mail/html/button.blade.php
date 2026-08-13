@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
@php
    // bgcolor is the actual, primary color source here (an HTML attribute,
    // not CSS) — see the comment on .button-cell in themes/default.css for
    // why the button's color moved off the <a> tag entirely onto this <td>.
    $bgColors = [
        'primary' => '#2563eb',
        'blue' => '#2563eb',
        'success' => '#16a34a',
        'green' => '#16a34a',
        'error' => '#dc2626',
        'red' => '#dc2626',
    ];
    $bgColor = $bgColors[$color] ?? $bgColors['primary'];
@endphp
<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="button-cell button-cell-{{ $color }}" bgcolor="{{ $bgColor }}">
<a href="{{ $url }}" class="button-link" target="_blank" rel="noopener">{!! $slot !!}</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
