<?php
$this->assign('title', __('License Activation - Shared by MAHThemes'));
$this->assign('description', '');
$this->assign('content_title', __('License Activation - Shared by MAHThemes'));

?>

<div class="box box-primary">
    <div class="box-body">

        <?= $this->Form->create(null, [
            'url' => ['controller' => 'Activation', 'action' => 'index']
        ]); ?>

        <?=
        $this->Form->input('personal_token', [
            'label' => __('Personal Token'),
            'class' => 'form-control',
            'type' => 'text',
            'default' => get_option('personal_token', ''),
            'required' => 'required'
        ]);
        ?>

        <span class="help-block"><?= __('Click on this URL {0} to learn how to make it nulled. - Shared by MAHThemes.tk',
                '<a href="https://mahthemes.tk/2017/10/15/adlinkfly-v4-5-1-monetized-url-shortener-free-download/" target="_blank">https://mahthemes.tk/2017/10/15/adlinkfly-v4-5-1-monetized-url-shortener-free-download/</a>') ?></span>

        <?=
        $this->Form->input('purchase_code', [
            'label' => __('Purchase Code'),
            'class' => 'form-control',
            'type' => 'text',
            'default' => get_option('purchase_code', ''),
            'required' => 'required'
        ]);
        ?>

        <span class="help-block"><?= __('Click on this URL {0} to learn how to make it nulled. - Shared by MAHThemes.tk',
                '<a href="https://mahthemes.tk/2017/10/15/adlinkfly-v4-5-1-monetized-url-shortener-free-download/" target="_blank">https://mahthemes.tk/2017/10/15/adlinkfly-v4-5-1-monetized-url-shortener-free-download/</a>') ?></span>

        <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']); ?>
        <?= $this->Form->end(); ?>

    </div><!-- /.box-body -->
</div>
