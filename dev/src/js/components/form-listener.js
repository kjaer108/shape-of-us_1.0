/**
 * Toggle disabled attr to form
 */

export default (() => {
  document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('.validate-form')
    forms.forEach((form) => {
      const submitButton = form.querySelector('button[type="submit"]')
      if (!submitButton) return
      function updateSubmitState() {
        const inputs = form.querySelectorAll('input, select, textarea')
        let allEmpty = true
        for (const input of inputs) {
          if (
            (input.type === 'checkbox' || input.type === 'radio') &&
            input.checked
          ) {
            allEmpty = false
            break
          } else if (
            input.type !== 'checkbox' &&
            input.type !== 'radio' &&
            input.value.trim() !== ''
          ) {
            allEmpty = false
            break
          }
        }
        submitButton.disabled = allEmpty
      }
      form.addEventListener('input', updateSubmitState)
      form.addEventListener('change', updateSubmitState)
      updateSubmitState()
    })
  })
})()
