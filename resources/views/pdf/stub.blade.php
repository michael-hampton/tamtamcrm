<!DOCTYPE html>
<html lang="{!! $lang !!}">


<title>{{ $entity->number }}</title>
<link href="{{ public_path() }}/css/pdf.css" rel="stylesheet">
<style>
html {
font-size: {{ $settings->font_size }}px;
}
</style>
	<body>
		{!! $header !!}
		{!! $body !!}
		{!! $footer !!}
	</body>
</html>
