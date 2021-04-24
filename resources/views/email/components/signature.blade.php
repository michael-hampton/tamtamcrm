<tr>
    <td
            style="margin-top: 25px; background-color: <?= isset($template) && $template === 'dark' ? '#343a40' : '#ffffff' ?>; text-align: center; padding: 0px 30px 0px 30px; color: <?= isset($template) && $template === 'dark' ? '#ffffff' : '#343a40' ?>; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
        <img style="width:100px;height:100px;" id="base64image"
             src="{{ $signature }}">
    </td>
</tr>