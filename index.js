// Dependencies
const morgan = require("morgan");
const express = require ("express");
const app = express();

// Routes
const list = require("./routes/listado");
const user = require("./routes/user");

// Middleware
const auth = require("./middleware/auth")
const notFound = require("./middleware/notFound")
const cors = require("./middleware/cors")

app.use(morgan("dev"))
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// CORS middleware (apply early)
app.use(cors);

app.get("/", (req, res) => {
  return res.status(200).json({ message: "API is running" });
});

// Public routes
app.use("/user", user);

// Protected routes
app.use(auth);
app.use("/listado", list);

app.use(notFound);

app.listen(process.env.PORT || 5000, () =>{
    console.log("Server is running...");
});