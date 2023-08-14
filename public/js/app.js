window.onload = function(){
  const form = document.querySelector('.form_contact')
  form.addEventListener('submit', e => {
    e.preventDefault()
    const formData = new FormData(form)
    $.ajax({
      type:'POST',
      url:'/lead',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },
      dataType: 'JSON',
      contentType: false,
      cache: false,
      processData: false,
      data:formData
    }).done(() => {
      removeErrors()
      clearForm()
    }).fail(response => {
      removeErrors()
      if(response.status === 422){
        const errors = response.responseJSON.errors
        for(let key in errors){
          document.querySelector('input[name="'+ key +'"]')
            .insertAdjacentHTML('afterend', '<div class="error">'+ errors[key].shift() +'</div>')
        }
      }
    }).always(response => {
      if(response.status === undefined){
        document.querySelector('.container').insertAdjacentHTML('afterbegin',
          `<div class="create_success">${response.message}</div>`)
        setTimeout(() => {
          document.querySelector('.create_success').style.opacity = '0'
        }, 3000)
      } else if(response.status === 422){
      } else {
        document.querySelector('.container').insertAdjacentHTML('afterbegin',
          `<div class="create_error">${response.responseJSON.message}</div>`)
        setTimeout(() => {
          document.querySelector('.create_error').style.opacity = '0'
        }, 3000)
      }
    })
  })

  // setup datetimepicker flatpickr
  const flatpicker = flatpickr('input[name=birth]', {
    dateFormat: 'Y-m-d',
    altInput: true,
    altFormat: 'F j, Y'
  })

  // clears form inputs after it was sent
  function clearForm(){
    let inputs = document.querySelectorAll('.input_container > *:first-child')
    if(inputs){
      for(let input of inputs){
        input.value = ''
      }
    }
    flatpicker.clear()
  }

  // removes validation messages
  function removeErrors(){
    let errorNodes = document.querySelectorAll('.error')
    if(errorNodes){
      for(let error of errorNodes){
        error.remove()
      }
    }
  }
}
