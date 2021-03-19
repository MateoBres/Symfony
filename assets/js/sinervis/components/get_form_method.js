function getFormMethod($form) {
    // when the form method is different from 'POST', Symfony adds a new field with the name '_method'
    // to the form and sets the form method as its value
    if ($form.find('input[name=_method]').length) {
        return $form.find('input[name=_method]').val();
    }

    return $form.attr('method')
}

export { getFormMethod };