<style>
  tbody tr:nth-child(odd) {
    background-color: lightgray
  }

  tbody tr:nth-child(even) {
    background-color: silver;
  }

  tbody>tr:hover {
    outline: 1px solid black
  }

  table table tbody tr:nth-child(odd) {
    background-color: lightyellow
  }

  table table tbody tr:nth-child(even) {
    background-color: lightgoldenrodyellow;
  }

  table:not(table table) {
    table-layout: fixed;
  }

  th:first-child,
  td:first-child {
    width: 30px
  }

  th:not(td th):nth-child(2),
  td:not(td td):nth-child(2) {
    width: 300px
  }

  #standalone-prices-table th:nth-child(3),
  #standalone-prices-table td:nth-child(3),
  #constants-table th:nth-child(3),
  #constants-table td:nth-child(3) {
    width: 150px
  }

  #calculated-prices-table th:not(td th):nth-child(3),
  #calculated-prices-table td:not(td td):nth-child(3) {
    width: 600px
  }

  th:not(td th):nth-child(4),
  td:not(td td):nth-child(4) {
    width: 300px
  }

  #standalone-prices-table {
    width: 780px
  }

  #constants-table {
    width: 780px
  }

  #calculated-prices-table {
    width: 1230px;
  }

  .standalone-value,
  .constant-value {
    width: 80px
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

  .operation:has(option[value="+"]:checked),
  .operation:has(option[value="*"]:checked) {
    font-size: 1.2rem;
    font-weight: bold;
    line-height: 0.4rem;
    width: 3rem;
  }

  .operation-option {
    font-size: 1.2rem;
    font-weight: bold;
  }

  .operation-text {
    font-size: 1.2rem;
    font-weight: bold;
    line-height: 1rem;
  }
</style>
<div class="wrap">
  <h1>Árak</h1>
  <form method="post" action="">
    <?php wp_nonce_field('ar_beallitasok_action', 'ar_beallitasok_nonce') ?>
    <input type="hidden" id="prices-json" name="prices_json">
    <h2>Önálló árak</h2>
    <button id="standalone-add" type="button" class="button button-primary">Hozzáadás</button>
    <table class="form-table" id="standalone-prices-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Ár neve</th>
          <th>Ár</th>
          <th>Műveletek</th>
        </tr>
      </thead>
      <tbody id="standalone-prices"></tbody>
    </table>
    <h2>Konstansok</h2>
    <button id="constant-add" type="button" class="button button-primary">Hozzáadás</button>
    <table class="form-table" id="constants-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Konstans neve</th>
          <th>Érték</th>
          <th>Műveletek</th>
        </tr>
      </thead>
      <tbody id="constants"></tbody>
    </table>
    <h2>Számított árak</h2>
    <button id="calculated-add" type="button" class="button button-primary">Hozzáadás</button>
    <table class="form-table" id="calculated-prices-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Ár neve</th>
          <th>Számítás módja</th>
          <th>Műveletek</th>
        </tr>
      </thead>
      <tbody id="calculated-prices"></tbody>
    </table>
    <?php submit_button('Mentés') ?>
  </form>
</div>
<script>
  <?php $prices = get_option('prices_json') ?: 'null' ?>
  const originalPrices = '<?php echo $prices ?>'
  const prices = JSON.parse(originalPrices) ?? {
    standalone: [],
    constant: [],
    calculated: []
  }

  const deleted = new Set()
  const standalone = 'standalone'
  const constant = 'constant'
  const calculated = 'calculated'
  const priceTypes = [standalone, constant, calculated]
  const operations = [{
    text: '+',
    value: '+'
  }, {
    text: '×',
    value: '*'
  }]

  const standalonePricesEl = document.getElementById('standalone-prices')
  const constantsEl = document.getElementById('constants')
  const calculatedPricesEl = document.getElementById('calculated-prices')
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
  let clickedAt

  window.addEventListener('click', (e) => {
    clickedAt = {
      clientX: e.clientX,
      clientY: e.clientY
    }
  })

  function checkPrices() {
    if (!editingId) return true

    for (let i = 0; i < prices.standalone.length; i++) {
      const p = prices.standalone[i]
      const prefix = `Önálló árak sor ${i + 1}: `
      if (!p.name) {
        alert(prefix + 'nincsen Ár név')
        return false
      }
      if (!p.value) {
        alert(prefix + 'nincsen Ár')
        return false
      }

      if (p.value < 1) {
        alert(prefix + 'negatív Ár')
        return false
      }
    }

    for (let i = 0; i < prices.constant.length; i++) {
      const c = prices.constant[i]
      const prefix = `Konstansok sor ${i + 1}: `
      if (!c.name) {
        alert(prefix + 'nincsen Konstans név')
        return false
      }
      if (!c.value) {
        alert(prefix + 'nincsen Érték')
        return false
      }
    }

    for (let i = 0; i < prices.calculated.length; i++) {
      const p = prices.calculated[i]
      const prefix = `Számított árak sor ${i + 1}: `
      if (!p.name) {
        alert(prefix + 'nincsen Ár név')
        return false
      }
      if (!p.startingValue) {
        alert(prefix + 'változó 1 nincsen kiválasztva')
        return false
      }
      if (p.variables.length < 1) {
        alert(prefix + 'nincsenek egyéb változók')
        return false
      }

      for (let j = 0; j < p.variables.length; j++) {
        const v = p.variables[j]
        if (!v.operation) {
          alert(prefix + `változó ${j + 2}-höz nincsen művelet kiválasztva`)
          return false
        }
        if (!v.value) {
          alert(prefix + `változó ${j + 2} nincsen kiválasztva`)
          return false
        }
      }
    }

    return true
  }

  function addDeleteOnClick() {
    priceTypes.forEach(type => {
      document.querySelectorAll(`.${type}-delete`).forEach(deleteButton => {
        deleteButton.addEventListener('click', () => {
          const idx = deleteButton.dataset.idx
          const name = prices[type][idx].name
          if (name && !confirm(`Törlés megerősítése: '${name}'`)) return

          editingId = ''
          deleted.add(name)
          prices[type].splice(idx, 1)
          const calculatedToRemove = []
          prices.calculated.forEach(p => {
            p.variables = p.variables.filter(v => v.value !== name)
            if (p.startingValue !== name) return

            if (p.variables.length > 0) {
              p.startingValue = p.variables[0].value
              p.variables.splice(0, 1)
            } else {
              calculatedToRemove.push(p)
            }
          })

          calculatedToRemove.forEach(r => deleted.add(r.name))
          prices.calculated = prices.calculated.filter(p => !calculatedToRemove.includes(p))
          renderPrices()
        })
      })
    })
  }

  function addVariableDeleteOnClick() {
    document.querySelectorAll(`.variable-delete`).forEach(deleteButton => {
      deleteButton.addEventListener('click', () => {
        prices.calculated[deleteButton.dataset.pIdx].variables.splice(deleteButton.dataset.vIdx, 1)
        renderPrices()
      })
    })
  }

  function addNameOnChange() {
    priceTypes.forEach(type => {
      document.querySelectorAll(`.${type}-name`).forEach(input => {
        input.addEventListener('change', () => {
          const newName = input.value
          const originalName = prices[type][input.dataset.idx].name
          prices.calculated.forEach(p => {
            if (p.name === originalName) return

            if (p.startingValue === originalName) {
              p.startingValue = newName
            }

            p.variables.forEach(v => {
              if (v.value === originalName) {
                v.value = newName
              }
            })
          })

          prices[type][input.dataset.idx].name = newName
          renderVariables()
        })
      })
    })
  }

  function addValueOnChange() {
    [standalone, constant].forEach(type => {
      document.querySelectorAll(`.${type}-value`).forEach(input => {
        input.addEventListener('change', () => {
          prices[type][input.dataset.idx].value = input.value
        })
      })
    })
  }

  function variableOptions(priceName, value) {
    const emptyOption = `<option disabled value="" hidden ${value === '' ? 'selected' : ''}>Változó</option>`
    return emptyOption + [
      ...prices.constant.map(c => c.name),
      ...prices.standalone.map(p => p.name),
      ...prices.calculated.filter(p => p.name !== priceName).map(p => p.name)
    ].map(o => `
      <option value="${o}" ${o === value ? 'selected' : ''}>${o}</option>
    `).join('')
  }

  function addVariableAddOnClick() {
    document.querySelectorAll('.variable-add').forEach(addButton => {
      addButton.addEventListener('click', () => {
        const idx = addButton.dataset.idx
        prices.calculated[idx].variables.push({
          operation: '',
          value: ''
        })

        renderVariablesTable(idx)
      })
    })
  }

  function addVariableOnChange() {
    document.querySelectorAll('.starting-variable').forEach(select => {
      select.addEventListener('change', () => {
        prices.calculated[select.dataset.idx].startingValue = select.value
      })
    })

    document.querySelectorAll('.variable').forEach(select => {
      select.addEventListener('change', () => {
        prices.calculated[select.dataset.pIdx].variables[select.dataset.vIdx].value = select.value
      })
    })
  }

  function addOperationOnChange() {
    document.querySelectorAll('.operation').forEach(select => {
      select.addEventListener('change', () => {
        prices.calculated[select.dataset.pIdx].variables[select.dataset.vIdx].operation = select.value
      })
    })
  }

  function addCopyOnClick() {
    [standalone, calculated].forEach(type => {
      document.querySelectorAll(`.${type}-copy`).forEach(copyButton => {
        copyButton.addEventListener('click', () => {
          navigator.clipboard.writeText(`[ár="${prices[type][copyButton.dataset.idx].name}"]`)
        })
      })
    })
  }

  function addDoneOnClick() {
    document.getElementById('done')?.addEventListener('click', () => {
      if (!checkPrices()) return

      editingId = ''
      renderPrices()
    })
  }

  function addEditOnClick() {
    priceTypes.forEach(type => {
      document.querySelectorAll(`.${type}-edit`).forEach(editButton => {
        editButton.addEventListener('click', () => {
          if (!checkPrices()) return

          editingId = type + editButton.dataset.idx
          renderPrices()
        })
      })
    })
  }

  function addArrowOnClick() {
    priceTypes.forEach(type => {
      document.querySelectorAll(`.${type}-arrow-up`).forEach(arrowUp => {
        arrowUp.addEventListener('click', () => {
          if (!checkPrices()) return

          const idx = parseInt(arrowUp.dataset.idx)
          const list = prices[arrowUp.dataset.type]
          const temp = list[idx]
          list[idx] = list[idx - 1]
          list[idx - 1] = temp
          renderPrices()
        })
      })

      document.querySelectorAll(`.${type}-arrow-down`).forEach(arrowDown => {
        arrowDown.addEventListener('click', () => {
          if (!checkPrices()) return

          const idx = parseInt(arrowDown.dataset.idx)
          const list = prices[arrowDown.dataset.type]
          const temp = list[idx]
          list[idx] = list[idx + 1]
          list[idx + 1] = temp
          renderPrices()
        })
      })
    })
  }

  function copyButton(type, idx) {
    return `<button type="button" data-idx="${idx}" class="button button-primary ${type}-copy">Másolás</button>`
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
    const upArrow = idx > 0 ? `<span class="arrow-up ${type}-arrow-up" data-idx="${idx}" data-type="${type}">${arrowSvg}</span>` : ''
    const downArrow = idx < prices[type].length - 1 ? `<span class="arrow-down ${type}-arrow-down" data-idx="${idx}" data-type="${type}">${arrowSvg}</span>` : ''
    return `
      <div style="display: inline-flex; gap: 0.2rem; align-items: center">
        ${upArrow}
        ${downArrow}
      </div>
    `
  }

  function standaloneContent() {
    return prices.standalone.map((p, idx) =>
      editingId === standalone + idx ? standaloneEditRow(p, idx) : standaloneRow(p, idx)
    ).join('')
  }

  function standaloneRow(p, idx) {
    return `
      <tr>
        <td>${idx + 1}</td>
        <td>${p.name}</td>
        <td>${p.value} Ft</td>
        <td>
          ${copyButton(standalone, idx)}
          ${editButton(standalone, idx)}
          ${deleteButton(standalone, idx)}
          ${arrows(standalone, idx)}
        </td>
      </tr>
    `
  }

  function standaloneEditRow(p, idx) {
    return `
      <tr>
        <td>${idx + 1}</td>
        <td>
          <input type="text" placeholder="Ár neve" value="${p.name}" data-idx="${idx}" class="standalone-name" size="35">
        </td>
        <td>
          <input type="number" placeholder="Ár" value="${p.value}" data-idx="${idx}" class="standalone-value">
          <span>Ft</span>
        </td>
        <td>
          ${doneButton()}
          ${deleteButton(standalone, idx)}
        </td>
      </tr>
    `
  }

  function constantContent() {
    return prices.constant.map((c, idx) =>
      editingId === constant + idx ? constantEditRow(c, idx) : constantRow(c, idx)
    ).join('')
  }

  function constantRow(c, idx) {
    return `
      <tr>
        <td>${idx + 1}</td>
        <td>${c.name}</td>
        <td>${c.value}</td>
        <td>
          ${editButton(constant, idx)}
          ${deleteButton(constant, idx)}
          ${arrows(constant, idx)}
        </td>
      </tr>
    `
  }

  function constantEditRow(c, idx) {
    return `
      <tr>
        <td>${idx + 1}</td>
        <td>
          <input type="text" placeholder="Konstans neve" value="${c.name}" data-idx="${idx}" class="constant-name" size="35">
        </td>
        <td>
          <input type="number" placeholder="Érték" value="${c.value}" data-idx="${idx}" class="constant-value">
        </td>
        <td>
          ${doneButton()}
          ${deleteButton(constant, idx)}
        </td>
      </tr>
    `
  }

  function calculatedContent() {
    return prices.calculated.map((p, idx) =>
      editingId === calculated + idx ? calculatedEditRow(p, idx) : calculatedRow(p, idx)
    ).join('')
  }

  function variableCalculationText(p) {
    return p.startingValue + p.variables.map(v => {
      const op = operations.find(o => o.value === v.operation).text
      return `<span class="operation-text"> ${op}</span> ${v.value}`
    }).join('')
  }

  function calculatedRow(p, idx) {
    return `
      <tr>
        <td>${idx + 1}</td>
        <td>${p.name}</td>
        <td class="variable-cell" data-idx="${idx}">
          ${variableCalculationText(p)}
        </td>
        <td>
          ${copyButton(calculated, idx)}
          ${editButton(calculated, idx)}
          ${deleteButton(calculated, idx)}
          ${arrows(calculated, idx)}
        </td>
      </tr>
    `
  }

  function variablesTable(p, pIdx) {
    const variables = p.variables.map((v, vIdx) => {
      const opOptions = operations.map(o => `
        <option value="${o.value}" ${o.value === v.operation ? 'selected' : ''} class="operation-option">${o.text}</option>
      `).join('')

      return `
        <tr>
          <td>${vIdx + 2}</td>
          <td class="variable-edit-cell">
            <select data-p-idx="${pIdx}" data-v-idx="${vIdx}" class="operation">
              <option disabled value="" hidden ${v.operation === '' ? 'selected' : ''}>Művelet</option>
              ${opOptions}
            </select>
            <select data-p-idx="${pIdx}" data-v-idx="${vIdx}" class="variable">${variableOptions(p.name, v.value)}</select>
          </td>
          <td>
            <button type="button" data-p-idx="${pIdx}" data-v-idx="${vIdx}" class="button button-secondary variable-delete">Változó törlése</button>
          </td>
        </tr>
      `
    }).join('')

    return `
      <table>
        <thead>
          <tr>
            <td>#</td>
            <td>Változó</td>
            <td>Művelet</td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>
              <select data-idx="${pIdx}" class="starting-variable">${variableOptions(p.name, p.startingValue)}</select>
            </td>
            <td>
              <button type="button" data-idx="${pIdx}" class="button button-primary variable-add">Változó hozzáadása</button>
            </td>
          </tr>
          ${variables}
        </tbody>
      </table>
    `
  }

  function calculatedEditRow(p, pIdx) {
    return `
      <tr>
        <td>${pIdx + 1}</td>
        <td>
          <input type="text" required placeholder="Ár neve" value="${p.name}" data-idx="${pIdx}" class="calculated-name" size="35">
        </td>
        <td class="variable-edit-cell" data-idx="${pIdx}">${variablesTable(p, pIdx)}</td>
        <td>
          ${doneButton()}
          ${deleteButton(calculated, pIdx)}
        </td>
      </tr>
    `
  }

  function renderPrices() {
    standalonePricesEl.innerHTML = standaloneContent()
    constantsEl.innerHTML = constantContent()
    calculatedPricesEl.innerHTML = calculatedContent()
    addDeleteOnClick()
    addCopyOnClick()
    addNameOnChange()
    addValueOnChange()
    addVariableAddOnClick()
    addVariableOnChange()
    addVariableDeleteOnClick()
    addOperationOnChange()
    addDoneOnClick()
    addEditOnClick()
    addArrowOnClick()
  }
  renderPrices()

  function renderVariables() {
    document.querySelectorAll('.variable-cell').forEach(cell => {
      cell.innerHTML = variableCalculationText(prices.calculated[cell.dataset.idx])
    })
  }

  function renderVariablesTable(idx) {
    document.querySelector('.variable-edit-cell').innerHTML = variablesTable(prices.calculated[idx], idx)
    addVariableAddOnClick()
    addVariableOnChange()
    addVariableDeleteOnClick()
    addOperationOnChange()
  }

  document.getElementById('standalone-add').addEventListener('click', () => {
    if (!checkPrices()) return

    editingId = standalone + 0
    prices.standalone.splice(0, 0, {
      type: standalone,
      name: '',
      value: ''
    })

    renderPrices()
  })

  document.getElementById('constant-add').addEventListener('click', () => {
    if (!checkPrices()) return

    editingId = constant + 0
    prices.constant.splice(0, 0, {
      type: constant,
      name: '',
      value: ''
    })

    renderPrices()
  })

  document.getElementById('calculated-add').addEventListener('click', () => {
    if (!checkPrices()) return

    editingId = calculated + 0
    prices.calculated.splice(0, 0, {
      type: calculated,
      name: '',
      startingValue: '',
      variables: []
    })

    renderPrices()
  })

  let submitted = false
  document.querySelector('form').addEventListener('submit', (e) => {
    prices.deleted = Array.from(deleted)
    if (editingId || !checkPrices()) {
      e.preventDefault()
      if (editingId) {
        alert('Szerkesztés közben nem lehet menteni')
      }

      return
    }

    submitted = true
    document.getElementById('prices-json').value = JSON.stringify(prices)
  })

  window.addEventListener('beforeunload', (e) => {
    if (!submitted && originalPrices !== JSON.stringify(prices)) {
      e.preventDefault()
      e.returnValue = true
    }
  })
</script>