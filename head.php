<?php
$canonicalPath = preg_replace('~\.php(?=\?|$)~', '', $_SERVER['REQUEST_URI'] ?? '/');
$canonicalPath = preg_replace('~^/index(?=\?|$)~', '/', $canonicalPath);
$canonicalUrl = $canonicalUrl ?? ('https://focusstudios.no' . $canonicalPath);
$pageTitleMap = [
    'release.php' => 'Release',
    'releases_grid.php' => 'Releases',
    'releases_details.php' => 'Releases',
];
$pageName = $pageTitleMap[basename($_SERVER['SCRIPT_NAME'] ?? '')] ?? '';
$pageTitle = $pageTitle ?? ($pageName !== '' ? 'Focus Studios - ' . $pageName : 'Focus Studios');
?>
<title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
<meta charset="utf-8">
<link rel="icon" href="/favicon.svg?v=<?php echo filemtime(__DIR__ . '/favicon.svg'); ?>" type="image/svg+xml">
<link rel="stylesheet" type="text/css" href="/style.css?v=<?php echo filemtime(__DIR__ . '/style.css'); ?>">
<meta property="og:image" content="https://focusstudios.no/favicon.png">
<meta property="og:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="keywords" content="Focus Studios, Focus, Focus Studios Fredrikstad, music studio, visuals studio, studio in Fredrikstad, studio in Norway, Fredrikstad Studio, Norway studio">
<meta name="description" content="This is the official website for the music and visuals studio Focus Studios, Fredrikstad, Norway.">
<link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl ?? 'https://focusstudios.no', ENT_QUOTES, 'UTF-8'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">