<?php
echo '<pre>';
print_r($data);
echo '</pre>';

?>


<body style="background-color: #000">
<span style="font-size: 60px; color: #FFF">Loading...</span>
</body>

<script>
    const userData = {
        name: '{{ $data['name'] }}',
        id: {{ $data['id'] }},
        email: '{{ $data['email'] }}',
        account_id: {{ $data['account_id'] }},
        auth_token: '{{ $data['auth_token'] }}',
        timestamp: new Date ().toString ()
    }

    const appState = {
        isLoggedIn: true,
        user: userData,
        accounts: <?php echo $data['accounts'] ?>
    }

    window.sessionStorage.setItem ( 'authenticated', true )

    var d1 = new Date ()
    var d2 = new Date ( d1 )
    d2.setMinutes ( d1.getMinutes () + 154.8 )

    alert ( location.href )

    // save app state with user date in local storage
    localStorage.appState = JSON.stringify ( appState )
    localStorage.setItem ( 'currencies', JSON.stringify (<?php echo $data['currencies'] ?>) )
    localStorage.setItem ( 'languages', JSON.stringify (<?php echo $data['languages'] ?>) )
    localStorage.setItem ( 'countries', JSON.stringify (<?php echo $data['countries'] ?>) )
    localStorage.setItem ( 'payment_types', JSON.stringify (<?php echo $data['payment_types'] ?>) )
    localStorage.setItem ( 'gateways', JSON.stringify (<?php echo $data['gateways'] ?>) )
    localStorage.setItem ( 'tax_rates', JSON.stringify (<?php echo $data['tax_rates'] ?>) )
    localStorage.setItem ( 'custom_fields', JSON.stringify (<?php echo $data['custom_fields'] ?>) )
    localStorage.setItem ( 'users', JSON.stringify (<?php echo $data['users'] ?>) )
    localStorage.setItem ( 'access_token', userData.auth_token )
    localStorage.setItem ( 'number_of_accounts', <?php echo $data['number_of_accounts']?>)
    localStorage.setItem ( 'expires', d2 )
    localStorage.setItem ( 'account_id', <?php echo $data['account_id'] ?>)
    const url = localStorage.getItem ( 'domain' ) || '<?php echo $data['redirect'] ?>'
    window.location.replace(`${url}/#/`)
</script>
