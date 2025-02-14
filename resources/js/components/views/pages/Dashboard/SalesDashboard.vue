<script>
import { Pie } from "vue-chartjs";

export default {
  extends: Pie,
  mounted() {
    this.gradient = this.$refs.canvas
      .getContext("2d")
      .createLinearGradient(0, 0, 0, 450);
    this.gradient2 = this.$refs.canvas
      .getContext("2d")
      .createLinearGradient(0, 0, 0, 450);
    this.getGraph();
  },
  methods:{
      async getGraph() {
          this.$store.commit('LOADER', true);
          try {
              const response = await API.dashboard.sales();
              const salesData = response.data;
              console.log(salesData)
              const chartData = {
                  labels: salesData.map(({ nombre_producto }) => nombre_producto),
                  datasets: [
                      {
                          backgroundColor: [
                              "#512DA8",
                              "#673AB7",
                              "#7E57C2",
                              "#9575CD",
                              "#B39DDB",
                          ],
                          data: salesData.map(({ total_vendido }) => total_vendido),
                      }
                  ]
              };

              this.renderChart(chartData, { responsive: true, maintainAspectRatio: false });
          } catch (error) {
              console.error("Error al obtener los datos del gráfico:", error);
              throw error;
          } finally {
              this.$store.commit('LOADER', false);
          }
      }
  }
};
</script>
