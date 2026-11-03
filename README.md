# Focus Studios

The Focus Studios music and visuals website.

The homepage contains Software, Hardware and Artists sections, with local
navigation to each section and artist. Studio equipment and artist records
are stored in `data/studio.json`. Only verified artist website links are
included. The original studio introduction, address, floor plan, release
pages and artwork are retained.

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
