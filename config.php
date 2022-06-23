.git/info/exclude
<?php 
    $valeursUtilisees = array(
        'ID_SERVEUR'            => '705530817039827026',
        'API_REFERENCE'         => 'https://discord.com/api/v10/'
    );

    return (object) array(
        'BOT_TOKEN'             => 'OTY4MTUxMjM0MTA2MjQxMDM0.GaMlrx.SAC3eESlpNycOjHOgTm53OIh3E5H8v_e6UDwDU',
        'OAUTH2_CLIENT_SECRET'  => 'YrhqGeFPWUEwcYKP_3dgXJkAjQ490rzM',
        'OAUTH2_CLIENT_ID'      => '968151234106241034',

        'ID_SERVEUR'            => $valeursUtilisees['ID_SERVEUR'],
        'API_REFERENCE'         => $valeursUtilisees['API_REFERENCE'],

        'ID_MODERATION'         => '941440986595336202',

        'authorizeURL'          => 'https://discord.com/api/oauth2/authorize',
        'tokenURL'              => 'https://discord.com/api/oauth2/token',
        'apiURLUserBase'        => 'https://discord.com/api/users/@me',
        'apiURLGuild'           => 'https://discord.com/api/users/@me/guilds',
        'apiURLGuildInfo'       => 'https://discord.com/api/users/@me/guilds/' . $valeursUtilisees['ID_SERVEUR'] . '/member',
        'apiURLGuildRoles'      => $valeursUtilisees['API_REFERENCE'] . 'guilds/' . $valeursUtilisees['ID_SERVEUR'] . '/roles',
        'revokeURL'             => 'https://discord.com/api/oauth2/token/revoke'
    );
?>