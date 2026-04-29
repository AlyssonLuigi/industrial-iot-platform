<template>
  <div class="container">
    <h1 style="color: white !important;">🏭 Dashboard Industrial</h1>

    <!-- STATUS -->
    <div class="status-card" :class="statusClass">
      <h2>Status: {{ latest?.state || 'N/A' }}</h2>
    </div>

    <!-- OEE -->
    <div class="cards">
      <div class="card">OEE: {{ oee?.oee ? (oee.oee * 100).toFixed(2) : 0 }}%</div>
      <div class="card">Disponibilidade: {{ oee?.availability ? (oee.availability * 100).toFixed(2) : 0 }}%</div>
      <div class="card">Performance: {{ oee?.performance ? (oee.performance * 100).toFixed(2) : 0 }}%</div>
      <div class="card">Qualidade: {{ oee?.quality ? (oee.quality * 100).toFixed(2) : 0 }}%</div>
    </div>

    <!-- GRÁFICO -->
    <canvas ref="oeeChart"></canvas>
  </div>
</template>

<script>
import axios from "axios"
import Chart from "chart.js/auto"
import echo from "@/echo"
import { nextTick } from "vue"
import { markRaw } from "vue"

export default {
  data() {
    return {
      lastUpdate: 0,
      latest: {},
      oee: {},
      chart: null,
    }
  },

  computed: {
    statusClass() {
      switch (this.latest?.state) {
        case "RUNNING":
          return "running"
        case "STOPPED":
          return "stopped"
        case "FAULT":
          return "fault"
        default:
          return "unknown"
      }
    }
  },
  methods: {
    async fetchData() {
      try {
        const [latestRes, oeeRes] = await Promise.all([
          axios.get("/api/machine-events/latest"),
          axios.get("/api/machine-events/oee")
        ])

        this.latest = latestRes.data || {}
        this.oee = oeeRes.data || {}

      } catch (error) {
        console.error("Erro ao buscar dados:", error)
      }
    },

    initChart() {
      const canvas = this.$refs.oeeChart
      if (!canvas) return

      const ctx = canvas.getContext("2d")

      if (this.chart) {
        this.chart.destroy()
      }

      const chartInstance = new Chart(ctx, {
        type: "line",
        data: {
          labels: [],
          datasets: [
            {
              label: "OEE",
              data: [],
              borderColor: "#00ff9d",
              backgroundColor: "rgba(0,255,157,0.2)",
              borderWidth: 2,
              tension: 0.3,
              fill: true,
              pointRadius: 0
            }
          ]
        },
        options: {
          responsive: true,
          animation: false
        }
      })

      // 🔥 ESSA LINHA RESOLVE TUDO
      this.chart = markRaw(chartInstance)
    },

    updateChart(value) {
      const now = Date.now()

      if (now - this.lastUpdate < 300) return
      this.lastUpdate = now

      if (!this.chart) return

      const dataset = this.chart.data.datasets[0].data

      dataset.push(value)

      if (dataset.length > 20) dataset.shift()

      this.chart.data.labels = dataset.map((_, i) => i + 1)

      this.chart.update()
    },
  },

  
  async mounted() {
    await nextTick()

    this.initChart()
    this.fetchData()

    echo.channel("machine-events")
    .listen(".machine.updated", (e) => {

      this.oee = e.data
      this.latest = e.data

      const value = Number(e.data.oee)

      if (!isNaN(value)) {
        this.updateChart(value)
      }
    })
},

  beforeUnmount() {
    if (this.interval) {
      clearInterval(this.interval)
    }

    if (this.chart) {
      this.chart.destroy()
    }
  }
}
</script>

<style>
.container {
  padding: 20px;
  font-family: Arial;
}
canvas {
  height: 300px !important;
}
.status-card {
  padding: 20px;
  margin-bottom: 20px;
  color: white;
  font-weight: bold;
  border-radius: 8px;
}

.running {
  background: #2ecc71;
}

.stopped {
  background: #f39c12;
}

.fault {
  background: #e74c3c;
}

.unknown {
  background: #7f8c8d;
}

.cards {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.card {
  background: #f2f2f2;
  padding: 15px;
  border-radius: 8px;
  flex: 1;
  text-align: center;
}
.fault {
  background: #e74c3c;
  animation: blink 1s infinite;
}

@keyframes blink {
  50% { opacity: 0.3; }
}
.running {
  background: #2ecc71;
  box-shadow: 0 0 20px #2ecc71;
}
.card {
  background: #111;
  color: #00ff9d;
  font-family: monospace;
  border: 1px solid #1f2a38;
}
.container {
  background: #0b0f14;
  min-height: 100vh;
}
</style>