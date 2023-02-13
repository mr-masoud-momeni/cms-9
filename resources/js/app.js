import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const channel = Echo.channel('trades');
channel.listen('NewTrade', function(e){
    console.log(e.trade);
});
