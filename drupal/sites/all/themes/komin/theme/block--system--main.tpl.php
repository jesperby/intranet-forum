<section class="box" id="<?php print $block_html_id; ?>">
  <?php $title = drupal_get_title();
  if (!empty($title)): ?>
    <h1 class="box-title"><?php print $title; ?></h1>
  <?php endif; ?>

  <?php print render($title_prefix); ?>
  <?php if ($block->subject): ?>
    <h1 class="box-title"<?php print $title_attributes; ?>><?php print $block->subject ?></h1>
  <?php endif; ?>
  <?php print render($title_suffix); ?>
  <div class="box-content body-copy"<?php print $content_attributes; ?>>
    <?php print $content ?>
  </div>
</section>