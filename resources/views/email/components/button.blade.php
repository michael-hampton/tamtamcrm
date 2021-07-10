<tr>
    <td bgcolor="<?= isset($template) && $template === 'dark' ? '#343a40' : '#ffffff' ?>" align="left">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td bgcolor="<?= isset($template) && $template === 'dark' ? '#343a40' : '#ffffff' ?>" align="center"
                    style="padding: 20px 30px 60px 30px;">
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td align="center" style="border-radius: 3px;"
                                bgcolor="<?= isset($template) && $template === 'dark' ? '#343a40' : '#ffffff' ?>"><a
                                        href="{{ $url }}"
                                        target="_blank"
                                        style="font-size: 20px; color: #fff; background-color: #007bff; border-color: #007bff; display: inline-block; font-weight: 400; text-align: center; white-space: nowrap; vertical-align: middle; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; border: 1px solid transparent; padding: .375rem .75rem; font-size: 1rem; line-height: 1.5; border-radius: .25rem;">{{ $slot }}</a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
