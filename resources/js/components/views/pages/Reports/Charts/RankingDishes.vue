<script>
import { Bar, mixins } from "vue-chartjs";
const { reactiveProp } = mixins;

export default {
  extends: Bar,
  mixins: [
    reactiveProp
  ],
  props: [
    "chartData", 
    "options"
  ],
  mounted() {
    console.log('fdsdfsd')
    this.renderChart(this.chartData, this.options);
  }
};
</script>