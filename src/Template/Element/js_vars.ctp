<script type='text/javascript'>
    /* <![CDATA[ */
    var app_vars = [];
    app_vars['base_url'] = '<?= $this->Url->build('/', true); ?>';
    app_vars['language'] = '<?= h(locale_get_default()); ?>';
    app_vars['copy'] = '<?= h(__("Copy")); ?>';
    app_vars['copied'] = '<?= h(__("Copied!")); ?>';
    app_vars['user_id'] = '<?= h($this->request->session()->read('Auth.User.id')); ?>';
    app_vars['home_shortening_register'] = '<?= (get_option('home_shortening_register') == 'yes') ? 'yes' : 'no' ?>';
    app_vars['enable_captcha'] = '<?= get_option('enable_captcha', 'no'); ?>';
    app_vars['captcha_type'] = '<?= h(get_option('captcha_type', "recaptcha")); ?>';
    app_vars['reCAPTCHA_site_key'] = '<?= h(get_option('reCAPTCHA_site_key')); ?>';
    app_vars['invisible_reCAPTCHA_site_key'] = '<?= h(get_option('invisible_reCAPTCHA_site_key')); ?>';
    app_vars['solvemedia_challenge_key'] = '<?= h(get_option('solvemedia_challenge_key')); ?>';
    app_vars['captcha_short_anonymous'] = '<?= h(get_option('enable_captcha_shortlink_anonymous', 0)); ?>';
    app_vars['captcha_shortlink'] = '<?= h(get_option('enable_captcha_shortlink', 'no')); ?>';
    app_vars['captcha_signup'] = '<?= h(get_option('enable_captcha_signup', 'no')); ?>';
    app_vars['captcha_forgot_password'] = '<?= h(get_option('enable_captcha_forgot_password', 'no')); ?>';
    app_vars['captcha_contact'] = '<?= h(get_option('enable_captcha_contact', 'no')); ?>';
    app_vars['counter_value'] = '<?= h(get_option('counter_value', 5)); ?>';
    app_vars['counter_start'] = '<?= h(get_option('counter_start', 'DOMContentLoaded')); ?>';
    app_vars['get_link'] = '<?= h(__('Get Link')); ?>';
    app_vars['getting_link'] = '<?= h(__('Getting link...')); ?>';
    app_vars['skip_ad'] = '<?= h(__('Skip Ad')); ?>';
    app_vars['force_disable_adblock'] = '<?= h(get_option('force_disable_adblock', 0)); ?>';
    app_vars['please_disable_adblock'] = '<?= h(__("Please disable Adblock to proceed to the destination page.")); ?>';
    /* ]]> */
</script>
