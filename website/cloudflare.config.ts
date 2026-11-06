import { defineConfig } from "cf/config";

/**
 * Production is explicit; Preview builds never inherit the hostname route.
 * The hub owns the hostname: this Worker only claims its own prefix.
 */
export default defineConfig(({ mode, isPreview }) => {
  const production = mode === "microsite-production" && !isPreview;
  return {
    worker: {
      name: production
        ? "flat-file-microsite"
        : "flat-file-microsite-preview",
      compatibilityDate: "2026-11-02",
      workersDev: !production,
      assets: {
        htmlHandling: "force-trailing-slash",
        notFoundHandling: "404-page",
      },
      triggers: production
        ? [
            {
              type: "fetch",
              pattern: "projects.logsdon.io/flat-file/*",
              zone: "logsdon.io",
            },
          ]
        : [],
    },
  };
});
