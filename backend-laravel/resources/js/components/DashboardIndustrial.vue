<template>
  <div class="container">
    <h1>🏭 Dashboard Industrial</h1>

    <!-- STATUS -->
    <div class="status-card" :class="statusClass">
      <h2>Status: {{ latest.state }}</h2>
    </div>

    <!-- OEE -->
    <div class="cards">
      <div class="card">OEE: {{ oee.oee }}%</div>
      <div class="card">Disponibilidade: {{ oee.availability }}</div>
      <div class="card">Performance: {{ oee.performance }}</div>
      <div class="card">Qualidade: {{ oee.quality }}</div>
    </div>

    <!-- GRÁFICO -->
    <canvas id="oeeChart"></canvas>
  </div>
</template>

<script>
import axios from "axios";
import Chart from "chart.js/auto";

export default {
  data() {
    return {
      latest: {},
      oee: {},
      chart: null,
      chartData: []
    };
  },

  computed: {
    statusClass() {
      switch (this.latest.state) {
        case "RUNNING":
          return "running";
        case "STOPPED":
          return "stopped";
        case "FAULT":
          return "fault";
        default:
          return "";
      }
    }
  },

  methods: {
    async fetchData() {
      try {
        const latestRes = await axios.get("/api/machine-events/latest");
        const oeeRes = await axios.get("/api/machine-events/oee");

        this.latest = latestRes.data;
        this.oee = oeeRes.data;

        this.updateChart(oeeRes.data.oee);

      } catch (e) {
        console.error("Erro ao buscar dados", e);
      }
    },

    updateChart(value) {
      this.chartData.push(value);

      if (this.chartData.length > 20) {
        this.chartData.shift();
      }

      this.chart.data.labels = this.chartData.map((_, i) => i);
      this.chart.data.datasets[0].data = this.chartData;
      this.chart.update();
    }
  },

  mounted() {
    const ctx = document.getElementById("oeeChart");

    this.chart = new Chart(ctx, {
      type: "line",
      data: {
        labels: [],
        datasets: [{
          label: "OEE",
          data: [],
          borderWidth: 2
        }]
      }
    });

    this.fetchData();

    setInterval(() => {
      this.fetchData();
    }, 2000);
  }
};
</script>

<style>
.container {
  padding: 20px;
  font-family: Arial;
}

.status-card {
  padding: 20px;
  margin-bottom: 20px;
  color: white;
  font-weight: bold;
}

.running {
  background: green;
}

.stopped {
  background: orange;
}

.fault {
  background: red;
}

.cards {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.card {
  background: #eee;
  padding: 15px;
  border-radius: 5px;
}
</style>