<br>
<br>


<div class="standard">
  &copy; <a href="https://focusstudios.no">Focus Studios</a>.
  Some rights reserved.
</div>

<br>

Produced at <a href="https://focusstudios.no">Focus Studios</a>.

<br>
<br>

<div class="images">
  <a href="https://focusstudios.no">
    <img src="/img/logo.svg" alt="Focus Studios" style="height: 50px;">
  </a>
</div>

<br>
<br>
<br>

<script>
function revealSection() {
  const id = window.location.hash.slice(1);
  if (!id) return;
  const target = document.getElementById(id);
  if (!target) return;
  const section = target.closest('.studio-section');
  const details = section?.querySelector('details');
  if (details) {
    details.open = true;
    target.scrollIntoView();
  }
}
window.addEventListener('hashchange', revealSection);
revealSection();
</script>
<script src="/index.js?v=<?php echo filemtime(__DIR__ . '/index.js'); ?>"></script>

<div class="image-container">
  <a href="https://facebook.com/focusstudiosfredrikstad">
    <img src="/img/facebook.svg" alt="Facebook" title="Facebook" style="height: 30px; width: 30px;">
  </a>
</div>

<br>
<br>
<br>