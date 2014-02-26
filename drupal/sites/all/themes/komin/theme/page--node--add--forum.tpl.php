<header>
  <?php if (!empty($primary_nav) || !empty($secondary_nav) || !empty($page['navigation'])): ?>
    <div>
      <nav role="navigation">
        <?php if (!empty($primary_nav)): ?>
          <?php print render($primary_nav); ?>
        <?php endif; ?>
        <?php if (!empty($secondary_nav)): ?>
          <?php print render($secondary_nav); ?>
        <?php endif; ?>
        <?php if (!empty($page['navigation'])): ?>
          <?php print render($page['navigation']); ?>
        <?php endif; ?>
      </nav>
    </div>
  <?php endif; ?>
</header>

<div class="wrapper">
  <?php if (!empty($page['sidebar_first'])): ?>
    <aside>
      <?php print render($page['sidebar_first']); ?>
    </aside>
  <?php endif; ?>

  <article>
    <?php print render($title_prefix); ?>
    <?php if (!empty($title)): ?>
      <h1 class="box-title"><?php print $title; ?></h1>
    <?php endif; ?>
    <?php print render($title_suffix); ?>
    <?php print $messages; ?>
    <?php if (!empty($tabs)): ?>
      <?php print render($tabs); ?>
    <?php endif; ?>
    <?php if (!empty($page['help'])): ?>
      <?php print render($page['help']); ?>
    <?php endif; ?>
    <?php if (!empty($action_links)): ?>
      <ul class="action-links"><?php print render($action_links); ?></ul>
    <?php endif; ?>
    <?php print render($page['content']); ?>
    <?php if (!empty($breadcrumb)): print $breadcrumb; endif; ?>
 </article>

</div>
<footer class="bigfoot">
  <?php print render($page['footer']); ?>
</footer>
