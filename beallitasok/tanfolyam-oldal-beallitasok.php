<style>
  iframe {
    width: 100%;
  }

  ul li::marker {
    content: '- '
  }

  li ul li::marker {
    content: '> '
  }

  li ul li ul li::marker {
    content: '* '
  }

  table {
    table-layout: fixed;
    max-width: 1350px;
  }

  tbody tr:nth-child(odd) {
    background-color: lightgray
  }

  tbody tr:nth-child(even) {
    background-color: silver;
  }

  th:first-child,
  td:first-child {
    width: 30px;
  }

  th:nth-child(2),
  td:nth-child(2) {
    width: 320px
  }

  th:nth-child(3),
  td:nth-child(3) {
    width: 700px
  }

  th:nth-child(4),
  td:nth-child(4) {
    width: 300px
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

  .complex-section {
    background-color: powderblue;
    padding: 15px;
    margin: 15px 0;
    overflow-x: auto
  }

  tbody>tr:hover {
    outline: 1px solid black
  }
</style>
<div class="wrap">
  <h1>Tanfolyami Adatok Oldal</h1>
  <form method="post" action="">
    <?php wp_nonce_field('course_page_form_action', 'course_page_form_nonce') ?>
    <input type="hidden" id="course-page-json" name="course_page_json">
    <div id="simple-sections"></div>
    <div>
      <h2>Szekciók</h2>
      <button type="button" id="complex-section-add" class="button button-primary">Hozzáadás</button>
      <div id="complex-sections"></div>
    </div>
    <?php submit_button('Mentés') ?>
  </form>
</div>
<script>
  const cssUrl = '<?php echo get_stylesheet_uri() ?>'
  const descriptionEditor = `
  <?php
  wp_editor(
    '',
    'description-editor',
    array(
      'media_buttons' => false,
      'textarea_rows' => 5,
      'quicktags' => false,
    )
  )
  ?>`

  <?php $course_page_json = get_option('course_page_json') ?: 'null' ?>
  const originalData = '<?php echo $course_page_json ?>'
  const data = JSON.parse(originalData) ?? {
    notifications: [],
    tuitionFees: [],
    examFees: [],
    sections: []
  }

  const complexSections = data.sections
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
  const complexSectionsEl = document.getElementById('complex-sections')
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

  const htmlCodes = [
    ['<', '&lt;'],
    ['>', '&gt;'],
    ['"', '&quot;'],
  ]

  let editingId = ''

  function html(text) {
    htmlCodes.forEach(c => text = text.replaceAll(c[1], c[0]))
    return text
  }

  function encodedHtml(text) {
    htmlCodes.forEach(c => text = text.replaceAll(c[0], c[1]))
    return text
  }

  function checkData(checkComplexSectionIsEmpty = false) {
    if (!editingId && !checkComplexSectionIsEmpty) return true

    if (editingId) {
      setDescription()
    }

    for (let i = 0; i < simpleSections.length; i++) {
      const section = simpleSections[i]
      for (let j = 0; j < section.sectionData.length; j++) {
        const prefix = section.title + ` sor ${j + 1}: `
        const r = section.sectionData[j]
        if (!r.name) {
          alert(prefix + `nincsen ${section.itemName} név`)
          return false
        }
        if (r.description === '') {
          alert(prefix + `nincsen Leírás`)
          return false
        }
      }
    }

    for (let i = 0; i < complexSections.length; i++) {
      const s = complexSections[i]
      const name = s.name
      const prefix = `Szekciók sor ${i + 1}: `
      if (!name) {
        alert(prefix + 'nincsen Szekció név')
        return false
      }

      console.log(checkComplexSectionIsEmpty, s.subSections.length, s.hasDescription)
      if (checkComplexSectionIsEmpty && s.subSections.length === 0 && !s.hasDescription) {
        alert(`Szekció ${name}: nincsen Leírása vagy Alszekciói`)
        return false
      }

      for (let j = 0; j < s.subSections.length; j++) {
        const prefix = `Szekció ${name} sor ${j + 1}: `
        const subS = s.subSections[j]
        if (!subS.name) {
          alert(prefix + 'nincsen Alszekció név')
          return false
        }
        if (!subS.description) {
          console.log(subS)
          alert(prefix + 'nincsen Leírás')
          return false
        }
      }
    }

    removeDesciptionEditor()
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

  function setupDescriptionEditor() {
    if (!editingId) return

    const params = editingId.split('-')
    let description = ''
    if (params.length === 2) {
      description = data[params[0]][params[1]].description
    } else {
      description = complexSections[params[1]].subSections[params[2]].description
    }

    document.getElementById('description-editor').innerText = html(description)
    window.tinyMCE.init({
      selector: '#description-editor',
      width: '100%',
      content_css: '<?php echo get_template_directory_uri() . "/assets/css/tw.css" ?>',
      menubar: false,
      plugins: 'lists link fullscreen csucsplusz_shortcodes',
      toolbar1: `
        undo redo |
        bold italic underline strikethrough |
        bullist numlist |
        link | fullscreen |
      `,
      toolbar2: 'price_shortcodes contact_shortcodes | copy_content_shortcode |'
    })
  }

  function getDescriptionEditor() {
    return window.tinyMCE.get('description-editor')
  }

  function removeDesciptionEditor() {
    if (editingId && getDescriptionEditor()) {
      getDescriptionEditor().remove()
    }
  }

  function setDescription() {
    window.tinyMCE.triggerSave()
    const editor = getDescriptionEditor()
    const rawContent = editor.getContent({
      format: 'raw'
    })
    const content = editor.getContent()
    if (!content) return

    const description = encodedHtml(rawContent.replaceAll('\n', ''))
    const params = editingId.split('-')
    if (params.length === 2) {
      data[params[0]][params[1]].description = description
    } else {
      complexSections[params[1]].subSections[params[2]].description = description
    }
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

          editingId = `${type}-${editButton.dataset.idx}`
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

          editingId = ''
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

          editingId = ''
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

        editingId = `${s.type}-${0}`
        data[s.type].splice(0, 0, {
          name: '',
          description: ''
        })

        render()
      })
    })
  }

  function addSubSectionDeleteOnClick() {
    document.querySelectorAll(`.sub-delete`).forEach(deleteButton => {
      deleteButton.addEventListener('click', () => {
        const secIdx = deleteButton.dataset.secIdx
        const subIdx = deleteButton.dataset.subIdx
        const name = complexSections[secIdx].subSections[subIdx].name
        if (name && !confirm(`Alszekció törlés megerősítése: '${name}'`)) return

        editingId = ''
        complexSections[secIdx].subSections.splice(subIdx, 1)
        render()
      })
    })
  }

  function addSubSectionNameOnChange() {
    document.querySelectorAll(`.sub-name`).forEach(input => {
      input.addEventListener('change', () => {
        complexSections[input.dataset.secIdx].subSections[input.dataset.subIdx].name = input.value
      })
    })
  }

  function addSubSectionEditOnClick() {
    types.forEach(type => {
      document.querySelectorAll(`.sub-edit`).forEach(editButton => {
        editButton.addEventListener('click', () => {
          if (!checkData()) return

          editingId = `sub-${editButton.dataset.secIdx}-${editButton.dataset.subIdx}`
          render()
        })
      })
    })
  }

  function addSubSectionArrowOnClick() {
    document.querySelectorAll(`.sub-arrow-up`).forEach(arrowUp => {
      arrowUp.addEventListener('click', () => {
        if (!checkData()) return

        editingId = ''
        const s = complexSections[arrowUp.dataset.secIdx].subSections
        const idx = parseInt(arrowUp.dataset.subIdx)
        const temp = s[idx]
        s[idx] = s[idx - 1]
        s[idx - 1] = temp
        render()
      })
    })

    document.querySelectorAll(`.sub-arrow-down`).forEach(arrowDown => {
      arrowDown.addEventListener('click', () => {
        if (!checkData()) return

        editingId = ''
        const s = complexSections[arrowDown.dataset.secIdx].subSections
        const idx = parseInt(arrowDown.dataset.subIdx)
        const temp = s[idx]
        s[idx] = s[idx + 1]
        s[idx + 1] = temp
        render()
      })
    })
  }

  function addSubSectionAddButtonOnClick() {
    document.querySelectorAll('.sub-add').forEach(addButton => {
      addButton.addEventListener('click', () => {
        if (!checkData()) return

        const idx = addButton.dataset.idx
        editingId = `sub-${idx}-${0}`
        complexSections[idx].subSections.splice(0, 0, {
          name: '',
          description: ''
        })

        render()
      })
    })
  }

  function addComplexSectionDescriptionToggleOnClick() {
    document.querySelectorAll('.complex-description-toggle').forEach(addButton => {
      addButton.addEventListener('click', () => {
        const idx = addButton.dataset.idx
        complexSections[idx].hasDescription = !complexSections[idx].hasDescription
        render()
      })
    })
  }

  function addDescriptionPreview() {
    const descriptions1 = Array.from(document.querySelectorAll('.description-preview')).map(descriptionEl => {
      return {
        descriptionEl,
        descriptionText: data[descriptionEl.dataset.type][descriptionEl.dataset.idx].description
      }
    })

    const descriptions2 = Array.from(document.querySelectorAll('.sub-description-preview')).map(descriptionEl => {
      return {
        descriptionEl,
        descriptionText: complexSections[descriptionEl.dataset.secIdx].subSections[descriptionEl.dataset.subIdx].description
      }
    })

    descriptions1.concat(descriptions2).forEach(d => {
      const iframe = document.createElement('iframe')
      iframe.srcdoc = `
        <link rel="stylesheet" type="text/css" href="${cssUrl}">
        ${html(d.descriptionText)}
      `

      d.descriptionEl.appendChild(iframe)
    })
  }

  function deleteButton(type, idx, text = 'Törlés') {
    return `<button type="button" data-idx="${idx}" class="button button-secondary ${type}-delete">${text}</button>`
  }

  function doneButton() {
    return `<button type="button" id="done" class="button button">Kész</button>`
  }

  function editButton(type, idx, text = 'Szerkesztés') {
    return `<button type="button" data-idx="${idx}" class="button button-secondary ${type}-edit">${text}</button>`
  }

  function arrows(type, idx) {
    const upArrow = idx > 0 ? `<span class="arrow-up ${type}-arrow-up" data-idx="${idx}">${arrowSvg}</span>` : ''
    const downArrow = idx < data[type].length - 1 ? `<span class="arrow-down ${type}-arrow-down" data-idx="${idx}">${arrowSvg}</span>` : ''
    return `
      <div style="display: inline-flex; gap: 0.2rem; align-items: center">
        ${upArrow}
        ${downArrow}
      </div>
    `
  }

  function subSectionDeleteButton(secIdx, subIdx) {
    return `<button type="button" data-sec-idx="${secIdx}" data-sub-idx="${subIdx}" class="button button-secondary sub-delete">Törlés</button>`
  }

  function subSectionArrows(secIdx, subIdx) {
    const upArrow = subIdx > 0 ?
      `<span class="arrow-up sub-arrow-up" data-sec-idx="${secIdx}" data-sub-idx="${subIdx}">${arrowSvg}</span>` :
      ''

    const downArrow = subIdx < complexSections[secIdx].subSections.length - 1 ?
      `<span class="arrow-down sub-arrow-down" data-sec-idx="${secIdx}" data-sub-idx="${subIdx}">${arrowSvg}</span>` :
      ''

    return `
      <div style="display: inline-flex; gap: 0.2rem; align-items: center">
        ${upArrow}
        ${downArrow}
      </div>
    `
  }

  function complexSectionDescription(section, idx) {
    if (editingId === 'sections-' + idx) {
      return `
        <div style="${section.hasDescription ? '' : 'display: none'}">${descriptionEditor}</div>
      `
    } else if (section.hasDescription) {
      return `<div class="description-preview" data-type="sections" data-idx="${idx}"></div>`
    }

    return `<p>nincsen</p>`
  }

  function complexSectionName(section, idx) {
    return editingId === 'sections-' + idx ?
      `<div><input type="text" value="${section.name}" placeholder="Szekció neve" data-idx="${idx}" class="sections-name" size="40"></div>` :
      `<h3>${section.name}</h2>`
  }

  function complexSectionMenu(section, idx) {
    const descriptionButton = section.hasDescription ?
      `<button type="button" data-idx="${idx}" class="button button-secondary complex-description-toggle">Leírás törlése</button>` :
      `<button type="button" data-idx="${idx}" class="button button-primary complex-description-toggle">Leírás hozzáadása</button>`

    return editingId === 'sections-' + idx ? `
      ${doneButton()}
      ${descriptionButton}
      ${deleteButton('sections', idx, 'Szekció törlése')}
    ` : `
      ${editButton('sections', idx, 'Szekció szerkesztése')}
      ${deleteButton('sections', idx, 'Szekció törlése')}
      ${arrows('sections', idx)}
    `
  }

  function simpleSectionsContent() {
    return simpleSections.map(section => simpleSectionTable(section)).join('')
  }

  function simpleSectionTable(section) {
    const type = section.type
    const rows = section.sectionData.map((row, idx) =>
      editingId === `${type}-${idx}` ?
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

  function simpleSectionRow(type, row, idx) {
    return `
      <tr>
        <td>${idx + 1}</td>
        <td>${row.name}</td>
        <td class="description-preview" data-type="${type}" data-idx="${idx}"></td>
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
        <td>${descriptionEditor}</td>
        <td>
          ${doneButton()}
          ${deleteButton(type, idx)}
        </td>
      </tr>
    `
  }

  function complexSectionContent() {
    return complexSections.map((section, secIdx) => complexSectionTable(section, secIdx)).join('')
  }

  function complexSectionTable(section, secIdx) {
    const rows = section.subSections.map((row, subIdx) =>
      editingId === `sub-${secIdx}-${subIdx}` ?
      complexSectionEditRow(row, secIdx, subIdx) :
      complexSectionRow(row, secIdx, subIdx)
    ).join('')

    const addSubSectionButton = `<button type="button" class="sub-add button button-primary" data-idx="${secIdx}">Alszekció hozzáadása</button>`
    const subSectionsTable = rows ? `
      <h3>Alszekciók</h3>
      ${addSubSectionButton}
      <table class="form-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Alszekció neve</th>
            <th>Leírás</th>
            <th>Műveletek</th>
          </tr>
        </thead>
        <tbody>${rows}</tbody>
      </table>` :
      addSubSectionButton

    return `
      <div class="complex-section">
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px">
          <div style="flex-grow: 1">
            ${complexSectionName(section, secIdx)}
            <h3>Szekció leírása</h3>
            ${complexSectionDescription(section, secIdx)}
          </div>
          <div>${complexSectionMenu(section, secIdx)}</div>
        </div>
        ${subSectionsTable}
      </div>
    `
  }

  function complexSectionRow(row, secIdx, subIdx) {
    return `
      <tr>
        <td>${subIdx + 1}</td>
        <td>${row.name}</td>
        <td class="sub-description-preview" data-sec-idx="${secIdx}" data-sub-idx="${subIdx}"></td>
        <td>
          <button type="button" data-sec-idx="${secIdx}" data-sub-idx="${subIdx}" class="button button-secondary sub-edit">Szerkesztés</button>
          ${subSectionDeleteButton(secIdx, subIdx)}
          ${subSectionArrows(secIdx, subIdx)}
        </td>
      </tr>
    `
  }

  function complexSectionEditRow(row, secIdx, subIdx) {
    return `
      <tr>
        <td>${subIdx + 1}</td>
        <td>
          <input type="text" placeholder="Alszekció neve" value="${row.name}" data-sec-idx="${secIdx}" data-sub-idx="${subIdx}" class="sub-name" size="40">
        </td>
        <td>${descriptionEditor}</td>
        <td>
          ${doneButton()}
          ${subSectionDeleteButton(secIdx, subIdx)}
        </td>
      </tr>
    `
  }

  function render() {
    removeDesciptionEditor()
    simpleSectionsEl.innerHTML = simpleSectionsContent()
    complexSectionsEl.innerHTML = complexSectionContent()
    addDeleteOnClick()
    addNameOnChange()
    addDoneOnClick()
    addEditOnClick()
    addArrowOnClick()
    addSimpleSectionsAddButtonOnClick()
    addSubSectionDeleteOnClick()
    addSubSectionNameOnChange()
    addSubSectionEditOnClick()
    addSubSectionArrowOnClick()
    addSubSectionAddButtonOnClick()
    addComplexSectionDescriptionToggleOnClick()
    setupDescriptionEditor()
    addDescriptionPreview()
  }
  window.addEventListener('load', render)

  document.getElementById('complex-section-add').addEventListener('click', () => {
    if (!checkData()) return

    editingId = `sections-${0}`
    complexSections.splice(0, 0, {
      name: '',
      hasDescription: false,
      description: '',
      subSections: []
    })

    render()
  })

  let submitted = false
  document.querySelector('form').addEventListener('submit', (e) => {
    if (editingId || !checkData(true)) {
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