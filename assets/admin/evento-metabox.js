document.addEventListener("DOMContentLoaded", function () {
  const container = document.getElementById("evento_horarios_container");
  const addBtn = document.getElementById("add_horario_btn");
  if (!container || !addBtn) return;

  let counter = container.querySelectorAll(".horario-row").length;

  addBtn.addEventListener("click", function (e) {
    e.preventDefault();
    const row = document.createElement("div");
    row.className = "horario-row";
    row.innerHTML = `
            <div class="horario-cols">
                <div class="col">
                    <label>Etiqueta / Turno:</label>
                    <input type="text" name="evento_horarios[${counter}][etiqueta]" placeholder="Ej: Turno Mañana" />
                </div>
                <div class="col">
                    <label>Fecha:</label>
                    <input type="date" name="evento_horarios[${counter}][fecha]" required />
                </div>
                <div class="col">
                    <label>Hora Inicio:</label>
                    <input type="time" name="evento_horarios[${counter}][inicio]" required />
                </div>
                <div class="col">
                    <label>Hora Fin:</label>
                    <input type="time" name="evento_horarios[${counter}][fin]" required />
                </div>
                <div class="col col-btn">
                    <button type="button" class="button remove-horario">Eliminar</button>
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
