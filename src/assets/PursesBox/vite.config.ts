import { defineConfig } from "vite";
import { svelte } from "@sveltejs/vite-plugin-svelte";
import { resolve } from "path";

export default defineConfig(({
  plugins: [
    svelte({
      compilerOptions: {
        css: "injected",
      },
    }),
  ],
  resolve: {
    alias: {
      "@": resolve(__dirname, "./src"),
    },
    conditions: ["browser", "import", "module"],
  },
  build: {
    ssr: false,
    lib: {
      entry: resolve(__dirname, "src/main.ts"),
      name: "PursesBox",
      formats: ["iife"],
      fileName: () => "purses-box.js",
    },
    outDir: "build",
    emptyOutDir: true,
  },
}));
