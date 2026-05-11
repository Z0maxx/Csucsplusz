<style>
  tbody tr:nth-child(odd) {
    background-color: lightgray
  }

  tbody tr:nth-child(even) {
    background-color: silver;
  }

  table {
    table-layout: fixed;
  }

  th:first-child,
  td:first-child {
    width: 30px
  }

  th:nth-child(2),
  td:nth-child(2) {
    width: 320px
  }

  th:nth-child(3),
  td:nth-child(3) {
    width: 80px
  }

  th:nth-child(4),
  td:nth-child(4) {
    width: 140px
  }

  th:nth-child(5),
  td:nth-child(5) {
    width: 470px
  }

  th:nth-child(6),
  td:nth-child(6) {
    width: 300px
  }

  .form-table {
    width: 1340px;
  }

  #arrow-svg {
    width: 0.8rem;
    height: 0.8rem;
  }

  .arrow-up,
  .arrow-down {
    color: #2271b1;
    background: #f6f7f7;
    width: 30px;
    height: 30px;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #2271b1;
    cursor: pointer
  }

  .arrow-up {
    rotate: 180deg
  }

  tbody>tr:hover {
    outline: 1px solid black
  }
</style>
<div class="wrap">
  <h1>Képzés Beállítások</h1>
  <form method="post" action="">
    <?php wp_nonce_field('training_form_action', 'training_form_nonce') ?>
    <h2>Nincsen képzés szöveg</h2>
    <input type="text" size="80" name="no_training_start_text" id="no-training-start-text">
    <input type="hidden" id="trainings-json" name="trainings_json">
    <h2>Képzések</h2>
    <button id="add" type="button" class="button button-primary">Hozzáadás</button>
    <table class="form-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Képzés neve</th>
          <th>Indulás dátumhoz kötött</th>
          <th>Indulás dátuma</th>
          <th>Leírás</th>
          <th>Műveletek</th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
        <tr>
          <td><?php submit_button('Mentés') ?></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script>
  const descriptionEditor = `
  <?php
  wp_editor(
    '',
    'description-editor',
    array(
      'media_buttons' => false,
      'textarea_rows' => 10,
      'quicktags' => false,
    )
  )
  ?>
  `

  <?php $trainings_json = get_option('trainings_json') ?: 'null' ?>
  const originalData = '<?php echo $trainings_json ?>'
  const data = JSON.parse(originalData) ?? {
    noTrainingStartText: '',
    trainings: []
  }

  const trainings = data.trainings
  const deleted = new Set()
  const trainingsEl = document.querySelector('tbody')
  const noTrainingStartTextInput = document.getElementById('no-training-start-text')
  const jsonInput = document.getElementById('trainings-json')
  noTrainingStartTextInput.value = data.noTrainingStartText

  const arrowSvg = `
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 -4.5 20 20" version="1.1" id="arrow-svg">
      <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <g id="Dribbble-Light-Preview" transform="translate(-220.000000, -6684.000000)" fill="#2271b1">
          <g id="icons" transform="translate(56.000000, 160.000000)">
            <path d="M164.292308,6524.36583 L164.292308,6524.36583 C163.902564,6524.77071 163.902564,6525.42619 164.292308,6525.83004 L172.555873,6534.39267 C173.33636,6535.20244 174.602528,6535.20244 175.383014,6534.39267 L183.70754,6525.76791 C184.093286,6525.36716 184.098283,6524.71997 183.717533,6524.31405 C183.328789,6523.89985 182.68821,6523.89467 182.29347,6524.30266 L174.676479,6532.19636 C174.285736,6532.60124 173.653152,6532.60124 173.262409,6532.19636 L165.705379,6524.36583 C165.315635,6523.96094 164.683051,6523.96094 164.292308,6524.36583" id="arrow_down-[#338]"></path>
          </g>
        </g>
      </g>
    </svg>
  `

  let editingId = ''

  function html(text) {
    return text.replaceAll('&lt;', '<').replaceAll('&gt;', '>')
  }

  function encodedHtml(text) {
    return text.replaceAll('<', '&lt;').replaceAll('>', '&gt;')
  }

  function checkData() {
    if (!editingId) return true

    for (let i = 0; i < trainings.length; i++) {
      const t = trainings[i]
      const prefix = `Sor ${i + 1}: `
      if (!t.name) {
        alert(prefix + 'nincsen Képzés név')
        return false
      }
    }

    return true
  }

  function addDeleteOnClick() {
    document.querySelectorAll(`.delete`).forEach(deleteButton => {
      deleteButton.addEventListener('click', () => {
        const idx = deleteButton.dataset.idx
        const name = trainings[idx].name
        if (name && !confirm(`Törlés megerősítése: '${name}'`)) return

        editingId = ''
        deleted.add(name)
        trainings.splice(idx, 1)
        renderTrainings()
      })
    })
  }

  function addNameOnChange() {
    document.querySelectorAll(`.name`).forEach(input => {
      input.addEventListener('change', () => {
        trainings[input.dataset.idx].name = input.value
      })
    })
  }

  function addHasStartDateOnChange() {
    document.querySelectorAll(`.has-start-date`).forEach(input => {
      input.addEventListener('change', () => {
        console.log(input.checked)
        trainings[input.dataset.idx].hasStartDate = input.checked
        renderTrainings()
      })
    })
  }

  function addStartDateOnChange() {
    document.querySelectorAll(`.start-date`).forEach(input => {
      input.addEventListener('change', () => {
        trainings[input.dataset.idx].startDate = input.value
      })
    })
  }

  function setupDescriptionEditor() {
    if (editingId) {
      document.getElementById('description-editor').innerText = html(trainings[editingId].description)
      window.tinyMCE.init({
        selector: '#description-editor',
        width: '100%',
        menubar: false,
        plugins: 'lists link fullscreen',
        toolbar: `
          undo redo |
          bold italic underline strikethrough |
          bullist numlist |
          link
        `
      })
    }
  }

  function setDescription() {
    window.tinyMCE.triggerSave()
    const editor = window.tinyMCE.get('description-editor')
    trainings[editingId].description = encodedHtml(window.tinyMCE.get('description-editor').getContent())
    editor.remove()
  }

  function addCopyOnClick() {
    document.querySelectorAll(`.copy`).forEach(copyButton => {
      copyButton.addEventListener('click', () => {
        navigator.clipboard.writeText(`[képzés="${trainings[copyButton.dataset.idx].name}"]`)
      })
    })
  }

  function addDoneOnClick() {
    document.getElementById('done')?.addEventListener('click', () => {
      if (!checkData()) return

      setDescription()
      editingId = ''
      renderTrainings()
    })
  }

  function addEditOnClick() {
    document.querySelectorAll(`.edit`).forEach(editButton => {
      editButton.addEventListener('click', () => {
        if (!checkData()) return

        editingId = editButton.dataset.idx
        renderTrainings()
      })
    })
  }

  function addArrowOnClick() {
    document.querySelectorAll(`.arrow-up`).forEach(arrowUp => {
      arrowUp.addEventListener('click', () => {
        if (!checkData()) return

        const idx = parseInt(arrowUp.dataset.idx)
        const temp = trainings[idx]
        trainings[idx] = trainings[idx - 1]
        trainings[idx - 1] = temp
        renderTrainings()
      })
    })

    document.querySelectorAll(`.arrow-down`).forEach(arrowDown => {
      arrowDown.addEventListener('click', () => {
        if (!checkData()) return

        const idx = parseInt(arrowDown.dataset.idx)
        const temp = trainings[idx]
        trainings[idx] = trainings[idx + 1]
        trainings[idx + 1] = temp
        renderTrainings()
      })
    })
  }

  function copyButton(idx) {
    return `<button type="button" data-idx="${idx}" class="button button-primary copy">Másolás</button>`
  }

  function deleteButton(idx) {
    return `<button type="button" data-idx="${idx}" class="button button-secondary delete">Törlés</button>`
  }

  function doneButton() {
    return `<button type="button" id="done" class="button button">Kész</button>`
  }

  function editButton(idx) {
    return `<button type="button" data-idx="${idx}" class="button button-secondary edit">Szerkesztés</button>`
  }

  function arrows(idx) {
    const upArrow = idx > 0 ? `<span class="arrow-up" data-idx="${idx}">${arrowSvg}</span>` : ''
    const downArrow = idx < trainings.length - 1 ? `<span class="arrow-down" data-idx="${idx}">${arrowSvg}</span>` : ''
    return `
      <div style="display: inline-flex; gap: 0.2rem; align-items: center">
        ${upArrow}
        ${downArrow}
      </div>
    `
  }

  function content() {
    return trainings.map((t, idx) =>
      editingId === idx.toString() ? editRow(t, idx) : row(t, idx)
    ).join('')
  }

  function row(t, idx) {
    return `
      <tr>
        <td>${idx + 1}</td>
        <td>${t.name}</td>
        <td>${t.hasStartDate ? 'Igen' : 'Nem'}</td>
        <td>${t.startDate}</td>
        <td>${html(t.description)}</td>
        <td>
          ${copyButton(idx)}
          ${editButton(idx)}
          ${deleteButton(idx)}
          ${arrows(idx)}
        </td>
      </tr>
    `
  }

  function editRow(t, idx) {
    const startDateInput = t.hasStartDate ? `<input type="date" value="${t.startDate}" data-idx="${idx}" class="start-date">` : ''
    return `
      <tr>
        <td>${idx + 1}</td>
        <td>
          <input type="text" placeholder="Képzés neve" value="${t.name}" data-idx="${idx}" class="name" size="40">
        </td>
        <td>
          <input type="checkbox" ${t.hasStartDate ? 'checked' : ''} data-idx="${idx}" class="has-start-date">
        </td>
        <td>${startDateInput}</td>
        <td>${descriptionEditor}</td>
        <td>
          ${doneButton()}
          ${deleteButton(idx)}
        </td>
      </tr>
    `
  }

  function renderTrainings() {
    trainingsEl.innerHTML = content()
    addDeleteOnClick()
    addCopyOnClick()
    addNameOnChange()
    addHasStartDateOnChange()
    addStartDateOnChange()
    addDoneOnClick()
    addEditOnClick()
    addArrowOnClick()
    setupDescriptionEditor()
  }
  renderTrainings()

  noTrainingStartTextInput.addEventListener('input', () => {
    data.noTrainingStartText = noTrainingStartTextInput.value
  })

  document.getElementById('add').addEventListener('click', () => {
    if (!checkData()) return

    editingId = '0'
    trainings.splice(0, 0, {
      name: '',
      hasStartDate: false,
      startDate: '',
      description: ''
    })

    renderTrainings()
  })

  let submitted = false
  document.querySelector('form').addEventListener('submit', (e) => {
    trainings.deleted = Array.from(deleted)
    if (!data.noTrainingStartText) {
      e.preventDefault()
      alert('Üres a Nincsen képzés szöveg mező')
      return
    }

    if (editingId || !checkData()) {
      e.preventDefault()
      if (editingId) {
        alert('Szerkesztés közben nem lehet menteni')
      }

      return
    }

    submitted = true
    jsonInput.value = JSON.stringify(data)
  })

  window.addEventListener('beforeunload', (e) => {
    if (!submitted && originalData !== JSON.stringify(data)) {
      e.preventDefault()
      e.returnValue = true
    }
  })
</script>