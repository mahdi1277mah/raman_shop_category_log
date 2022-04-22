require('./bootstrap');

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

var Turbolinks = require("turbolinks")
Turbolinks.start()

let Swal = require('sweetalert2');

const Toast = Swal.mixin({
    toast: true,
    position: 'bottom',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})

document.addEventListener('livewire:load', () => {
    Livewire.on('toast', (type, message) => {
        Toast.fire({
            icon: type,
            title: message
        })
    })
})
