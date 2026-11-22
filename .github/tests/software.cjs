const assert = require('node:assert/strict');
const path = require('node:path');
const { spawnSync } = require('node:child_process');
const test = require('node:test');

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
