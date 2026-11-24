const assert = require('node:assert/strict');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const { spawnSync } = require('node:child_process');
const test = require('node:test');
const vm = require('node:vm');

const root = path.resolve(__dirname, '..', '..');

function createResizableTable(containerWidth = 400, minimumWidth = 0) {
  const handlers = {};
  const frames = [];
  let resize;
  const classes = () => {
    const values = new Set();
    return {
      add: value => values.add(value),
      remove: (...names) => names.forEach(value => values.delete(value)),
      contains: value => values.has(value),
      toggle: (value, enabled) => enabled ? values.add(value) : values.delete(value)
    };
  };
  const cell = () => ({
    dataset: {}, classList: classes(),
    style: { removeProperty(name) { delete this[name]; } },
    getBoundingClientRect() { const width = parseFloat(this.style.width) || Math.max(minimumWidth, containerWidth) / 2; return { width, right: width }; },
    closest() { return this; },
    setPointerCapture() {}
  });
  const headers = [cell(), cell()];
  const data = [cell(), cell()];
  const table = {
    clientLeft: 2,
    dataset: {}, classList: classes(),
    parentElement: { get clientWidth() { return containerWidth; } },
    style: { setProperty(name, value) { this[name] = value; }, removeProperty(name) { delete this[name]; } },
    tHead: { querySelector: () => headers[0], contains: header => headers.includes(header) },
    rows: [{ cells: headers }, { cells: data }],
    querySelectorAll: selector => selector === 'thead th' ? headers : [],
    addEventListener(name, callback) { handlers[name] = callback; },
    getBoundingClientRect() { return { left: 0, width: Math.max(minimumWidth, parseFloat(this.style.width) || containerWidth) }; }
  };
  const window = { requestAnimationFrame: callback => frames.push(callback) };
  vm.runInNewContext(fs.readFileSync(path.join(root, 'resize_table.js'), 'utf8'), {
    window,
    document: {
      querySelectorAll: () => [table],
      addEventListener(name, callback) {
        callback();
        containerWidth = 400;
      }
    },
    MutationObserver: class { observe() {} },
    ResizeObserver: class {
      constructor(callback) { resize = callback; }
      observe(element) { assert.equal(element, table.parentElement); }
    },
    getComputedStyle: () => ({ display: 'table-cell', minWidth: `${minimumWidth}px` }),
    setTimeout: callback => frames.push(callback)
  });
  while (frames.length) frames.shift()();
  const event = (clientX, options = {}) => ({
    target: headers[0], clientX, button: 0, pointerId: 1, pointerType: 'mouse',
    preventDefault() {}, ...options
  });
  return {
    headers, data, table, handlers, event,
    resize(width) {
      containerWidth = width;
      resize();
      while (frames.length) frames.shift()();
    }
  };
}

test('header-edge drags resize columns, mark the full column and do not sort', () => {
  const { headers, data, table, handlers, event } = createResizableTable();
  handlers.pointerdown(event(100));
  assert.equal(headers[0].classList.contains('column-resizing'), false);
  handlers.pointerdown(event(199));
  assert.equal(headers[0].classList.contains('column-resizing'), true);
  assert.equal(data[0].classList.contains('column-resizing'), true);
  assert.equal(data[1].classList.contains('column-resizing'), false);
  assert.equal(table.style['--column-resize-guide-left'], '198px');
  handlers.pointermove(event(259));
  assert.equal(headers[0].style.width, '260px');
  assert.equal(headers[1].style.width, '200px');
  assert.equal(table.style.width, '460px');
  assert.equal(table.style['--column-resize-guide-left'], '258px');
  handlers.pointerup(event(259));
  assert.equal(data[0].classList.contains('column-resizing'), false);
  assert.equal(table.style['--column-resize-guide-left'], undefined);
  let sorted = true;
  handlers.click(event(100, { detail: 1, stopImmediatePropagation() { sorted = false; } }));
  assert.equal(sorted, false);
  handlers.dblclick(event(259));
  assert.equal(headers[0].style.width, '200px');
});

test('initial column widths are measured after the sidebars have opened', () => {
  const { headers, table } = createResizableTable(800);
  assert.equal(table.style.width, '400px');
  assert.equal(headers[0].dataset.defaultColumnWidth, '200');
});

test('table and default column widths follow both growing and shrinking containers', () => {
  const state = createResizableTable();
  for (const width of [900, 600, 400]) {
    state.resize(width);
    assert.equal(Number.parseFloat(state.table.style.width), width);
    for (const header of state.headers) {
      assert.equal(Number(header.dataset.columnWidth), width / 2);
      assert.equal(Number(header.dataset.defaultColumnWidth), width / 2);
    }
  }
});

