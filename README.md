# Aberdeen Mumbai WordPress assessment

The theme recreates the supplied university home, undergraduate and postgraduate listings, registration dialog and thank-you state. The catalogue uses native WordPress content, a React editor panel and a progressively enhanced filter.

## Local setup

1. Install WordPress 6.8 or newer with PHP 8.1 or newer, MySQL or MariaDB, and pretty-permalink support. Enable PHP GD or Imagick and mysqli.
2. Copy `theme/` into `wp-content/themes/aberdeen-mumbai/` and activate **Aberdeen Mumbai Assessment**.
3. Choose **Post name** under **Settings → Permalinks**. Open **Programs → Program Types** to confirm the two default terms.
4. Run `wp eval-file /absolute/path/to/tools/seed.php` from your WordPress installation. The seeder copies the supplied image crops into the Media Library and creates ten sample programmes. Re-running it skips programmes already present.
5. Open the site root, `/programmes/`, `/programmes/undergraduate/`, `/programmes/postgraduate/`, and `/programmes/bachelor-engineering/`. Use **Programs → Edit** to update programme fields, and **Programme Enquiries** as an administrator to see form submissions.

`exports/programmes.xml` is the WXR snapshot of the sample catalogue. Its attachment URLs point at the local authoring site. To recreate the content and image relationships on another installation, use the seeder with the included theme assets.

## Build the editor

In `theme/`, run `npm ci` and `npm run build` with Node.js 22. The compiled bundle and asset manifest in `theme/build/` are committed for installation without Node.js. The frontend uses native JavaScript and the Interactivity API script module.

## Source layout

| Path | Contents |
| --- | --- |
| `theme/inc/` | Content model, routes, editor integration, REST endpoints and log writer |
| `theme/template-parts/` | Programme card, breadcrumbs and registration dialog |
| `theme/src/` and `theme/build/` | React editor source and compiled WordPress Scripts output |
| `theme/assets/` | Site styles, browser scripts and image assets cropped from the supplied references |
| `tools/seed.php` and `exports/` | Repeatable sample catalogue setup and WXR snapshot |

## Implementation decisions

The brief prohibits plugins, so a classic theme owns the content model and views. Programme editing uses WordPress's classic post screen with a custom React metabox so the panel stays visible and saves with the native Update action. The PHP template hierarchy provides the archive and detail entry points. `Aberdeen\render_programme_card( $post_id )` can render the shared card from any template. The listing keeps both programme groups available for the Interactivity API, and the server chooses the initial visible group based on the URL. Direct links and page reloads work without JavaScript.

Programme cards follow the written field list and three, two and one column breakpoints. The flattened mockups informed the desktop composition, colours, imagery and modal states. The two extra slides use existing design imagery. The application link opens the local registration form because the brief supplies no external application destination. Valid form submissions create private enquiries in WordPress for administrators to review. No email or CRM integration is configured.

## Click tracking

Public visitors can post click events to `/wp-json/programmes/v1/track`. The route explicitly permits anonymous calls so navigation and filter clicks can be recorded without sign-in. It limits the JSON body to 2 KiB, enforces the payload formats and discards unknown keys. The browser sends only the pathname and tracking payload. The log contains no lead form fields, cookies or client IP addresses.

The active JSON Lines file is `wp-content/uploads/logs/data-track.log`. The writer serializes appends and rotates the previous UTC day to `tracking-YYYY-MM-DD.log`. Use `aberdeen_tracking_log_path` to change the file location, for example to a dedicated directory outside the web root. The theme generates Apache and IIS deny rules and an index file. For Nginx, add a deny rule for the entire uploads log directory, such as `location ^~ /wp-content/uploads/logs/ { deny all; }`, adjusted to your installation path. The local test server also denies direct requests to this path. Raw logs are shared separately.

## Git workflow

`uat` was created from the initial `main`, `dev` from `uat`, and `feature/programs-module` from `dev`. The assessment commits are on the feature branch. Promote the feature through pull requests from `feature/programs-module` to `dev`, from `dev` to `uat`, then from `uat` to `main`. Each pull request should cover implementation, test steps and known limitations.

## Local verification

