<?php
$this->assign('title', get_option('site_name'));
$this->assign('description', get_option('description'));
$this->assign('content_title', get_option('site_name'));
$this->assign('og_title', $link->title);
$this->assign('og_description', $link->description);
$this->assign('og_image', $link->image);
?>

<?php $this->start('scriptTop'); ?>
<script type="text/javascript">
    if (window.self !== window.top) {
        window.top.location.href = window.location.href;
    }
</script>
<?php $this->end(); ?>

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="box box-success">
            <div class="box-body text-center">
                <?php if (!empty($banner_728x90)) : ?>
                    <div class="banner banner-728x90">
                        <div class="banner-inner">
                            <?= $banner_728x90; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <h4><?= __('Your link is almost ready.') ?></h4>

                <span id="countdown" class="countdown">
                    <span id="timer" class="timer"><?= get_option('counter_value', 5) ?></span><br><?= __('Seconds') ?>
                </span>

                <?php if (!empty($banner_468x60)) : ?>
                    <div class="banner banner-468x60">
                        <div class="banner-inner">
                            <?= $banner_468x60; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div style="margin-bottom: 10px;">
                    <a href="javascript: void(0)" class="btn btn-success btn-lg get-link disabled">
                        <?= __('Please wait...') ?>
                    </a>
                </div>

                <?php if (!empty($banner_336x280)) : ?>
                    <div class="banner banner-336x280">
                        <div class="banner-inner">
                            <?= $banner_336x280; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="myTestAd" style="height: 5px; width: 5px; position: absolute;"></div>

            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>


<?=
$this->Form->create(null, [
    'url' => ['controller' => 'Links', 'action' => 'go', 'prefix' => false],
    'id' => 'go-link',
    'class' => 'hidden'
]);
?>

<?= $this->Form->hidden('alias', ['value' => $link->alias]); ?>
<?= $this->Form->hidden('ci', ['value' => $campaign_item->campaign_id]); ?>
<?= $this->Form->hidden('cui', ['value' => $campaign_item->campaign->user_id]); ?>
<?= $this->Form->hidden('cii', ['value' => $campaign_item->id]); ?>
<?= $this->Form->hidden('ref', ['value' => strtolower(env('HTTP_REFERER'))]); ?>
<?= $this->Form->hidden('country', ['value' => $country]); ?>

<?=
$this->Form->button(__('Submit'), [
    'id' => 'go-submit',
    'class' => 'hidden'
]);
?>

<?= $this->Form->end(); ?>

<?php if (get_option('enable_popup', 'yes') == 'yes' && $show_pop_ad) : ?>
    <?=
    $this->Form->create(null, [
        'url' => ['controller' => 'Links', 'action' => 'popad', 'prefix' => false],
        'target' => "_blank",
        'id' => 'go-popup',
        'class' => 'hidden'
    ]);
    ?>

    <?= $this->Form->hidden('pop_ad', ['value' => $pop_ad]); ?>

    <?= $this->Form->end(); ?>
<?php endif; ?>

<?php $this->start('scriptBottom'); ?>
<?php $this->end(); ?>
