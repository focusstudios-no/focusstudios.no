<?php
if (!isset($section, $heading) || !in_array($section, ['software', 'hardware', 'artists'], true)) {
  http_response_code(404);
  echo 'Page not found.';
  exit;
}
$pageTitle = $heading . ' - Focus Studios';
$canonicalUrl = 'https://focusstudios.no/' . $section . '.php';
try {
  $records = file_get_contents(__DIR__ . '/data/studio.json');
  if ($records === false) {
    throw new RuntimeException('Unable to read studio records.');
  }
  $studio = json_decode($records, true, 512, JSON_THROW_ON_ERROR);
  if (!isset($studio[$section]) || !is_array($studio[$section])) {
    throw new RuntimeException('Invalid studio section.');
  }
  foreach ($studio[$section] as $item) {
    if ($section === 'software') {
      if (!is_array($item) || !isset($item['vendor'], $item['name'], $item['icon'], $item['url'])
        || !is_string($item['vendor']) || trim($item['vendor']) === ''
        || !is_string($item['name']) || trim($item['name']) === ''
        || !is_string($item['icon']) || !preg_match('/^[a-z0-9-]+$/', $item['icon'])
        || !is_string($item['url']) || !filter_var($item['url'], FILTER_VALIDATE_URL)
        || !preg_match('~^https://~', $item['url'])) {
        throw new RuntimeException('Invalid software record.');
      }
    } elseif ($section === 'hardware') {
      if (!is_string($item) || trim($item) === '') {
        throw new RuntimeException('Invalid equipment record.');
      }
    } elseif (!is_array($item) || !isset($item['id'], $item['name'])
      || !is_string($item['id']) || !preg_match('/^[a-z0-9-]+$/', $item['id'])
      || !is_string($item['name']) || trim($item['name']) === ''
      || (isset($item['url']) && (!is_string($item['url'])
        || !filter_var($item['url'], FILTER_VALIDATE_URL)
        || !preg_match('~^https?://~', $item['url'])))) {
      throw new RuntimeException('Invalid artist record.');
    }
  }
} catch (RuntimeException | JsonException $error) {
  error_log('Studio records: ' . $error->getMessage());
  http_response_code(500);
  $studio = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head><?php require __DIR__ . '/head.php'; ?></head>
<body>
  <?php require __DIR__ . '/toc.php'; ?>
  <main class="studio-section standard colored" id="<?php echo $section; ?>">
    <h1><?php echo $heading; ?></h1>
    <?php if ($studio === null): ?>
      <p role="alert">Studio information could not be loaded. Please try again later.</p>
    <?php elseif ($section === 'artists'): ?>
      <div class="artist-list">
        <?php foreach ($studio[$section] as $artist): ?>
          <article id="<?php echo htmlspecialchars($artist['id'], ENT_QUOTES, 'UTF-8'); ?>">
            <h2>
              <?php if (isset($artist['url'])): ?>
                <a href="<?php echo htmlspecialchars($artist['url'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8'); ?></a>
              <?php else: ?>
                <?php echo htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8'); ?>
              <?php endif; ?>
            </h2>
          </article>
        <?php endforeach; ?>
      </div>
    <?php elseif ($section === 'software'): ?>
      <ul class="equipment-list software-list">
        <?php foreach ($studio[$section] as $item): ?>
          <li>
            <a href="<?php echo htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>">
              <svg class="icons" aria-hidden="true"><use href="/icons.svg?v=<?php echo filemtime(__DIR__ . '/icons.svg'); ?>#<?php echo htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>"></use></svg>
              <span><?php echo htmlspecialchars($item['vendor'] . ' ' . $item['name'], ENT_QUOTES, 'UTF-8'); ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <ul class="equipment-list">
        <?php foreach ($studio[$section] as $item): ?>
          <li><?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </main>
  <?php require __DIR__ . '/footer.php'; ?>
</body>
</html>
