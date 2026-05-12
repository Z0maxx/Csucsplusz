window.addEventListener('load', () => {
  const copyContents = document.querySelectorAll('.copy-content')
  for (const content of copyContents) {
    content.addEventListener('click', () => {
      navigator.clipboard.writeText(content.querySelector('span').textContent)
      content.style = 'background: lightgreen; border-color: green'
      setTimeout(() => content.style = '', 300)
    })
  }
})