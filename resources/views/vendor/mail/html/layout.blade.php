<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
@media only screen and (max-width: 600px) {
.inner-body {
width: 100% !important;
}

.footer {
width: 100% !important;
}
}

@media only screen and (max-width: 500px) {
.button-cell {
width: 100% !important;
}
}

/* Gmail's mobile app flags dark-mode-rendered elements with [data-ogsc] —
   this is a direct hook into Gmail's own dark-mode pass, which can override
   inline styles (even ones marked !important) outside the normal CSS
   cascade. Written here rather than in themes/default.css because
   Illuminate\Mail\Markdown inlines that stylesheet onto each element and
   discards whatever it can't inline (like this attribute selector, which
   has no matching element until Gmail adds it) — this <style> block is
   hand-written directly in the layout instead, so it survives untouched. */
[data-ogsc] .button-link,
[data-ogsc] .button-link:visited {
color: #ffffff !important;
-webkit-text-fill-color: #ffffff !important;
}

[data-ogsc] .button-cell-blue,
[data-ogsc] .button-cell-primary {
background-color: #2563eb !important;
}

[data-ogsc] .button-cell-green,
[data-ogsc] .button-cell-success {
background-color: #16a34a !important;
}

[data-ogsc] .button-cell-red,
[data-ogsc] .button-cell-error {
background-color: #dc2626 !important;
}
</style>
{!! $head ?? '' !!}
</head>
<body>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center">
<table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
{!! $header ?? '' !!}

<!-- Email Body -->
<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
<table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<!-- Body content -->
<tr>
<td class="content-cell">
{!! Illuminate\Mail\Markdown::parse($slot) !!}

{!! $subcopy ?? '' !!}
</td>
</tr>
</table>
</td>
</tr>

{!! $footer ?? '' !!}
</table>
</td>
</tr>
</table>
</body>
</html>
