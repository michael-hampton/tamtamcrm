<tr>
    <td style="background-color: <?= isset($template) && $template === 'dark' ? '#343a40' : '#ffffff' ?>; text-align: left; padding: 20px 30px 40px 30px; color: <?= isset($template) && $template === 'dark' ? '#ffffff' : '#343a40' ?>; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
        {{ $content }}
    </td>
</tr>