<style>
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

  textarea {
    width: 100%
  }
</style>
<div class="wrap">
  <h1>Tanfolyami Adatok Oldal</h1>
  <form method="post" action="">
    <?php wp_nonce_field('course_page_form_action', 'course_page_form_nonce') ?>
    <input type="hidden" id="course-page-json" name="course_page_json">
    <div id="simple-sections"></div>
    <?php submit_button('Mentés') ?>
  </form>
</div>
<script>
  <?php $course_page_json = get_option('course_page_json') ?: 'null' ?>
  const originalData = '<?php echo $course_page_json ?>'
  const data = JSON.parse(originalData) ?? {
    notifications: [],
    tuitionFees: [],
    examFees: [],
    sections: []
  }

  const simpleSections = [{
      title: 'Értesítések',
      type: 'notifications',
      itemName: 'Értesítés',
      sectionData: data.notifications
    },
    {
      title: 'Tandíjak',
      type: 'tuitionFees',
      itemName: 'Tandíj',
      sectionData: data.tuitionFees
    },
    {
      title: 'Vizsgadíjak',
      type: 'examFees',
      itemName: 'Vizsgadíj',
      sectionData: data.examFees
    }
  ]

  const types = Object.keys(data)
  const typeNames = {
    notifications: 'Értesítés',
    tuitionFees: 'Tandíj',
    examFees: 'Vizsgadíj',
    sections: 'Szekció'
  }
  const jsonInput = document.getElementById('course-page-json')
  const simpleSectionsEl = document.getElementById('simple-sections')
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

  function checkData() {
    for (let i = 0; i < simpleSections.length; i++) {
      const section = simpleSections[i]
      for (let j = 0; j < section.sectionData.length; j++) {
        const prefix = section.name + ` sor ${j + 1}: `
        const r = section.sectionData[j]
        if (!r.name) {
          alert(prefix + `nincsen ${section.itemName} név`)
          return false
        }
        if (!r.description) {
          alert(prefix + `nincsen Leírás`)
          return false
        }
      }
    }

    for (let i = 0; i < data.sections.length; i++) {
      const s = data.sections[i]
      const name = s.name
      const prefix = `Szekciók sor ${i + 1}: `
      if (!name) {
        alert(prefix + 'nincsen Szekció név')
        return false
      }
      if (s.description !== null && s.description === '') {
        alert(prefix + 'üres a Leírás')
      }

      for (let j = 0; j < s.subSections.length; j++) {
        const prefix = `Szekció ${name} sor ${j + 1}: `
        const subS = s.subSections[j]
        if (!subS.name) {
          alert(prefix + 'nincsen Alszekció név')
          return false
        }
        if (!subS.description) {
          alert(prefix + 'nincsen Leírás')
          return false
        }
      }
    }

    return true
  }

  function addDeleteOnClick() {
    types.forEach(type => {
      document.querySelectorAll(`.${type}-delete`).forEach(deleteButton => {
        deleteButton.addEventListener('click', () => {
          const idx = deleteButton.dataset.idx
          const name = data[type][idx].name
          if (name && !confirm(`${typeNames[type]} törlés megerősítése: '${name}'`)) return

          editingId = ''
          data[type].splice(idx, 1)
          render()
        })
      })
    })
  }

  function addNameOnChange() {
    types.forEach(type => {
      document.querySelectorAll(`.${type}-name`).forEach(input => {
        input.addEventListener('change', () => {
          data[type][input.dataset.idx].name = input.value
        })
      })
    })
  }

  function addDescriptionOnChange() {
    types.forEach(type => {
      document.querySelectorAll(`.${type}-description`).forEach(textarea => {
        textarea.addEventListener('change', () => {
          data[type][textarea.dataset.idx].description = textarea.value
        })
      })
    })
  }

  function addDoneOnClick() {
    document.getElementById('done')?.addEventListener('click', () => {
      if (!checkData()) return

      editingId = ''
      render()
    })
  }

  function addEditOnClick() {
    types.forEach(type => {
      document.querySelectorAll(`.${type}-edit`).forEach(editButton => {
        editButton.addEventListener('click', () => {
          if (!checkData()) return

          editingId = type + editButton.dataset.idx
          render()
        })
      })
    })
  }

  function addArrowOnClick() {
    types.forEach(type => {
      document.querySelectorAll(`.${type}-arrow-up`).forEach(arrowUp => {
        arrowUp.addEventListener('click', () => {
          if (!checkData()) return

          const sectionData = data[type]
          const idx = parseInt(arrowUp.dataset.idx)
          const temp = sectionData[idx]
          sectionData[idx] = sectionData[idx - 1]
          sectionData[idx - 1] = temp
          render()
        })
      })

      document.querySelectorAll(`.${type}-arrow-down`).forEach(arrowDown => {
        arrowDown.addEventListener('click', () => {
          if (!checkData()) return

          const d = data[type]
          const idx = parseInt(arrowDown.dataset.idx)
          const temp = d[idx]
          d[idx] = d[idx + 1]
          d[idx + 1] = temp
          render()
        })
      })
    })
  }

  function addSimpleSectionsAddButtonOnClick() {
    simpleSections.forEach(s => {
      document.getElementById(s.type + '-add').addEventListener('click', () => {
        if (!checkData()) return

        editingId = s.type + 0
        data[s.type].splice(0, 0, {
          name: '',
          description: ''
        })

        render()
      })
    })
    
  }

  function addSubSectionDeleteOnClick() {
    document.querySelectorAll(`.sub-section-delete`).forEach(deleteButton => {
      deleteButton.addEventListener('click', () => {
        const secIdx = deleteButton.dataset.secIdx
        const subIdx = deleteButton.dataset.subIdx
        const name = data.sections[secIdx].subSections[subIdx].name
        if (name && !confirm(`Alszekció törlés megerősítése: '${name}'`)) return

        editingId = ''
        data.sections[secIdx].subSections.splice(subIdx, 1)
        renderSubSections()
      })
    })
  }

  function addSubSectionNameOnChange() {
    document.querySelectorAll(`.sub-section-name`).forEach(input => {
      input.addEventListener('change', () => {
        data.sections[input.dataset.secIdx].subSections[input.dataset.subIdx].name = input.value
      })
    })
  }

  function addSubSectionDescriptionOnChange() {
    document.querySelectorAll(`.sub-section-description`).forEach(textarea => {
      textarea.addEventListener('change', () => {
        data.sections[textarea.dataset.secIdx].subSections[textarea.dataset.subIdx].name = textarea.value
      })
    })
  }

  function addSubSectionArrowOnClick() {
    document.querySelectorAll(`.sub-section-arrow-up`).forEach(arrowUp => {
      arrowUp.addEventListener('click', () => {
        if (!checkData()) return

        const s = data.sections[arrowUp.dataset.secIdx].subSections
        const idx = parseInt(arrowUp.dataset.subIdx)
        const temp = s[idx]
        s[idx] = s[idx - 1]
        s[idx - 1] = temp
        render()
      })
    })

    document.querySelectorAll(`.sub-section-arrow-down`).forEach(arrowDown => {
      arrowDown.addEventListener('click', () => {
        if (!checkData()) return

        const s = data.sections[arrowDown.dataset.secIdx].subSections
        const idx = parseInt(arrowDown.dataset.subIdx)
        const temp = s[idx]
        s[idx] = s[idx + 1]
        s[idx + 1] = temp
        render()
      })
    })
  }

  function deleteButton(type, idx) {
    return `<button type="button" data-idx="${idx}" class="button button-secondary ${type}-delete">Törlés</button>`
  }

  function doneButton() {
    return `<button type="button" id="done" class="button button">Kész</button>`
  }

  function editButton(type, idx) {
    return `<button type="button" data-idx="${idx}" class="button button-secondary ${type}-edit">Szerkesztés</button>`
  }

  function arrows(type, idx) {
    const upArrow = idx > 0 ? `<span class="${type}-arrow-up" data-idx="${idx}">${arrowSvg}</span>` : ''
    const downArrow = idx < data[type].length - 1 ? `<span class="${type}-arrow-down" data-idx="${idx}">${arrowSvg}</span>` : ''
    return `
      <div style="display: inline-flex; gap: 0.2rem; align-items: center">
        ${upArrow}
        ${downArrow}
      </div>
    `
  }

  function subSectionArrows(secIdx, subIdx) {
    const upArrow = subIdx > 0 ?
      `<span class="arrow-up sub-section-arrow-up" data-sec-idx="${secIdx}" data-sub-idx="${subIdx}">${arrowSvg}</span>` :
      ''

    const downArrow = subIdx < data[secIdx].subSections.length - 1 ?
      `<span class="arrow-down sub-section-arrow-down" data-sec-idx="${secIdx}" data-sub-idx="${subIdx}">${arrowSvg}</span>` :
      ''

    return `
      <div style="display: inline-flex; gap: 0.2rem; align-items: center">
        ${upArrow}
        ${downArrow}
      </div>
    `
  }

  function simpleSectionTable(section) {
    const type = section.type
    const rows = section.sectionData.map((row, idx) =>
      editingId === type + idx ?
        simpleSectionEditRow(section, row, idx) :
        simpleSectionRow(type, row, idx)
    ).join('')

    return `
      <h2>${section.title}</h2>
      <button id="${type}-add" type="button" class="button button-primary">Hozzáadás</button>
      <table class="form-table">
        <thead>
          <tr>
            <th>#</th>
            <th>${section.itemName} neve</th>
            <th>Leírás</th>
            <th>Műveletek</th>
          </tr>
        </thead>
        <tbody>${rows}</tbody>
      </table>
    `
  }

  function simpleSectionsContent() {
    return simpleSections.map(section => simpleSectionTable(section)).join('')
  }

  function simpleSectionRow(type, row, idx) {
    return `
      <tr>
        <td>${idx + 1}</td>
        <td>${row.name}</td>
        <td>${row.description.replaceAll('\n', '<br>')}</td>
        <td>
          ${editButton(type, idx)}
          ${deleteButton(type, idx)}
          ${arrows(type, idx)}
        </td>
      </tr>
    `
  }

  function simpleSectionEditRow(section, row, idx) {
    const type = section.type
    return `
      <tr>
        <td>${idx + 1}</td>
        <td>
          <input type="text" placeholder="${section.itemName} neve" value="${row.name}" data-idx="${idx}" class="${type}-name" size="40">
        </td>
        <td>
          <textarea placeholder="Leírás" data-idx="${idx}" class="${type}-description">${row.description}</textarea>
        </td>
        <td>
          ${doneButton()}
          ${deleteButton(type, idx)}
        </td>
      </tr>
    `
  }

  function render() {
    simpleSectionsEl.innerHTML = simpleSectionsContent()
    addDeleteOnClick()
    addNameOnChange()
    addDescriptionOnChange()
    addDoneOnClick()
    addEditOnClick()
    addArrowOnClick()
    addSimpleSectionsAddButtonOnClick()
  }
  render()

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