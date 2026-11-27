# WordPress practical assessment

## Summary

- Build a responsive WordPress university site from the four Figma frames.
- Model programmes and programme types in WordPress so editors can manage the catalogue.
- Give editors a React panel for academic year, duration and ordered content sections with media.
- Serve the grouped listing, type archives and programme details at the specified permalinks.
- Enhance the listing with the WordPress Interactivity API while keeping the links usable without JavaScript.
- Build and test the lead form and thank-you state, and record validated click events in a protected log.
- Verify the assignment locally before promoting it through Git pull requests and recording the functionality demo.

## Work order and estimates

| Work | Estimate |
| --- | ---: |
| Review brief and Figma, prepare this document and the pre-build walkthrough | 45 min |
| Set up local WordPress and theme, register the content model and prepare sample content | 60 min |
| Build and verify the React editor panel | 75 min |
| Implement Figma screens, routes, cards and detail templates | 120 min |
| Add interactive filtering, form flow and click tracking | 75 min |
| Test on a clean database, check desktop and mobile, export data and log samples | 75 min |
| Finalize docs, promote branches and record the functionality demo | 30 min |
| **Total** | **8 hr** |

## Architecture

The explicit rules prohibit custom and third-party plugins. A classic theme with PHP templates fits this brief because the custom archive, cards and detail view can all share native template parts. The theme will also own the content model and REST endpoints for this assessment. WordPress stores programmes and attachments in its posts tables, programme types in its taxonomy tables, and the registered fields in post meta.

```text
theme/
  inc/             Content registration, routing, editor and REST handlers
  template-parts/  Programme cards, breadcrumbs and lead form
  src/             React and Interactivity API sources
  build/           Compiled deliverables
  assets/          Theme styles and design assets
exports/           Sample content export
```

## Technical approach

**Build.** Use native theme templates, hierarchical programme types and registered REST-visible post meta. Build the React panel with WordPress packages and compile it with WordPress Scripts. Use the media library for featured and section images. Reserve the type archive slugs and seed five programmes of each type into a clean local database.

**Click tracking.** Render the documented JSON through one escaped PHP helper. Delegate clicks and send them asynchronously to a REST endpoint with a bounded body and strict field validation. Append validated records under a file lock, with server timestamps and local page paths. Keep form fields, credentials and raw logs out of Git.

**Interactivity.** Render working listing and archive links on the server. Use an Interactivity API store to manage the selected type, loading and empty states, and History API updates. Check direct loads, refresh and browser back and forward.

## Assumptions and questions

- The supplied ZIP contains six flattened design references, including both programme levels and the slideshow hover state. Use the supplied screenshots to match layout, colours and states, and extract only individual visual assets where feasible.
- The programme mockups show two cards with description text, while the module explicitly requires a 3/2/1 grid and limits card fields. Use the mockup's colours and proportions with the required layout and fields.
- The rules explicitly prohibit plugins, while the planning and deliverables sections mention them. I will implement the module entirely in the theme.
- The log instructions ask for both daily files and a fixed filename. I will treat uploads/logs/data-track.log as the current log and use dated files for rotation. The server will deny direct HTTP access to the directory.
- Where should enquiries go? In the absence of a supplied integration, I will use a locally testable submission flow and show the thank-you state after a successful response. Click logs will contain no lead fields.
- The brief gives an eight-hour time box. The email supplied here has no calendar cutoff.

## Risks

Design access and clean-machine setup may take longer than planned. Prioritize the required screens, native editor, navigation, filtering and logs. Defer optional caching, schema markup and scheduled housekeeping first.

## Walkthrough outline

Explain the four Figma deliverables and where the shared programme card appears. Show how editors will create programmes, assign a type and reorder media-backed sections. Walk through the four route patterns, the enhanced filter and its plain-link fallback, and the lead form and confirmation. Trace a click from its HTML attribute through validation to the protected JSON Lines log. Close with the local verification plan, the three pull requests and the two brief ambiguities documented above.
