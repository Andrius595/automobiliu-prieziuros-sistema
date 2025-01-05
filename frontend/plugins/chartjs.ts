import {
    Chart as ChartJS,
    registerables,

} from 'chart.js'
import { Line } from 'vue-chartjs'


export default defineNuxtPlugin(nuxtApp => {
    ChartJS.register(...registerables)
    nuxtApp.vueApp.component('Line', Line)
})
