import Chart from 'chart.js/auto';
import { createIcons, icons } from 'lucide';

window.Chart = Chart;
window.createIcons = createIcons;
window.lucideIcons = icons;

document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});

document.addEventListener('livewire:navigated', () => {
    createIcons({ icons });
});

document.addEventListener('icons:render', () => {
    createIcons({ icons });
});
