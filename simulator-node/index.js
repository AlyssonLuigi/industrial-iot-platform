const mqtt = require("mqtt");

const client = mqtt.connect("mqtt://localhost:1883");

let state = "RUNNING";
let productionCount = 0;
let goodCount = 0;
let rejectCount = 0;

const idealCycleTime = 2; // segundos por peça ideal

function getRandomState() {
    const rand = Math.random();

    if (rand < 0.7) return "RUNNING";
    if (rand < 0.85) return "STOPPED";
    if (rand < 0.95) return "SETUP";
    return "FAULT";
}

client.on("connect", () => {
    console.log("🚀 Simulador industrial iniciado");

    setInterval(() => {
        state = getRandomState();

        let produced = 0;
        let rejected = 0;

        if (state === "RUNNING") {
            produced = Math.floor(Math.random() * 5);

            for (let i = 0; i < produced; i++) {
                if (Math.random() < 0.9) {
                    goodCount++;
                } else {
                    rejectCount++;
                    rejected++;
                }
            }

            productionCount += produced;
        }

        const totalProduced = goodCount + rejectCount;

        // OEE cálculo simplificado
        const availability = ["RUNNING"].includes(state) ? 1 : 0.5;
        const performance = state === "RUNNING"
            ? Math.min(produced / idealCycleTime, 1)
            : 0;

        const quality = totalProduced > 0
            ? (goodCount / totalProduced)
            : 0;

        const oee = availability * performance * quality;

        const event = {
            machine_id: "M1",
            state: state,
            produced: produced,
            rejected: rejected,
            total_production: productionCount,
            good_count: goodCount,
            reject_count: rejectCount,
            temperature: Number((20 + Math.random() * 15).toFixed(2)), vibration: (Math.random() * 5).toFixed(2),
            availability: availability,
            performance: performance.toFixed(2),
            quality: quality.toFixed(2),
            oee: oee.toFixed(2),
            timestamp: new Date().toISOString()
        };

        client.publish("factory/machine/events", JSON.stringify(event));

        console.log("📡 Evento industrial:", event);

    }, 2000);
});