test('narrow layouts keep the CSS minimum without carrying excess width into wide layouts', () => {
  const state = createResizableTable(400, 700);
  for (const width of [300, 1200, 500, 1400]) {
    state.resize(width);
    assert.equal(Number.parseFloat(state.table.style.width), Math.max(700, width));
  }
});

test('container resizing preserves dragged proportions and adapts double-click defaults', () => {
  const state = createResizableTable();
  state.handlers.pointerdown(state.event(199));
  state.handlers.pointermove(state.event(259));
  state.handlers.pointerup(state.event(259));
  state.resize(800);
  assert.equal(Number.parseFloat(state.table.style.width), 920);
  assert.equal(Number(state.headers[0].dataset.columnWidth), 520);
  assert.equal(Number(state.headers[1].dataset.columnWidth), 400);
  state.handlers.dblclick(state.event(state.headers[0].getBoundingClientRect().right - 1));
  assert.equal(Number.parseFloat(state.table.style.width), 800);
  assert.equal(Number(state.headers[0].dataset.columnWidth), 400);
});

test('touch resize respects minimum widths and cancellation removes the guide', () => {
  const { headers, data, table, handlers, event } = createResizableTable();
  handlers.pointerdown(event(180, { pointerType: 'touch' }));
  assert.equal(data[0].classList.contains('column-resizing'), true);
  handlers.pointermove(event(-100, { pointerType: 'touch' }));
  assert.equal(headers[0].style.width, '48px');
  handlers.pointercancel();
  assert.equal(data[0].classList.contains('column-resizing'), false);
  assert.equal(table.style['--column-resize-guide-left'], undefined);
});

test('Updates paginates Git history and reports invalid input or feeds', t => {
  const dir = fs.mkdtempSync(path.join(os.tmpdir(), 'site-updates-'));
  t.after(() => fs.rmSync(dir, { recursive: true, force: true }));
  fs.mkdirSync(path.join(dir, 'data'));
  for (const file of ['updates_view.php', 'updates.css']) {
    fs.copyFileSync(path.join(root, file), path.join(dir, file));
  }
  for (const file of ['head.php', 'toc.php', 'footer.php']) {
    fs.writeFileSync(path.join(dir, file), '');
  }
  const runs = Array.from({ length: 28 }, (_, index) => ({
    head_sha: index.toString(16).padStart(40, '0'),
    created_at: '2026-01-01T00:00:00Z',
    display_title: `Update ${index} <test>`
  }));
  const feed = path.join(dir, 'data', 'updates.json');
  fs.writeFileSync(feed, JSON.stringify({ workflow_runs: runs }));
  const render = query => {
    const script = `$_GET = json_decode(getenv('TEST_QUERY'), true); $repository = 'https://github.com/example/site'; require ${JSON.stringify(path.join(dir, 'updates_view.php'))}; fwrite(STDERR, "\\nSTATUS:" . (http_response_code() ?: 200));`;
    const result = spawnSync('php', ['-r', script], {
      encoding: 'utf8',
      env: { ...process.env, TEST_QUERY: JSON.stringify(query) }
    });
    assert.equal(result.status, 0, result.stderr);
    return { body: result.stdout, status: Number(result.stderr.match(/STATUS:(\d+)/)?.[1]) };
  };
  const first = render({});
  assert.equal(first.status, 200);
  assert.match(first.body, /<svg class="icons" aria-hidden="true"><use href="\/icons\.svg#github"><\/use><\/svg>\s*<h1>Updates<\/h1>\s*<svg class="icons" aria-hidden="true"><use href="\/icons\.svg#github"><\/use><\/svg>/);
  assert.match(first.body, /Page 1 of 2/);
  assert.match(first.body, /Update 0 &lt;test&gt;/);
  assert.match(first.body, /<time datetime="2026-01-01T00:00:00Z">01-01-2026 00:00<\/time>/);
  assert.match(first.body, /<table>\s*<thead><tr><th scope="col">Title<\/th><th scope="col">Date<\/th><th scope="col">Number<\/th><th scope="col">Commit<\/th><\/tr><\/thead>\s*<tbody>/);
  assert.doesNotMatch(first.body, /data-label=/);
  const styles = fs.readFileSync(path.join(root, 'updates.css'), 'utf8');
  assert.doesNotMatch(styles, /container-type|@container|data-label|display:\s*grid/);
  assert.match(first.body, new RegExp(`https://github.com/example/site/commit/${runs[0].head_sha}`));
  assert.doesNotMatch(first.body, /Update 25 &lt;test&gt;/);
  const second = render({ page: '2' });
  assert.match(second.body, /Update 25 &lt;test&gt;/);
  assert.equal(render({ page: ['1'] }).status, 400);
  fs.writeFileSync(feed, '{"workflow_runs":[{}]}');
  const invalid = render({});
  assert.equal(invalid.status, 503);
  assert.match(invalid.body, /Updates unavailable/);
});
