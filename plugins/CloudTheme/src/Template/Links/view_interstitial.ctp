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

<div class="myTestAd" style="height: 5px; width: 5px; position: absolute;"></div>
<iframe id="frame" src="<?= ($plan_disable_ads) ? '' : $campaign_item->campaign->website_url ?>" style="width: 100%; border: none;"></iframe>

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
$this->Form->button(__('Please Wait 10s'), [
    'id' => 'go-submit',
    'class' => 'btn btn-default',
    'onclick' => 'javascript: return false;'
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
