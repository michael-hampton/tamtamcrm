<tr>
    <td
        style="margin-top: 25px; background-color: <?= isset($template) && $template === 'dark' ? '#343a40' : '#ffffff' ?>; text-align: left; padding: 0px 30px 40px 30px; border-radius: 0px 0px 4px 4px; color: <?= isset($template) && $template === 'dark' ? '#ffffff' : '#343a40' ?>; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">

        @isset($url)
            <a href="{{ $url }}" class="text-primary">
                @isset($url_text)
                    {!! $url_text !!}
                @else
                    {{ $url }}
                @endisset
            </a>

        @endisset
    </td>
</tr>
