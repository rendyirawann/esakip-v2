window.onload = function() {
  window.ui = SwaggerUIBundle({
    url: "/esakip/doc-api/frontend/web/swagger/swagger.json",
    dom_id: '#swagger-ui',
    deepLinking: true,
    presets: [
      SwaggerUIBundle.presets.apis,
      SwaggerUIStandalonePreset
    ],
    plugins: [
      SwaggerUIBundle.plugins.DownloadUrl
    ],
    // layout: "StandaloneLayout"
    layout: "BaseLayout",
  });
};