Tested with WordPress 7.1.5, PHP 8.2, MariaDB 10.4 and Chromium. Verified home and programme navigation, all three listing filters, browser Back, direct type links with JavaScript disabled, mobile layout, programme detail, live form submission and its confirmation. The editor browser test covered adding, collapsing, reordering and removing a section, selecting an image from the Media Library, and saving and reloading. The click endpoint rejected invalid and oversized bodies, wrote browser-generated events, rotated logs by UTC day and returned HTTP 403 for direct access to the log.

## Plan vs Actual

The design link returned HTTP 403 in the local environment. The supplied ZIP replaced it as the visual reference. The planning artifact is a captioned pre-build walkthrough. The implementation uses a stable active log filename and dated rotations to satisfy both filename requirements. WordPress Scripts uses the project's explicit webpack configuration because the default configuration in the pinned package required a missing helper.

## AI disclosure

OpenAI Codex assisted with planning, implementation, local verification and documentation. See [AI-PROMPTS.md](AI-PROMPTS.md).

## Sample click log

Browser-generated events from the local walkthrough.

```jsonl
{"timestamp":"2026-11-27T09:43:21+00:00","page_url":"/","payload":{"type":"click","event":"button","button":{"label":"next-slide"},"content_data":{"sectionName":"home-hero","uiComponent":"componentButton"}}}
{"timestamp":"2026-11-27T09:43:22+00:00","page_url":"/","payload":{"type":"click","event":"button","button":{"label":"previous-slide"},"content_data":{"sectionName":"home-hero","uiComponent":"componentButton"}}}
{"timestamp":"2026-11-27T09:43:24+00:00","page_url":"/","payload":{"type":"click","event":"link","link":{"label":"study"},"content_data":{"sectionName":"site-header","uiComponent":"componentLink"}}}
{"timestamp":"2026-11-27T09:43:30+00:00","page_url":"/programmes/","payload":{"type":"click","event":"link","link":{"label":"undergraduate"},"content_data":{"sectionName":"programme-listing","uiComponent":"componentFilter"}}}
{"timestamp":"2026-11-27T09:43:34+00:00","page_url":"/programmes/undergraduate/","payload":{"type":"click","event":"link","link":{"label":"postgraduate"},"content_data":{"sectionName":"programme-listing","uiComponent":"componentFilter"}}}
{"timestamp":"2026-11-27T09:43:39+00:00","page_url":"/programmes/undergraduate/","payload":{"type":"click","event":"link","link":{"label":"bachelor-engineering"},"content_data":{"sectionName":"programme-listing","uiComponent":"componentCard"}}}
{"timestamp":"2026-11-27T09:43:46+00:00","page_url":"/programmes/bachelor-engineering/","payload":{"type":"click","event":"link","link":{"label":"apply-now"},"content_data":{"sectionName":"programme-overview","uiComponent":"componentLink"}}}
{"timestamp":"2026-11-27T09:43:47+00:00","page_url":"/programmes/bachelor-engineering/","payload":{"type":"click","event":"button","button":{"label":"close-registration"},"content_data":{"sectionName":"lead-form","uiComponent":"componentButton"}}}
{"timestamp":"2026-11-27T09:43:48+00:00","page_url":"/programmes/bachelor-engineering/","payload":{"type":"click","event":"button","button":{"label":"open-enquiry-form"},"content_data":{"sectionName":"programme-overview","uiComponent":"componentButton"}}}
{"timestamp":"2026-11-27T09:43:50+00:00","page_url":"/programmes/bachelor-engineering/","payload":{"type":"click","event":"button","button":{"label":"submit-registration"},"content_data":{"sectionName":"lead-form","uiComponent":"componentButton"}}}
{"timestamp":"2026-11-27T09:43:56+00:00","page_url":"/programmes/bachelor-engineering/","payload":{"type":"click","event":"link","link":{"label":"back-to-programmes"},"content_data":{"sectionName":"programme-overview","uiComponent":"componentLink"}}}
{"timestamp":"2026-11-27T09:43:59+00:00","page_url":"/programmes/","payload":{"type":"click","event":"link","link":{"label":"home"},"content_data":{"sectionName":"breadcrumbs","uiComponent":"componentLink"}}}
```
