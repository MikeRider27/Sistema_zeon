import './bootstrap';

import Alpine from 'alpinejs';
import { createApp } from 'vue';
import TableBoard from './components/TableBoard.vue';
import OrderTaker from './components/OrderTaker.vue';
import KitchenDisplay from './components/KitchenDisplay.vue';

window.Alpine = Alpine;
Alpine.start();

const mounts = [
    ['table-board', TableBoard],
    ['order-taker', OrderTaker],
    ['kitchen-display', KitchenDisplay],
];

for (const [id, component] of mounts) {
    const el = document.getElementById(id);

    if (el) {
        const props = el.dataset.props ? JSON.parse(el.dataset.props) : {};
        createApp(component, props).mount(el);
    }
}
