const assert = require('node:assert/strict');
const path = require('node:path');
const { spawnSync } = require('node:child_process');
const test = require('node:test');

test('main pages show a separate heading with their navigation icon', () => {
  const root = path.resolve(__dirname, '..', '..');
  for (const [file, heading, icon] of [
    ['index.php', 'Home', 'home'],
    ['software.php', 'Software', 'display'],
    ['hardware.php', 'Hardware', 'player'],
    ['artists.php', 'Artists', 'band']
  ]) {
    const result = spawnSync('php', ['-r',
      '$_SERVER["DOCUMENT_ROOT"] = $argv[1]; require $argv[1] . "/" . $argv[2];',
      root, file], { cwd: root, encoding: 'utf8' });
    assert.equal(result.status, 0, result.stderr);
    assert.match(result.stdout, new RegExp(`<div class="top-line">\\s*<svg[^>]*><use href="/icons\\.svg#${icon}"></use></svg>\\s*<h1>${heading}</h1>\\s*<svg[^>]*><use href="/icons\\.svg#${icon}"></use></svg>\\s*</div>`), file);
    assert.equal((result.stdout.match(/<h1\b/g) || []).length, 1, file);
  }
});

test('Software renders underscore-separated icon names', () => {
  const root = path.resolve(__dirname, '..', '..');
  const result = spawnSync('php', [path.join(root, 'software.php')], {
    cwd: root,
    encoding: 'utf8'
  });
  assert.equal(result.status, 0, result.stderr);
  for (const id of ['ableton_live', 'premiere_pro', 'visual_studio_code']) {
    assert.match(result.stdout, new RegExp(`#${id}"`));
  }
  assert.doesNotMatch(result.stdout, /Studio information could not be loaded/);
});
