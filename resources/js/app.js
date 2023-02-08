import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
let MessagesBox = document.getElementById('Messages');

const channel = Echo.channel('masoud');
channel.listen('NMN',(e)=>{
    MessagesBox += `<li>${e.username} : ${e.message}</li>`
});
