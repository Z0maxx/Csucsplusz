<style>
  table {
    max-width: 300px
  }
</style>
<div class="wrap">
  <h1>Elérhetőségek</h1>
  <form method="post" action="">
    <?php wp_nonce_field('contact_info_form_action', 'contact_info_form_nonce') ?>
    <input type="hidden" name="crt_json" id="crt-json">
    <table class="form-table" id="contact-info">
      <tr>
        <th for="phone">Telefonszám</th>
        <td><input type="text" id="phone" name="phone"></td>
      </tr>
      <tr>
        <th for="email">Email</th>
        <td><input type="email" id="email" name="email"></td>
      </tr>
      <tr>
        <th for="address">Cím</th>
        <td><input type="text" id="address" name="address" size="40"></td>
      </tr>
    </table>
    <h2>Ügyfélfogadási idő</h2>
    <table class="form-table">
      <tbody id="crt"></tbody>
    </table>
    <?php submit_button('Mentés') ?>
  </form>
</div>
<script>
  <?php
  $crt_json = get_option('crt_json') ?: 'null';
  $phone = get_option('phone_contact');
  $email = get_option('email_contact');
  $address = get_option('address_contact');
  ?>
  const days = ['Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek']
  const crt = JSON.parse('<?php echo $crt_json ?>') ?? days.map(day => {
    return {
      day,
      startTime: '',
      endTime: ''
    }
  })

  const contactInfos = {
    phone: '<?php echo $phone ?>',
    email: '<?php echo $email ?>',
    address: '<?php echo $address ?>'
  }

  const data = {
    contactInfos,
    crt
  }

  const originalData = JSON.stringify(data)
  const jsonInput = document.getElementById('crt-json')

  function checkData() {
    if (!contactInfos.phone) {
      alert('Nincsen Telefonszám megadva')
      return false
    }

    if (!contactInfos.email) {
      alert('Nincsen Email megadva')
      return false
    }

    if (!contactInfos.address) {
      alert('Nincsen Cím megadva')
      return false
    }

    for (let i = 0; i < crt.length; i++) {
      const t = crt[i]
      const s1 = t.startTime.split(':')
      const s2 = t.endTime.split(':')
      const m1 = parseInt(s1[0]) * 60 + parseInt(s1[1])
      const m2 = parseInt(s2[0]) * 60 + parseInt(s2[1])
      if (m1 > m2) {
        alert(t.day + ': az időpont kezdete később van, mint a vége')
        return false
      }
    }

    return true
  }

  document.getElementById('crt').innerHTML = days.map((day, idx) => {
    const startTime = crt[idx].startTime
    const endTime = crt[idx].endTime
    return `
      <tr>
        <th>${day}</th>
        <td>
          <div style="display: flex; align-items: center; gap: 0.2rem">
            <input type="time" data-idx="${idx}" value="${startTime}" class="start-time">
            <span style="font-weight: bold">—</span>
            <input type="time" data-idx="${idx}" value="${endTime}" class="end-time">
          </div>
        </td>
      </tr>
    `
  }).join('')

  document.querySelectorAll('.start-time').forEach(input => {
    input.addEventListener('change', () => {
      crt[input.dataset.idx].startTime = input.value
    })
  })

  document.querySelectorAll('.end-time').forEach(input => {
    input.addEventListener('change', () => {
      crt[input.dataset.idx].endTime = input.value
    })
  })

  Object.keys(contactInfos).forEach(ci => {
    const input = document.getElementById(ci)
    input.value = contactInfos[ci]
    input.addEventListener('input', () => {
      contactInfos[ci] = input.value
    })
  })

  let submitted = false
  document.querySelector('form').addEventListener('submit', (e) => {
    if (!checkData()) {
      e.preventDefault()
      return
    }

    submitted = true
    jsonInput.value = JSON.stringify(crt)
  })

  window.addEventListener('beforeunload', (e) => {
    if (!submitted && originalData !== JSON.stringify(data)) {
      e.preventDefault()
      e.returnValue = true
    }
  })
</script>