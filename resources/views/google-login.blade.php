<style>
    #dialogoverlay {
        display: none;
        opacity: .8;
        position: fixed;
        top: 0px;
        left: 0px;
        background: #FFF;
        width: 100%;
        z-index: 10;
    }

    #dialogbox {
        display: none;
        position: fixed;
        background: #000;
        border-radius: 7px;
        width: 550px;
        z-index: 10;
    }

    #dialogbox > div {
        background: #FFF;
        margin: 8px;
    }

    #dialogbox > div > #dialogboxhead {
        background: #666;
        font-size: 19px;
        padding: 10px;
        color: #CCC;
    }

    #dialogbox > div > #dialogboxbody {
        background: #333;
        padding: 20px;
        color: #FFF;
    }

    #dialogbox > div > #dialogboxfoot {
        background: #666;
        padding: 10px;
        text-align: right;
    }
</style>


<body style="background-color: #000">
<span style="font-size: 60px; color: #FFF">Loading...</span>

<div id="dialogoverlay"></div>
<div id="dialogbox">
    <div>
        <div id="dialogboxhead"></div>
        <div id="dialogboxbody"></div>
        <div id="dialogboxfoot"></div>
    </div>
</div>


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

    const require_login = <?= $data['require_login'] ?>

        // save app state with user date in local storage
        localStorage.appState = JSON.stringify ( appState )
    localStorage.setItem ( 'allowed_permissions', JSON.stringify (<?php echo $data['allowed_permissions'] ?>) )
    localStorage.setItem ( 'industries', JSON.stringify (<?php echo $data['industries'] ?>) )
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

    if ( require_login == 1 ) {
        render ( 'Please enter your password:', 'confirmPassword' )
    } else {
        window.location.replace ( `${url}/#/` )
    }

    function confirmPassword (password) {
        window.location.replace ( `${url}/#/` )
    }

    function render ( dialog, func ) {
        var winW = window.innerWidth;
        var winH = window.innerHeight;
        var dialogoverlay = document.getElementById ( 'dialogoverlay' );
        var dialogbox = document.getElementById ( 'dialogbox' );
        dialogoverlay.style.display = "block";
        dialogoverlay.style.height = winH + "px";
        dialogbox.style.left = (winW / 2) - (550 * .5) + "px";
        dialogbox.style.top = "100px";
        dialogbox.style.display = "block";
        document.getElementById ( 'dialogboxhead' ).innerHTML = "Please confirm your password";
        document.getElementById ( 'dialogboxbody' ).innerHTML = dialog;
        document.getElementById ( 'dialogboxbody' ).innerHTML += '<br><input name="password" id="password" type="password">';
        document.getElementById ( 'dialogboxfoot' ).innerHTML = '<button onclick="ok(\'' + func + '\')">OK</button> <button onclick="cancel()">Cancel</button>';
    }

    function cancel () {
        document.getElementById ( 'dialogbox' ).style.display = "none";
        document.getElementById ( 'dialogoverlay' ).style.display = "none";
    }

    function ok () {
        var prompt_value1 = document.getElementById ( 'password' ).value;
        confirmPassword(prompt_value1 );
        document.getElementById ( 'dialogbox' ).style.display = "none";
        document.getElementById ( 'dialogoverlay' ).style.display = "none";
    }

</script>
