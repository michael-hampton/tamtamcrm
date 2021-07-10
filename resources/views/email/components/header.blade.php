<tr>
    <td style="background-color: <?= isset($template) && $template === 'dark' ? '#343a40' : '#ffffff' ?>; text-align: center; padding: 0px 10px 0px 10px;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
            <tr>
                <td bgcolor="<?= isset($template) && $template === 'dark' ? '#343a40' : '#ffffff' ?>" align="center"
                    valign="top"
                    style="padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: <?= isset($template) && $template === 'dark' ? '#ffffff' : '#343a40' ?>; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
                    <h1 style="font-size: 48px; font-weight: 400; margin: 2px;">{{ $slot }}</h1>
                    <img src="{{ $logo }}" width="125" height="120"
                         style="display: block; border: 0px;"/>
                </td>
            </tr>
        </table>
    </td>
</tr>
