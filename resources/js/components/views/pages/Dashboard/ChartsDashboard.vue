<script>
import { Line } from "vue-chartjs";

export default {
  extends: Line,
  data() {
    return {
      gradient: null,
      gradient2: null
    };
  },
  mounted() {
    this.gradient = this.$refs.canvas
      .getContext("2d")
      .createLinearGradient(0, 0, 0, 450);

    this.gradient.addColorStop(0, "rgb(81,45,168)");
    this.gradient.addColorStop(0.5, "rgb(81,45,168,0.5)");
    this.gradient.addColorStop(1, "rgb(81,45,168,0.2)");

    this.gradient2 = this.$refs.canvas
      .getContext("2d")
      .createLinearGradient(0, 0, 0, 450);

    this.gradient2.addColorStop(0, "rgba(143, 143, 143, 0.5)");
    this.gradient2.addColorStop(0.5, "rgba(143, 143, 143, 0.25)");
    this.gradient2.addColorStop(1, "rgba(143, 143, 143, 0)");

    /*this.renderChart(
      {
        labels: [
          "January",
          "February",
          "March",
          "April",
          "May",
          "June",
          "July"
        ],
        datasets: [
          {
            label: "Ingresos",
            borderColor: "#7C4DFF",
            pointBackgroundColor: "white",
            borderWidth: 1,
            pointBorderColor: "white",
            backgroundColor: this.gradient,
            data: [40, 39, 10, 40, 39, 80, 40]
          },
        ]
      },
      { responsive: true, maintainAspectRatio: false }
    );*/

    this.getGraph();
  },
  methods:{
    async getGraph(){
      try{
          const data = await API.dashboard.graph();

          this.renderChart(
          {
            labels: data.data.map(({month})=>month),
            datasets: [
              {
                label: "Ingresos",
                borderColor: "#7C4DFF",
                pointBackgroundColor: "purple",
                borderWidth: 1,
                pointBorderColor: "white",
                backgroundColor: this.gradient,
                data: data.data.map(({incomes})=>incomes),
              },
              {
                label: "Egresos",
                borderColor: "#8f8f8f",
                pointBackgroundColor: "gray",
                borderWidth: 1,
                pointBorderColor: "white",
                backgroundColor: this.gradient2,
                data: data.data.map(({expenses})=>expenses),
              },
            ]
          },
          { responsive: true, maintainAspectRatio: false }
        );

          this.$store.commit('LOADER',false);
      }catch(error){
          console.error(error);
          this.$store.commit('LOADER',false);
      }
    },
  }
};
</script>
