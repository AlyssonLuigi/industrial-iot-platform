import Echo from "laravel-echo"
import Pusher from "pusher-js"

window.Pusher = Pusher

const echo = new Echo({
  broadcaster: "reverb",
  key: "local",
  wsHost: window.location.hostname,
  wsPort: 8080,
  forceTLS: false,
  enabledTransports: ["ws", "wss"],
})

export default echo