
import 'flowbite';
import Alpine from 'alpinejs';
import { Chart, registerables } from 'chart.js';

// Chart.js empacotado localmente (sem CDN de terceiros / sem risco de supply-chain)
Chart.register(...registerables);
window.Chart = Chart;

window.Alpine = Alpine;

Alpine.start();
