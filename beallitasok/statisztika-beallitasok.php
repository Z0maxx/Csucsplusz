<style>
  #regular th:first-child {
    width: 20px !important;
    max-width: 20px;
  }
</style>
<div class="wrap">
  <h1>Statisztika</h1>
  <form method="post" action="">
    <?php wp_nonce_field('stat_form_action', 'stat_form_nonce'); ?>
    <input type="hidden" name="stat_json" id="stat-json">
    <table class="form-table" id="regular"></table>
    <h2>VSM</h2>
    <table class="form-table" id="e">
      <thead>
        <tr>
          <th>E</th>
        </tr>
        <tr>
          <td>
            <button class="button button-primary add" type="button">Hozzáadás</button>
          </td>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
    <table class="form-table" id="gy">
      <thead>
        <tr>
          <th>GY</th>
        </tr>
        <tr>
          <td>
            <button class="button button-primary add" type="button">Hozzáadás</button>
          </td>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
    <?php submit_button('Mentés') ?>
  </form>
</div>
<script>
  <?php $stat_json = get_option('stat_json') ?: 'null' ?>
  const originalData = '<?php echo $stat_json ?>'
  const data = JSON.parse(originalData) ?? {
    ako: '',
    kk: '',
    vsm: [{
      name: e,
      stats: []
    }, {
      name: gy,
      stats: []
    }]
  }

  const vsm = data.vsm.reduce((acc, curr) => {
    acc[curr.name] = curr.stats
    return acc
  }, {})

  const ako = {
    name: 'ÁKÓ',
    id: 'ako',
    unit: '%'
  }

  const kk = {
    name: 'KK',
    id: 'kk',
    unit: 'Ft'
  }

  const regularStats = [ako, kk]
  const regularTable = document.getElementById('regular')
  const now = new Date()
  const currYear = now.getFullYear()
  const prevYear = currYear - 1
  const currMonth = (now.getMonth() + 1).toString().padStart(2, '0')
  const jsonInput = document.getElementById('stat-json')
  const months = [...Array(12).keys()].map(i => i + 1)
  const lastValues = new Map()
  const vsmTypes = Object.keys(vsm)

  let fromChange = false
  let clickedAt

  window.addEventListener('click', (e) => {
    clickedAt = {
      clientX: e.clientX,
      clientY: e.clientY
    }
  })

  function checkNotNegativePercent(name, value) {
    const notANumber = name + ': nem egy szám'
    replaced = value.replaceAll('.', '').replaceAll(',', '.')
    const parsed = parseFloat(replaced)
    if (Number.isNaN(parsed)) {
      alert(notANumber)
      return false
    }

    let split = replaced.split('.')
    if (split.length > 1) {
      const fixed = parsed.toFixed(split[1].length)
      if (fixed !== replaced.padEnd(fixed.length, '0')) {
        alert(notANumber)
        return false
      }
    } else if (parsed.toFixed(0) !== replaced) {
      alert(notANumber)
      return false
    }

    split = value.split('.')
    if (split.length > 1) {
      if (split[0].length > 3) {
        alert(notANumber)
        return false
      }

      for (let i = 1; i < split.length; i++) {
        if (split[i].split(',')[0].length !== 3) {
          alert(notANumber)
          return false
        }
      }
    }

    if (parsed < 0) {
      alert(name + ': százalék nem lehet negatív szám')
      return false
    }

    return true
  }

  function checkStats() {
    if (data.ako && !checkNotNegativePercent('ÁKÓ', data.ako)) {
      return false
    }

    if (data.kk && data.kk < 0) {
      alert('KK: nem lehet negatív szám')
      return false
    }

    for (let i = 0; i < vsmTypes.length; i++) {
      const type = vsmTypes[i]
      const set = new Set()
      const vsmStats = vsm[type]
      const typeName = 'VSM ' + type.toUpperCase()
      const dates = vsmStats.map(s => s.year + '-' + s.month)
      for (let j = 0; j < data.length; j++) {
        const d = dates[j]
        const p = data[j].percent
        const name = typeName + ' ' + d
        if (!p) {
          alert(name + ': nincsen százalék szám')
          return false
        }

        if (!checkNotNegativePercent(name, p)) {
          return false
        }

        if (parseFloat(p.replace(',', '.')) > 100) {
          alert(name + ': százalék nem lehet nagyobb, mint 100')
          return false
        }

        if (set.has(d)) {
          alert(`Duplikált ${name} statisztika: ${d}`)
          return false
        } else {
          set.add(d)
        }
      }
    }

    return true
  }

  function renderRegularStats() {
    function render() {
      regularTable.innerHTML = regularStats.map((s, idx) => {
        const value = data[s.id]
        const deleteButton = value ? `<button type="button" class="button button-secondary delete" data-id="${s.id}">Törlés</button>` : ''
        return `
          <tr>
            <th><label for="${s.id}-stat">${s.name}</label></th>
            <td>
              <input type="text" id="${s.id}" class="value" value="${value}" placeholder="${s.unit === '%' ? 'Százalék száma' : 'Ár'}" size="10">
              <span>${s.unit}</span>
              ${deleteButton}
            </td>
          </tr>
        `
      }).join('')

      regularTable.querySelectorAll('.delete').forEach(deleteButton => {
        deleteButton.addEventListener('click', () => {
          data[deleteButton.dataset.id] = ''
          renderRegularStats()
        })
      })

      regularTable.querySelectorAll('.value').forEach(input => {
        input.addEventListener('change', () => {
          fromChange = true
          data[input.id] = input.value
          renderRegularStats()
        })
      })
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

  function renderVsmStat(table, data) {
    const tbody = table.querySelector('tbody')
    tbody.innerHTML = data.map((item, idx) => {
      const yearOptions = [prevYear, currYear].map(y => {
        const year = y.toString()
        const selected = year === item.year || currYear === y ? 'selected' : ''
        return `<option ${selected} value="${year}">${year}</option>`
      }).join('')

      let selected
      const monthOptions = months.map(m => {
        const month = m.toString().padStart(2, '0')
        selected = month === item.month || month === currMonth ? 'selected' : ''
        return `<option ${selected} value="${month}">${month}</option>`
      }).join('')

      return `
        <tr>
          <td>
            <select class="year" data-idx="${idx}">
              ${yearOptions}
            </select>
            <select class="month" data-idx="${idx}">
              ${monthOptions}
            </select>
            <input type="text" class="percent" value="${item.percent}" data-idx="${idx}" placeholder="Százalék száma" id="${table.id}-${idx}" size="10">
            <span>%</span>
            <button type="button" class="button button-secondary delete" data-idx="${idx}">Törlés</button>
          </td>
        </tr>
      `
    }).join('')

    tbody.querySelectorAll('.year').forEach(select => {
      select.addEventListener('change', () => {
        data[select.dataset.idx].year = select.value
      })
    })

    tbody.querySelectorAll('.month').forEach(select => {
      select.addEventListener('change', () => {
        data[select.dataset.idx].month = select.value
      })
    })

    tbody.querySelectorAll('.percent').forEach(input => {
      input.addEventListener('input', () => {
        data[input.dataset.idx].percent = input.value
      })
    })

    tbody.querySelectorAll('.delete').forEach(button => {
      button.addEventListener('click', () => {
        data.splice(button.dataset.idx, 1)
        renderVsmStat(table, data)
      })
    })
  }

  function setupStats() {
    renderRegularStats()
    vsmTypes.forEach(type => {
      const table = document.getElementById(type)
      const data = vsm[type]
      renderVsmStat(table, data)
      table.querySelector('.add').addEventListener('click', () => {
        data.splice(0, 0, {
          year: '',
          month: '',
          percent: ''
        })

        renderVsmStat(table, data)
      })
    })
  }
  setupStats()

  let submitted = false
  document.querySelector('form').addEventListener('submit', e => {
    if (!checkStats()) {
      e.preventDefault()
      return
    }

    submitted = true
    data.vsm = Object.keys(vsm).map(k => {
      return {
        name: k,
        stats: vsm[k]
      }
    })

    jsonInput.value = JSON.stringify(data)
  })

  window.addEventListener('beforeunload', (e) => {
    if (!submitted && originalData !== JSON.stringify(data)) {
      e.preventDefault()
      e.returnValue = true
    }
  })
</script>