<footer>
    <?php if ($this->request->params['action'] === 'home') : ?>
        <div class="payment-methods">
            <div class="container text-center">
                <?= $this->Html->image('Payment-Methods.png'); ?>
            </div>
        </div>
        <div class="separator">
            <div class="container"></div>
        </div>
    <?php endif; ?>

    <div class="copyright-container">
        <div class="container">
            <div class="row">
                <div class="col-sm-4 bottom-menu">
                    <ul class="list-inline">
                        <li><a href="<?= $this->Url->build('/pages/privacy'); ?>"><?= __('Privacy Policy') ?></a>
                        </li>
                        <li><a href="<?= $this->Url->build('/pages/terms'); ?>"><?= __('Terms of Use') ?></a>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-4 social-links">
                    <ul class="list-inline">
                        <?php if (get_option('facebook_url')) : ?>
                            <li><a href="<?= h(get_option('facebook_url')) ?>"><i class="fa fa-facebook"></i></a></li>
                        <?php endif; ?>
                        <?php if (get_option('twitter_url')) : ?>
                            <li><a href="<?= h(get_option('twitter_url')) ?>"><i class="fa fa-twitter"></i></a></li>
                        <?php endif; ?>
                        <?php if (get_option('google_plus_url')) : ?>
                            <li><a href="<?= h(get_option('google_plus_url')) ?>"><i class="fa fa-google-plus"></i></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-sm-4 copyright">
                    <div><?= __('Copyright &copy;') ?> <?= h(get_option('site_name')) ?> <?= date("Y") ?></div>

                </div>
            </div>
        </div>
    </div>
</footer>

<?= $this->element('js_vars'); ?>

<?php
echo $this->Assets->script('/js/ads.js');

if ((bool)get_option('combine_minify_css_js', false)) {
    echo $this->Assets->script('/build/js/script.min.js?ver=' . APP_VERSION);
} else {
    echo $this->Assets->script('/vendor/jquery.min.js?ver=' . APP_VERSION);
    echo $this->Assets->script('/vendor/bootstrap/js/bootstrap.min.js?ver=' . APP_VERSION);
    echo $this->Assets->script('/vendor/owl/owl.carousel.min.js?ver=' . APP_VERSION);
    echo $this->Assets->script('/vendor/wow.min.js?ver=' . APP_VERSION);
    echo $this->Assets->script('/vendor/clipboard.min.js?ver=' . APP_VERSION);
    echo $this->Assets->script('/js/front.js?ver=' . APP_VERSION);
    echo $this->Assets->script('/js/app.js?ver=' . APP_VERSION);
}
?>

<?php if (in_array(get_option('captcha_type', 'recaptcha'), ['recaptcha', 'invisible-recaptcha'])) : ?>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadRecaptchaCallback&render=explicit"
        async defer></script>
<?php endif; ?>

<?php if (get_option('captcha_type') == 'solvemedia') : ?>
    <?php
    $sm_server = (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") ? "http://api" : "https://api-secure";
    ?>
    <script type="text/javascript" src="<?= $sm_server ?>.solvemedia.com/papi/challenge.ajax"></script>
<?php endif; ?>

<?= $this->fetch('scriptBottom') ?>
<?= get_option('footer_code'); ?>
