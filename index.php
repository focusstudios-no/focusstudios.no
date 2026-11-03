<!DOCTYPE html>
<html lang="en">

<head>
  <?php require($_SERVER['DOCUMENT_ROOT'] . "/head.php"); ?>
</head>

<body>

  <?php require($_SERVER['DOCUMENT_ROOT'] . "/toc.php"); ?>

  <div class="standard">
    <img src="/img/logo.svg" alt="Focus Studios" class="logo">
  </div>

  <br>

  <div class="standard colored justifyText centerOnMediumScreen">
    Focus Studios is a music and visuals establishment located in downtown Fredrikstad, Norway. You can find us at the start of walking street, top floor. We make such things as record sleeves, remixes and music videos. And we do a lot of mastering, logos and other production. By appointment only. Awesome stuff only. Price range: expensive. Organization number: <a href="https://w2.brreg.no/enhet/sok/detalj.jsp?orgnr=986758313">986758313</a>. Contact: <a href="https://peko.net">Ole Peko Sørensen</a>.
    <br>
  </div>

  <br>

  <div class="standard">
    <a href="https://maps.app.goo.gl/rDRnSNWisgu3qdzX9">Street address</a>: <br><br>
    Focus Studios<br>
    c/o Ole Peko Sørensen<br>
    Farmannsgate 1<br>
    1607 Fredrikstad<br>
    Norway
  </div>

  <br>

  <div class="standard">
    <img src="img/floor_plan_401.svg" alt="Floorplan" title="Floorplan" class="drawings">
  </div>

  <br>

  <?php
  try {
    $records = file_get_contents(__DIR__ . '/data/studio.json');
    if ($records === false) {
      throw new RuntimeException('Unable to read studio records.');
    }
    $studio = json_decode($records, true, 512, JSON_THROW_ON_ERROR);
    foreach (['software', 'hardware', 'artists'] as $section) {
      if (!isset($studio[$section]) || !is_array($studio[$section])) {
        throw new RuntimeException('Invalid studio section: ' . $section);
      }
    }
    foreach (array_merge($studio['software'], $studio['hardware']) as $item) {
      if (!is_string($item) || trim($item) === '') {
        throw new RuntimeException('Invalid equipment record.');
      }
    }
    foreach ($studio['artists'] as $artist) {
      if (!is_array($artist) || !isset($artist['id'], $artist['name'])
        || !is_string($artist['id']) || !preg_match('/^[a-z0-9-]+$/', $artist['id'])
        || !is_string($artist['name']) || trim($artist['name']) === ''
        || (isset($artist['url']) && (!is_string($artist['url'])
          || !filter_var($artist['url'], FILTER_VALIDATE_URL)
          || !preg_match('~^https?://~', $artist['url'])))) {
        throw new RuntimeException('Invalid artist record.');
      }
    }
  } catch (RuntimeException | JsonException $error) {
    error_log('Studio records: ' . $error->getMessage());
    http_response_code(500);
    echo '<p role="alert">Studio information could not be loaded. Please try again later.</p>';
    require __DIR__ . '/footer.php';
    exit;
  }
  ?>

  <?php foreach (['software' => 'Software', 'hardware' => 'Hardware'] as $section => $heading): ?>
    <section class="studio-section standard colored" id="<?php echo $section; ?>">
      <details open>
        <summary><h1><?php echo $heading; ?></h1></summary>
        <ul class="equipment-list">
          <?php foreach ($studio[$section] as $item): ?>
            <li><?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?></li>
          <?php endforeach; ?>
        </ul>
      </details>
    </section>
  <?php endforeach; ?>

  <section class="studio-section standard colored" id="artists">
    <details open>
      <summary><h1>Artists</h1></summary>
      <div class="artist-list">
        <?php foreach ($studio['artists'] as $artist): ?>
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
    </details>
  </section>

  <?php require($_SERVER['DOCUMENT_ROOT'] . "/footer.php"); ?>

</body>

</html>