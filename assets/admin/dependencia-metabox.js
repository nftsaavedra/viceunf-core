document.addEventListener("DOMContentLoaded", function () {
  const sourceRadios = document.querySelectorAll(
    'input[name="dependencia_resolucion_source_type"]',
  );
  const uploadSection = document.getElementById(
    "dependencia-upload-section",
  );
  const externalSection = document.getElementById(
    "dependencia-external-section",
  );
  const uploadBtn = document.getElementById("upload_resolucion_button");
  const removeBtn = document.getElementById("remove_resolucion_button");
  const fileIdInput = document.getElementById("dependencia_resolucion_file_id");
  const fileDisplayContainer = document.getElementById("dependencia-filename-display");
  const filenameText = document.getElementById("dependencia-filename-text");

  if (!uploadSection || !externalSection) return;

  function toggleSections() {
    const checked = document.querySelector(
      'input[name="dependencia_resolucion_source_type"]:checked',
    );
    const val = checked ? checked.value : "upload";
    uploadSection.style.display = val === "upload" ? "" : "none";
    externalSection.style.display = val === "external" ? "" : "none";
  }

  sourceRadios.forEach(function (radio) {
    radio.addEventListener("change", toggleSections);
  });
  toggleSections();

  // WP Media Library (wp.media es core de WP, no depende de jQuery para la API pública)
  let mediaFrame = null;

  if (uploadBtn) {
    uploadBtn.addEventListener("click", function (e) {
      e.preventDefault();

      if (mediaFrame) {
        mediaFrame.open();
        return;
      }

      mediaFrame = wp.media({
        title: "Seleccionar Archivo de Resolución",
        button: { text: "Usar este archivo" },
        multiple: false,
        library: {
          type: [
            "application/pdf",
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
          ],
        },
      });

      mediaFrame.on("select", function () {
        const attachment = mediaFrame.state().get("selection").first().toJSON();
        fileIdInput.value = attachment.id;
        
        if (filenameText) {
          filenameText.href = attachment.url;
          filenameText.innerText = attachment.filename;
        }
        if (fileDisplayContainer) fileDisplayContainer.style.display = 'block';

        if (removeBtn) removeBtn.style.display = "";
      });

      mediaFrame.open();
    });
  }

  if (removeBtn) {
    removeBtn.addEventListener("click", function (e) {
      e.preventDefault();
      fileIdInput.value = "";
      
      if (fileDisplayContainer) fileDisplayContainer.style.display = 'none';
      if (filenameText) {
        filenameText.href = '#';
        filenameText.innerText = '';
      }
      
      removeBtn.style.display = "none";
    });
  }
});
