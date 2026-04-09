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

  // --- LÓGICA DE BÚSQUEDA AJAX (Autoridad e Iconos) ---
  const ajaxWrappers = document.querySelectorAll('.ajax-search-wrapper, .ajax-icon-search-wrapper');

  ajaxWrappers.forEach(wrapper => {
    const isIconSearch = wrapper.classList.contains('ajax-icon-search-wrapper');
    const input = wrapper.querySelector('.ajax-search-input');
    const resultsContainer = wrapper.querySelector('.ajax-search-results');
    const hiddenIdInput = wrapper.querySelector('.ajax-search-hidden-id');
    const selectedView = wrapper.querySelector('.selected-item-view');
    const searchView = wrapper.querySelector('.search-input-view');
    const selectedTitle = wrapper.querySelector('.selected-item-title');
    const clearBtn = wrapper.querySelector('.clear-selection-btn');
    const action = wrapper.getAttribute('data-action');
    let timeoutId;

    if (!input || !action) return;

    input.addEventListener('input', function() {
      clearTimeout(timeoutId);
      const query = input.value.trim();

      if (query.length < 2) {
        resultsContainer.innerHTML = '';
        resultsContainer.style.display = 'none';
        return;
      }

      timeoutId = setTimeout(() => {
        resultsContainer.innerHTML = '<div class="ajax-search-item">Buscando...</div>';
        resultsContainer.style.display = 'block';

        const data = new URLSearchParams();
        data.append('action', action);
        data.append('search', query);
        data.append('nonce', window.viceunf_admin_vars ? window.viceunf_admin_vars.nonce : ''); // Requiere viceunf localize si aplica

        fetch(ajaxurl, {
          method: 'POST',
          body: data
        })
        .then(res => res.json())
        .then(response => {
          resultsContainer.innerHTML = '';
          if (response.success && response.data.length > 0) {
            response.data.forEach(item => {
              const div = document.createElement('div');
              div.className = 'ajax-search-item';
              if (isIconSearch) {
                  div.innerHTML = `<i class="${item.id}" style="margin-right:10px;"></i> ${item.id} <small>(${item.type})</small>`;
              } else {
                  div.innerHTML = item.title;
              }
              
              div.addEventListener('click', () => {
                hiddenIdInput.value = item.id;
                
                if (isIconSearch) {
                    const existingI = selectedView.querySelector('i');
                    if(existingI) existingI.className = item.id;
                    selectedTitle.innerText = item.id;
                } else {
                    selectedTitle.innerText = item.title;
                }

                searchView.classList.remove('active');
                selectedView.classList.add('active');
                resultsContainer.style.display = 'none';
                input.value = '';
              });
              resultsContainer.appendChild(div);
            });
          } else {
            resultsContainer.innerHTML = '<div class="ajax-search-item">No se encontraron resultados.</div>';
          }
        })
        .catch(() => {
           resultsContainer.innerHTML = '<div class="ajax-search-item">Error en la búsqueda.</div>';
        });
      }, 500);
    });

    clearBtn.addEventListener('click', function() {
      hiddenIdInput.value = '';
      selectedView.classList.remove('active');
      searchView.classList.add('active');
    });

    // Cerrar resultados al dar clic fuera
    document.addEventListener('click', function(e) {
      if (!wrapper.contains(e.target)) {
        resultsContainer.style.display = 'none';
      }
    });

    input.addEventListener('focus', function() {
      if (resultsContainer.innerHTML !== '') {
        resultsContainer.style.display = 'block';
      }
    });
  });

});
