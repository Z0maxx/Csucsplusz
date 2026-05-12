<style>
  table {
    max-width: 800px
  }

  tbody tr:nth-child(odd) {
    background-color: lightgray
  }

  tbody tr:nth-child(even) {
    background-color: silver;
  }

  th:first-child,
  td:first-child {
    width: 20px !important;
    max-width: 20px
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
  <h1>Okatók</h1>
  <button id="add" type="button" class="button button-primary">Hozzáadás</button>
  <form method="post" action="">
    <?php wp_nonce_field('instructors_form_action', 'instructors_form_nonce') ?>
    <input type="hidden" id="instructors-json" name="instructors_json">
    <table class="form-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Oktató neve</th>
          <th>Oktató titulusai</th>
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
  <?php $instructors_json = get_option('instructors_json') ?: 'null' ?>
  const originalInstructors = '<?php echo $instructors_json ?>'
  const instructors = JSON.parse(originalInstructors) ?? []
  const deleted = new Set()
  const instructorsEl = document.querySelector('tbody')
  const jsonInput = document.getElementById('instructors-json')
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
  let fromChange = false
  let clickedAt

  window.addEventListener('click', (e) => {
    clickedAt = {
      clientX: e.clientX,
      clientY: e.clientY
    }
  })

  function checkData() {
    if (!editingId) return true

    for (let i = 0; i < instructors.length; i++) {
      const instr = instructors[i]
      const prefix = `Sor ${i + 1}: `
      if (!instr.name) {
        alert(prefix + 'nincsen Okatató név')
        return false
      }

      if (!instr.titles) {
        alert(prefix + 'nincsen Oktató titulus')
        return false
      }
    }

    return true
  }

  function addDeleteOnClick() {
    document.querySelectorAll(`.delete`).forEach(deleteButton => {
      deleteButton.addEventListener('click', () => {
        const idx = deleteButton.dataset.idx
        const name = instructors[idx].name
        if (name && !confirm(`Törlés megerősítése: '${name}'`)) return

        editingId = ''
        deleted.add(name)
        instructors.splice(idx, 1)
        renderInstructors()
      })
    })
  }

  function addNameOnChange() {
    document.querySelectorAll(`.name`).forEach(input => {
      input.addEventListener('change', () => {
        instructors[input.dataset.idx].name = input.value
      })
    })
  }

  function addTitlesOnChange() {
    document.querySelectorAll(`.titles`).forEach(textarea => {
      textarea.addEventListener('change', () => {
        instructors[textarea.dataset.idx].titles = textarea.value
      })
    })
  }

  function addDoneOnClick() {
    document.getElementById('done')?.addEventListener('click', () => {
      if (!checkData()) return

      editingId = ''
      renderInstructors()
    })
  }

  function addEditOnClick() {
    document.querySelectorAll(`.edit`).forEach(editButton => {
      editButton.addEventListener('click', () => {
        if (!checkData()) return

        editingId = editButton.dataset.idx
        renderInstructors()
      })
    })
  }

  function addArrowOnClick() {
    document.querySelectorAll(`.arrow-up`).forEach(arrowUp => {
      arrowUp.addEventListener('click', () => {
        if (!checkData()) return

        editingId = ''
        const idx = parseInt(arrowUp.dataset.idx)
        const temp = instructors[idx]
        instructors[idx] = instructors[idx - 1]
        instructors[idx - 1] = temp
        renderInstructors()
      })
    })

    document.querySelectorAll(`.arrow-down`).forEach(arrowDown => {
      arrowDown.addEventListener('click', () => {
        if (!checkData()) return

        editingId = ''
        const idx = parseInt(arrowDown.dataset.idx)
        const temp = instructors[idx]
        instructors[idx] = instructors[idx + 1]
        instructors[idx + 1] = temp
        renderInstructors()
      })
    })
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
    const downArrow = idx < instructors.length - 1 ? `<span class="arrow-down" data-idx="${idx}">${arrowSvg}</span>` : ''
    return `
      <div style="display: inline-flex; gap: 0.2rem; align-items: center">
        ${upArrow}
        ${downArrow}
      </div>
    `
  }

  function content() {
    return instructors.map((t, idx) =>
      editingId === idx.toString() ? editRow(t, idx) : row(t, idx)
    ).join('')
  }

  function row(instr, idx) {
    const titles = instr.titles.split('\n').map(t => `<li>${t}</li>`).join('')
    return `
      <tr>
        <td>${idx + 1}</td>
        <td>${instr.name}</td>
        <td>
          <ul>
            ${titles}
          </ul>
        </td>
        <td>
          ${editButton(idx)}
          ${deleteButton(idx)}
          ${arrows(idx)}
        </td>
      </tr>
    `
  }

  function editRow(instr, idx) {
    return `
      <tr>
        <td>${idx + 1}</td>
        <td>
          <input type="text" placeholder="Óktató neve" value="${instr.name}" data-idx="${idx}" class="name">
        </td>
        <td>
          <textarea placeholder="Titulusok" data-idx="${idx}" class="titles">${instr.titles}</textarea>
        </td>
        <td>
          ${doneButton()}
          ${deleteButton(idx)}
        </td>
      </tr>
    `
  }

  function renderInstructors() {
    function render() {
      instructorsEl.innerHTML = content()
      addDeleteOnClick()
      addNameOnChange()
      addTitlesOnChange()
      addDoneOnClick()
      addEditOnClick()
      addArrowOnClick()
    }

    setTimeout(() => {
      if (!clickedAt || !fromChange) {
        render()
        return
      }

      render()
      const {
        clientX,
        clientY
      } = clickedAt

      const el = document.elementFromPoint(clientX, clientY);
      if (el.tagName === 'INPUT') {
        el.focus()
      }

      fromChange = false
      clickedAt = null
    }, 70)
  }
  renderInstructors()

  document.getElementById('add').addEventListener('click', () => {
    if (!checkData()) return

    editingId = '0'
    instructors.splice(0, 0, {
      name: '',
      titles: ''
    })

    renderInstructors()
  })

  let submitted = false
  document.querySelector('form').addEventListener('submit', (e) => {
    instructors.deleted = Array.from(deleted)
    if (editingId || !checkData()) {
      e.preventDefault()
      if (editingId) {
        alert('Szerkesztés közben nem lehet menteni')
      }

      return
    }

    submitted = true
    jsonInput.value = JSON.stringify(instructors)
  })

  window.addEventListener('beforeunload', (e) => {
    if (!submitted && originalInstructors !== JSON.stringify(instructors)) {
      e.preventDefault()
      e.returnValue = true
    }
  })
</script>