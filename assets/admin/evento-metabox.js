document.addEventListener("DOMContentLoaded", function () {
  const container = document.getElementById("evento_horarios_container");
  const addBtn = document.getElementById("add_horario_btn");
  if (!container || !addBtn) return;

  let counter = container.querySelectorAll(".horario-row").length;

  addBtn.addEventListener("click", function (e) {
    e.preventDefault();
    const row = document.createElement("div");
    row.className = "horario-row viceunf-metabox-repeater-row";
    row.innerHTML = `
            <div class="horario-cols" style="display:flex; gap:15px; align-items:flex-end;">
                <div class="col" style="flex:1.5;">
                    <label class="viceunf-metabox-label">Etiqueta / Turno:</label>
                    <input type="text" name="evento_horarios[${counter}][etiqueta]" placeholder="Ej: Turno Mañana" class="viceunf-metabox-input dt-w-100" />
                </div>
                <div class="col" style="flex:1;">
                    <label class="viceunf-metabox-label">Fecha:</label>
                    <input type="date" name="evento_horarios[${counter}][fecha]" required class="viceunf-metabox-input dt-w-100" />
                </div>
                <div class="col" style="flex:1;">
                    <label class="viceunf-metabox-label">Hora Inicio:</label>
                    <input type="time" name="evento_horarios[${counter}][inicio]" required class="viceunf-metabox-input dt-w-100" />
                </div>
                <div class="col" style="flex:1;">
                    <label class="viceunf-metabox-label">Hora Fin:</label>
                    <input type="time" name="evento_horarios[${counter}][fin]" required class="viceunf-metabox-input dt-w-100" />
                </div>
                <div class="col col-btn">
                    <button type="button" class="button remove-horario" style="color:#b32d2e; border-color:#b32d2e;">Eliminar</button>
                </div>
            </div>
        `;
    container.appendChild(row);
    counter++;
  });

  container.addEventListener("click", function (e) {
    if (e.target && e.target.classList.contains("remove-horario")) {
      e.preventDefault();
      e.target.closest(".horario-row").remove();
    }
  });
});
