import './assets/main.css'

import { createApp } from 'vue'


import App from './App.vue'
import router from './router'


import PrimeVue from 'primevue/config';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Menubar from 'primevue/menubar';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import ToastService from 'primevue/toastservice';
import Tag from 'primevue/tag';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';   // optional
import Row from 'primevue/row';                   // optional


import "primevue/resources/themes/aura-light-green/theme.css";
import 'primeflex/primeflex.css';
import 'primeicons/primeicons.css';


const app = createApp(App)
app.use(PrimeVue);
app.use(ToastService);

app.component('Button', Button);
app.component('Card', Card);
app.component('Menubar', Menubar);
app.component('InputText', InputText);
app.component('Textarea', Textarea);
app.component('DataTable', DataTable);
app.component('Column', Column);
app.component('ColumnGroup', ColumnGroup);
app.component('Row', Row);
app.component('Tag', Tag);

app.use(router)

app.mount('#app')
