# Focus Studios

The Focus Studios music and visuals website.

Software, Hardware and Artists have separate pages. The homepage retains
the studio introduction, address and floor plan. Individual artists appear
only on the Artists page, not on the homepage or in the Menu.
Studio equipment and artist records
are stored in `data/studio.json`. Only verified artist website links are
included. The original studio introduction, address, floor plan, release
pages and artwork are retained.

Software records specify `vendor`, `name`, a shared `icon` symbol and an
official HTTPS `url`. Each icon and program name link to that product's website.
Keep software records alphabetized by their displayed vendor-first names.
The Software page displays vendor-first names with monochrome symbols from
Pekosoft's master sprite. BandLab, Live, Premiere Pro, Photoshop, Illustrator
and Visual Studio Code use recognizable brand artwork from
[Simple Icons](https://github.com/simple-icons/simple-icons), distributed under
[CC0](https://github.com/simple-icons/simple-icons/blob/develop/LICENSE.md):
version 11 for all except Live (version 5). Adobe tiles retain rounded corners.
ChatGPT, VLC and Spotify also use Simple Icons version 11 artwork.
Audacity follows the supplied current icon, with rounded headphones and a
central waveform, adapted to monochrome without the background disc.
WaveLab's triangular W, RX's concentric rings and Waves' waveform
are monochrome SVG adaptations of the supplied application-icon captures.
Brand names and marks belong to their respective owners.
Traktor uses a simplified monochrome adaptation of its circular deck mark,
referenced from the TraktorLogo.png artwork on Wikimedia Commons.

All pages include the Menu and Settings sidebars used by MC Peko and
Silentlab. Settings provides Dark mode, Fullscreen, Share and Reset.
Desktop sidebar preferences persist. On phones, only one sidebar opens at
a time. Original bitmap artwork is preserved in both interface modes.

Run the shared `local-tools/startup.ps1` command from the parent workspace
to preview the site at http://localhost:8002/.

Deploy runtime pages, assets and JSON records. Keep this README, Git
metadata and development files out of production uploads.

GitHub Actions uploads runtime files via FTPS after website changes are
pushed to `main`. Run Deploy website manually for the initial upload or
a retry. Configure repository secrets `FTP_SERVER`, `FTP_USERNAME`,
`FTP_PASSWORD` and `SERVER_DIR`. A trailing `/` is added automatically.
